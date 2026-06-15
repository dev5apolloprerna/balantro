<?php if($errors->any()): ?>
  <div id="error_explanation">
    <h2>
      <?php echo app('translator')->choice('errors.messages.not_saved', $errors->count(), [
          'resource' => strtolower(class_basename($model))
      ]); ?>
    </h2>
    <ul>
      <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><?php echo e($message); ?></li>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
  </div>
<?php endif; ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\users\shared\_error_messages.blade.php ENDPATH**/ ?>