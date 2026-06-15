<p>{{ __('Hello :email!', ['email' => $user->email]) }}</p>

<p>{{ __('Your account has been locked due to an excessive number of unsuccessful sign in attempts.') }}</p>

<p>{{ __('Click the link below to unlock your account:') }}</p>

<p><a href="{{ route('unlock', ['token' => $token]) }}">{{ __('Unlock my account') }}</a></p>