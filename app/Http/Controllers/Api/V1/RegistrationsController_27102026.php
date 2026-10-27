<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegistrationsController extends Controller
{
    use \App\Http\Concerns\ResponseConcern;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            // 'password' => 'required|string|min:8|confirmed',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            // return $this->error($validator->errors()->all(), 617);
            return response()->json([
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => User::ROLES['client'],
            //'type' => 'Client',  // ✅ explicitly set
            'password' => Hash::make($request->password),
            'confirmation_token' => Str::uuid()
        ]);

        // Create profile
        $user->profile()->create([]);

        $token = auth()->login($user);

        return response()->json([
            'message' => 'Signed up successfully',
            'user' => $user,
            'token' => $token,
            'status' => 200
        ], 200);
    }
}
