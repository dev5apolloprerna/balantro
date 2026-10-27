<?php
// app/Http/Controllers/Auth/ForgotPasswordOtpController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordOtpMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordOtpController extends Controller
{
    use \App\Http\Concerns\ResponseConcern;
    // 1) Request OTP
    public function forgot_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email']
        ]);
        if ($validator->fails()) {
            // return $this->error($validator->errors()->all(), self::HTTP_STATUS_CODE_617);
            //return $this->error($validator->errors(), self::HTTP_STATUS_CODE_617);
            return response()->json([
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }
        $data = $request->all();
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            // For security, you can still say "OTP sent" but here we return 422 as you do.
            return $this->error(__("response_message.password.email_not_found"), self::HTTP_STATUS_CODE_422);
        }

        // Throttle: disallow frequent requests (e.g., 60s)
        $recent = DB::table('user_password_otps')
            ->where('email', $user->email)
            ->where('sent_at', '>=', now()->subSeconds(60))
            ->exists();

        if ($recent) {
            return $this->error('Please wait before requesting another OTP.', self::HTTP_STATUS_CODE_429 ?? 429);
        }

        $otp           = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);  // 6-digit numeric
        $hash          = Hash::make($otp);
        $expiresAt     = now()->addMinutes(10);

        // Delete any previous entries for this email
        DB::table('user_password_otps')->where('email', $user->email)->delete();

        DB::table('user_password_otps')->insert([
            'email'      => $user->email,
            'code_hash'  => $hash,
            'expires_at' => $expiresAt,
            'attempts'   => 0,
            'sent_at'    => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send email (do this queued in production)
        Mail::to($user->email)->send(new ForgotPasswordOtpMail($user->name ?? 'User', $otp, 10));

        return $this->success(__('response_message.password.otp_sent_success', ['email' => $user->email]));
    }

    // 2) Verify OTP and reset password
    public function verifyOtp(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'email'                 => ['required', 'email'],
            'otp'                   => ['required', 'digits:6'],
            //'password'              => ['required', 'min:8', 'confirmed'], // expects password_confirmation
        ]);
        if ($Validator->fails()) {
            // return $this->error($validator->errors()->all(), self::HTTP_STATUS_CODE_617);
            //return $this->error($validator->errors(), self::HTTP_STATUS_CODE_617);
            return response()->json([
                'errors' => $Validator->errors(),
                'status' => 422
            ], 422);
        }
        $data = $request->all();
        $row = DB::table('user_password_otps')
            ->where('email', $data['email'])
            ->latest('id')
            ->first();

        if (!$row) {
            return $this->error('OTP not found. Please request a new one.', self::HTTP_STATUS_CODE_422);
        }

        // Check expiry
        if (Carbon::parse($row->expires_at)->isPast()) {
            DB::table('user_password_otps')->where('id', $row->id)->delete();
            return $this->error('OTP expired. Please request a new one.', self::HTTP_STATUS_CODE_422);
        }

        // Limit attempts
        if ($row->attempts >= 5) {
            DB::table('user_password_otps')->where('id', $row->id)->delete();
            return $this->error('Too many invalid attempts. Please request a new OTP.', self::HTTP_STATUS_CODE_429 ?? 429);
        }

        // Validate OTP
        $valid = Hash::check($data['otp'], $row->code_hash);

        // Increment attempts if invalid
        if (!$valid) {
            DB::table('user_password_otps')->where('id', $row->id)->update([
                'attempts' => $row->attempts + 1,
                'updated_at' => now(),
            ]);
            return $this->error('Invalid OTP.', self::HTTP_STATUS_CODE_422);
        }

        // OTP ok → reset password
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            // (Shouldn't happen if we had a record)
            DB::table('user_password_otps')->where('id', $row->id)->delete();
            return $this->error(__("response_message.password.email_not_found"), self::HTTP_STATUS_CODE_422);
        }

        // $user->forceFill([
        //     'password' => Hash::make($data['password']),
        // ])->save();

        // Invalidate the used OTP
        DB::table('user_password_otps')->where('id', $row->id)->delete();

        // (Optional) Invalidate user sessions/tokens here

        return $this->success(__('response_message.password.otp_success'));
    }

    // 3) Optional: resend OTP (with throttle)
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email']
        ]);
        if ($validator->fails()) {
            // return $this->error($validator->errors()->all(), self::HTTP_STATUS_CODE_617);
            //return $this->error($validator->errors(), self::HTTP_STATUS_CODE_617);
            return response()->json([
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }
        $data = $request->all();
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            return $this->error(__("response_message.password.email_not_found"), 422);
        }

        // respect same 60s throttle
        $recent = DB::table('user_password_otps')
            ->where('email', $user->email)
            ->where('sent_at', '>=', now()->subSeconds(60))
            ->exists();
        if ($recent) {
            return $this->error('Please wait before requesting another OTP.', 429);
        }

        $otp       = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $hash      = Hash::make($otp);
        $expiresAt = now()->addMinutes(10);

        DB::table('user_password_otps')->where('email', $user->email)->delete();
        DB::table('user_password_otps')->insert([
            'email'      => $user->email,
            'code_hash'  => $hash,
            'expires_at' => $expiresAt,
            'attempts'   => 0,
            'sent_at'    => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Mail::to($user->email)->send(new ForgotPasswordOtpMail($user->name ?? 'User', $otp, 10));

        return $this->success(__('response_message.password.otp_sent_success', ['email' => $user->email]));
    }

    public function resetPassword(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'email'                 => ['required', 'email'],
            //'otp'                   => ['required', 'digits:6'],
            'password'              => ['required', 'min:8', 'confirmed'], // expects password_confirmation
        ]);
        if ($Validator->fails()) {
            return response()->json([
                'errors' => $Validator->errors(),
                'status' => 422
            ], 422);
        }
        $data = $request->all();
        // OTP ok → reset password
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            return $this->error(__("response_message.password.email_not_found"), self::HTTP_STATUS_CODE_422);
        }
        $user->forceFill([
            'password' => Hash::make($data['password']),
        ])->save();

        return $this->success(__('response_message.password.reset_success'));
    }
}
