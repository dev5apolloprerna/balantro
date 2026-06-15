<section class="bg-white dark:bg-[rgb(39,49,66)] flex flex-wrap min-h-[100vh]">
  <div class="lg:w-1/2 lg:block hidden">
    <div class="flex items-center flex-col h-full justify-center">
      <img src="<?php echo e(asset('theme/auth-img.png')); ?>" alt="Auth Image" class="max-w-full h-auto">
    </div>
  </div>
  <div class="font-sans w-full lg:w-1/2 py-8 px-4 sm:px-6 flex flex-col justify-center">
    <div class="w-full max-w-md mx-auto lg:max-w-[464px] px-4 sm:px-6">
      <div class="text-center lg:text-left">
        <a href="" class="mb-6 block max-w-[200px] sm:max-w-[290px] mx-auto lg:mx-0">
          <img src="<?php echo e(asset('light-logo.svg')); ?>" alt="light-logo" class="w-full light-logo block dark:hidden">
          <img src="<?php echo e(asset('dark-logo.svg')); ?>" alt="dark-logo" class="w-full dark-logo hidden dark:block">
        </a>
        <h6 class="mb-3"><?php echo e(__('Resend Confirmation Instructions')); ?></h6>
        <p class="mb-6 text-secondary-light text-base sm:text-lg"><?php echo e(__('Enter your email to resend confirmation instructions')); ?></p>
        <?php if(session('alert')): ?>
          <div class="mb-4 flex items-start gap-3 w-full rounded-lg px-4 py-3 text-base sm:text-lg text-red-600 dark:text-red-400">
            <div class="pt-0.5">
              <svg class="w-5 h-5 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
              </svg>
            </div>
            <div class="flex-1 leading-relaxed">
              <?php echo e(session('alert')); ?>

            </div>
          </div>
        <?php endif; ?>
      </div>
      
      <form method="POST" action="<?php echo e(route('verification.resend')); ?>">
        <?php echo csrf_field(); ?>
        <div class="relative mb-3 sm:mb-4">
          <div class="icon-field relative">
            <span class="absolute start-4 top-1/2 -translate-y-1/2 flex text-xl text-neutral-500 dark:text-white h-[26px] items-center pointer-events-none">
              <iconify-icon icon="mage:email" class="flex items-center"></iconify-icon>
            </span>
            <input id="email" type="email" name="email" 
                   value="<?php echo e(old('email', $user->pending_reconfirmation ? $user->unconfirmed_email : $user->email)); ?>"
                   required autocomplete="email" autofocus
                   placeholder="<?php echo e(__('Email')); ?>"
                   class="form-control h-[48px] sm:h-[55px] ps-11 border border-neutral-300 bg-custom-input dark:bg-dark-2 rounded-lg w-full text-sm sm:text-base">
          </div>
          <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <p class="form-error text-xs sm:text-sm text-red-600 mt-1"><?php echo e(__('Email')); ?> <?php echo e($message); ?></p>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        
        <button type="submit" class="btn btn-primary justify-center h-11 sm:h-13 w-full text-sm sm:mt-2 cursor-pointer">
          <?php echo e(__('Resend Confirmation Instructions')); ?>

        </button>
        
        <div class="mt-5 sm:mt-6 text-center">
          <a href="<?php echo e(route('login')); ?>" class="text-primary-600 font-medium hover:underline text-sm sm:text-base">
            <?php echo e(__('Back to Sign In')); ?>

          </a>
        </div>
      </form>
    </div>
  </div>
</section><?php /**PATH D:\xampp\htdocs\balantro\resources\views\users\confirmations\new.blade.php ENDPATH**/ ?>