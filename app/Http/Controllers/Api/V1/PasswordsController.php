<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordsController extends Controller
{
    use \App\Http\Concerns\ResponseConcern;

    public function forgot(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            $status = Password::sendResetLink($request->only('email'));

            if ($status === Password::RESET_LINK_SENT) {
                return $this->success(__("response_message.password.forgot_success", ['email' => $user->email]));
            }
        }

        return $this->error(__("response_message.password.email_not_found"), 422);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return $this->error(__("response_message.password.email_not_found"), 422);
            }

            // Optional: Add additional security checks here
            // For example, check if the user is active, not locked, etc.
            /*if (!$user->is_active) {
				return $this->error(__("response_message.password.account_inactive"), 422);
			}*/

            // Update the password
            $user->password = Hash::make($request->password);
            $user->save();

            // Optional: Clear any existing remember tokens
            // $user->setRememberToken(Str::random(60));
            // $user->save();

            return $this->success(__("response_message.password.update_success"));
        } catch (\Exception $e) {
            \Log::error('Password reset error: ' . $e->getMessage());
            return $this->error(__("response_message.password.update_failed"), 500);
        }
    }
}
