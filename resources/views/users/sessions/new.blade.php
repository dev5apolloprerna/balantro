<section class="bg-white dark:bg-[rgb(39,49,66)] flex flex-wrap min-h-[100vh]">
  <div class="lg:w-1/2 lg:block hidden">
    <div class="flex items-center flex-col h-full justify-center">
      <img src="{{ asset('theme/auth-img.png') }}" alt="Auth Image" class="max-w-full h-auto">
    </div>
  </div>
  <div class="font-sans w-full lg:w-1/2 py-8 px-4 sm:px-6 flex flex-col justify-center">
    <div class="w-full max-w-md mx-auto lg:max-w-[464px] px-4 sm:px-6">
      <div class="text-center">
        <a href="" class="mb-6 block max-w-[200px] sm:max-w-[290px] mx-auto lg:mx-0">
          <img src="{{ asset('light-logo.svg') }}" alt="light-logo" class="w-full light-logo block dark:hidden">
          <img src="{{ asset('dark-logo.svg') }}" alt="dark-logo" class="w-full dark-logo hidden dark:block">
        </a>
        <p class="mb-6 text-secondary-light text-base sm:text-lg">{{ __('Sign In') }}</p>
        @if ($errors->any())
          <div class="mb-4 flex items-start gap-3 w-full rounded-lg px-4 py-3 text-base sm:text-lg text-red-600 dark:text-red-400">
            <div class="pt-0.5">
              <svg class="w-5 h-5 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
              </svg>
            </div>
            <div class="flex-1 leading-relaxed">
              {{ $errors->first() }}
            </div>
          </div>
        @endif
      </div>
      
      <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="relative mb-4 sm:mb-6">
          <div class="icon-field relative">
            <span class="absolute start-4 top-1/2 -translate-y-1/2 flex text-xl text-neutral-500 dark:text-white h-[26px] items-center pointer-events-none">
              <iconify-icon icon="mage:email" class="flex items-center"></iconify-icon>
            </span>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="{{ __('Email') }}"
                   class="form-control h-[48px] sm:h-[55px] ps-11 border border-neutral-300 bg-custom-input dark:bg-dark-2 rounded-lg w-full text-sm sm:text-base">
          </div>
          @error('email')
            <p class="form-error text-xs sm:text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>
        
        <div class="mb-4 sm:mb-6 relative">
          <div class="icon-field relative mt-2">
            <span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none text-xl text-neutral-500 dark:text-white">
              <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
            </span>
            <input type="password" name="password" required autocomplete="current-password" placeholder="{{ __('Password') }}" id="your-password"
                   class="form-control h-[48px] sm:h-[56px] ps-11 pe-11 border border-neutral-300 bg-custom-input dark:bg-dark-2 rounded-lg w-full text-neutral-600 dark:text-white text-sm sm:text-base">
            <span class="toggle-password ri-eye-line cursor-pointer absolute end-4 top-1/2 -translate-y-1/2 text-secondary-light mt-[-2px]" data-toggle="#your-password"></span>
          </div>
          @error('password')
            <p class="form-error text-xs sm:text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>
        
        <div class="mt-5 sm:mt-7">
          <div class="flex justify-between items-center w-full gap-4">
            <div class="flex items-center shrink-0">
              <input type="checkbox" name="remember" id="remember" class="cursor-pointer form-check-input border border-neutral-300">
              <label for="remember" class="ps-2 text-neutral-600 dark:text-white text-sm sm:text-base whitespace-nowrap">{{ __('Remember Me') }}</label>
            </div>
            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}" class="text-primary-600 font-medium hover:underline text-sm sm:text-base whitespace-nowrap text-right">{{ __('Forgot Your Password?') }}</a>
            @endif
          </div>
        </div>
        
        <button type="submit" class="btn btn-primary justify-center h-11 sm:h-13 w-full text-sm mt-6 sm:mt-8 cursor-pointer">{{ __('Sign In') }}</button>
      </form>

      @if(false)
        <div class="mt-6 sm:mt-8 center-border-horizontal text-center relative before:absolute before:w-full before:h-[1px] before:top-1/2 before:-translate-y-1/2 before:bg-neutral-300 before:start-0">
          <span class="bg-white dark:bg-dark-2 z-[2] relative px-4 text-sm sm:text-base">Or sign in with</span>
        </div>
        <div class="mt-6 sm:mt-8 flex items-center gap-3">
          <button type="button"
                  class="font-semibold text-neutral-600 dark:text-neutral-200 py-3 sm:py-4 px-6 w-full border rounded-xl text-sm sm:text-base flex items-center justify-center gap-3 line-height-1 hover:bg-primary-50">
            <iconify-icon icon="ic:baseline-facebook" class="text-primary-600 text-xl line-height-1"></iconify-icon>
            Facebook
          </button>
          <button type="button"
                  class="font-semibold text-neutral-600 dark:text-neutral-200 py-3 sm:py-4 px-6 w-full border rounded-xl text-sm sm:text-base flex items-center justify-center gap-3 line-height-1 hover:bg-primary-50">
            <iconify-icon icon="logos:google-icon" class="text-primary-600 text-xl line-height-1"></iconify-icon>
            Google
          </button>
        </div>
      @endif

      <div class="mt-6 sm:mt-8 text-center text-sm">
        <p class="mb-0">
          {{ __('Don\'t have an account?') }}
          @if (Route::has('register'))
            <a href="{{ route('register') }}" class="text-primary-600 font-semibold hover:underline">{{ __('Sign Up') }}</a>
          @endif
        </p>
      </div>
    </div>
  </div>
</section>

<script>
  // =========== Password Show Hide Js Start ===========
  function initializePasswordToggle(toggleSelector) {
    $(toggleSelector).on('click', function () {
      $(this).toggleClass("ri-eye-off-line");
      var input = $($(this).attr("data-toggle"));
      if (input.attr("type") === "password") {
        input.attr("type", "text");
      } else {
        input.attr("type", "password");
      }
    });
  }

  // Call the function
  initializePasswordToggle('.toggle-password');
  // =========== Password Show Hide Js End ===========
</script>