@if (Route::currentRouteName() != 'login')
  <a href="{{ route('login') }}">Log in</a><br />
@endif

@if (Route::has('register') && Route::currentRouteName() != 'register')
  <a href="{{ route('register') }}">Sign up</a><br />
@endif

@if (Route::has('password.request') && 
     Route::currentRouteName() != 'password.request' && 
     Route::currentRouteName() != 'register')
  <a href="{{ route('password.request') }}">Forgot your password?</a><br />
@endif

@if (Route::has('verification.notice') && 
     Route::currentRouteName() != 'verification.notice')
  <a href="{{ route('verification.notice') }}">Didn't receive verification instructions?</a><br />
@endif

@if (Route::has('password.confirm') && 
     Route::currentRouteName() != 'password.confirm')
  <a href="{{ route('password.confirm') }}">Didn't receive unlock instructions?</a><br />
@endif

@foreach (config('auth.oauth_providers', []) as $provider)
  <a href="{{ route('oauth.login', ['provider' => $provider]) }}" 
     class="btn btn-{{ $provider }}">
    Sign in with {{ ucfirst($provider) }}
  </a><br />
@endforeach