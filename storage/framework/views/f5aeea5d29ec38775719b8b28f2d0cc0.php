<div data-controller="confirm-delete">
  <?php echo $__env->make('data_entry_operators.clients.client_list', ['clients' => $clients], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <!-- MOVE modals OUTSIDE of turbo_frame -->
  <?php echo $__env->make('shared.confirm_delete_modal', ['resourceName' => 'client'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\data_entry_operators\clients\index.blade.php ENDPATH**/ ?>