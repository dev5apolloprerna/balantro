<p>{{ __('Hello :email!', ['email' => $email]) }}</p>

@if ($user->unconfirmed_email)
  <p>{{ __("We're contacting you to notify you that your email is being changed to :email.", ['email' => $user->unconfirmed_email]) }}</p>
@else
  <p>{{ __("We're contacting you to notify you that your email has been changed to :email.", ['email' => $user->email]) }}</p>
@endif