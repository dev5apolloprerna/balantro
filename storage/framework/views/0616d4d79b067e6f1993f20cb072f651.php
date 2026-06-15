<div class="modal fade" id="permissionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="permissionsForm" method="POST" class="modal-content">
            <?php echo csrf_field(); ?>
            <div class="modal-header">
                <h5 class="modal-title">Assign Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="permissions_list">
                    <?php $__currentLoopData = $permissions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="form-check">
                            <input type="checkbox" name="permission_ids[]" class="form-check-input"
                                value="<?php echo e($permission->id); ?>">
                            <label class="form-check-label">
                                <?php echo e($permission->name); ?> (<?php echo e($permission->action); ?> - <?php echo e($permission->subject); ?>)
                            </label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\groups\permissions_modal.blade.php ENDPATH**/ ?>