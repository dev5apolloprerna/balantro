<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

// ✅ add these imports
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Models\UserDevice;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function userParams(Request $request)
    {
        return $request->only(['name', 'email']);
    }

    protected function generatePassword()
    {
        $chars = array_merge(
            range('a', 'z'),
            range('A', 'Z'),
            range(0, 9),
            str_split('!@#$%^&*()_+-=[]{};\':"\\|,.<>/?')
        );

        do {
            $password = '';
            for ($i = 0; $i < 12; $i++) {
                $password .= $chars[array_rand($chars)];
            }
        } while (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[ \]{};\':"\\|,.<>\/?]).{8,}$/', $password));

        return $password;
    }

    protected function createUser(string $userClass, Request $request)
    {
        $user = $this->buildUser($userClass, $request);

        if ($user->save()) {

            if ($request->filled('fcm_token')) {
                UserDevice::updateOrCreate(
                    [
                        'device_token' => $request->fcm_token,
                    ],
                    [
                        'user_id'      => $user->id,
                        'device_type'  => $request->device_type ?? 'pc',
                        'browser_name' => $request->browser_name,
                        'os_name'      => $request->os_name,
                        'is_active'    => true,
                    ]
                );
            }
            $this->sendUserNotifications($user);
            $this->setFlashMessage($userClass);

            if ($request->wantsJson()) {
                return response()->json(['success' => true], 201);
            }

            // For Turbo Stream-like behavior in Laravel, you might use Livewire or Inertia
            // This is a simplified approach
            //return $this->turboStreamUpdates($userClass, $request);
            return redirect()->back();
        }

        return $this->renderUserErrors($user, $request);
    }

    private function authorizeSuperAdmin()
    {
        if (!Auth::user()->isSuperAdmin()) {
            return redirect('/')->with('error', __('admin.base.flash.authorize_super_admin_alert'));
        }
    }

    private function buildUser($userClass, Request $request)
    {
        $user = new $userClass();
        // Either assign explicitly…
        $user->name  = $request->input('name');
        $user->email = $request->input('email');
        $user->password = $request->input('password'); // already hashed in controller
        $user->type  = $request->input('type');        // e.g. 'manager' or int if that’s your schema
        $user->role  = $request->input('role');        // e.g. 2
        $user->token  = $request->input('fcm_token') ?: $request->input('token');
        $user->device = $request->input('device');
        $user->origin = $request->input('origin');
        $user->is_active = true;
        $user->confirmation_token = $request->input('confirmation_token'); // ← IMPORTANT

        return $user;
    }

    private function sendUserNotifications($user)
    {
        try {
            Mail::to($user->email)->send(new \App\Mail\WelcomeEmail($user));
            Password::sendResetLink(['email' => $user->email]);
        } catch (\Throwable $e) {
            // Log the exception so you can debug later
            Log::error('Failed to send user notifications', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'error'   => $e->getMessage(),
            ]);

            // Optionally: you can flash a warning or silently ignore
            // session()->flash('warning', 'User created but email could not be sent.');
        }
    }

    private function setFlashMessage($userClass)
    {
        $roleName = Str::snake(class_basename($userClass));
        $roleTitle = Str::title($roleName);

        session()->flash(
            'notice',
            trans(
                "admin.{$roleName}.user_create_msg",
                ['default' => trans("admin.user_create_msg", ['role' => $roleTitle])]
            )
        );
    }

    private function turboStreamUpdates($userClass, Request $request)
    {
        $response = [];

        // Add flash messages to response
        $response[] = view('shared.flash_messages')->render();

        switch (class_basename($userClass)) {
            case 'Manager':
                $managers = $userClass::with('groups')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
                $response[] = view('admin.managers.manager_table', ['managers' => $managers])->render();
                break;

            case 'Supervisor':
                $supervisors = $userClass::with(['groups', 'managers'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
                $response[] = view('admin.supervisors.supervisor_table', ['supervisors' => $supervisors])->render();
                break;

            case 'DataEntryOperator':
                $dataEntryOperators = $userClass::with(['groups', 'managers', 'supervisors'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
                $response[] = view('admin.data_entry_operators.data_entry_operator_table', ['data_entry_operators' => $dataEntryOperators])->render();
                break;
        }

        if ($request->wantsJson()) {
            return response()->json(['content' => implode('', $response)]);
        }

        return back()->with('turbo_stream', $response);
    }

    private function renderUserErrors($user, Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(['errors' => $user->errors()->messages()], 422);
        }

        return back()->withErrors($user->errors());
    }
}
