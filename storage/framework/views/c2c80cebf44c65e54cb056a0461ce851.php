<p><?php echo e(__('Hello :email!', ['email' => $email])); ?></p>

<?php if($user->unconfirmed_email): ?>
  <p><?php echo e(__("We're contacting you to notify you that your email is being changed to :email.", ['email' => $user->unconfirmed_email])); ?></p>
<?php else: ?>
  <p><?php echo e(__("We're contacting you to notify you that your email has been changed to :email.", ['email' => $user->email])); ?></p>
<?php endif; ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\users\mailer\email-changed.blade.php ENDPATH**/ ?>