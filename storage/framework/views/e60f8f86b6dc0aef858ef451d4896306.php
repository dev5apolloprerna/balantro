<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('admin.clients.client_list', [
        'clients' => $clients,
        'data_entry_operators' => $dataEntryOperators,
        'managers' => $managers,
        'supervisors' => $supervisors,
        'groups' => $groups,
        'permissions' => $permissions,
        'mgrSupMap' => $mgrSupMap,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('admin.clients.modals.assign_users_plain', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('admin.clients.modals.assign_groups_plain', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('admin.clients.modals.assign_permissions_plain', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    

    <?php $__env->startPush('scripts'); ?>
        <script>
            window.CLIENT_ROUTES = {
                assignUsers: <?php echo json_encode(route('clients.assignUsers', ['client' => '__ID__']), 512) ?>,
                mgrSup: <?php echo json_encode(route('clients.managerSupervisors', ['manager' => '__ID__']), 512) ?>,
                supDeo: <?php echo json_encode(route('clients.supervisorDataEntryOperators', ['supervisor' => '__ID__']), 512) ?>,
                assignGroups: <?php echo json_encode(route('clients.assignGroups', ['client' => '__ID__']), 512) ?>,
                getGroups: <?php echo json_encode(route('clients.getGroups', ['client' => '__ID__']), 512) ?>,
                assignPermissions: <?php echo json_encode(route('clients.assignPermissions', ['client' => '__ID__']), 512) ?>,
                getPermissions: <?php echo json_encode(route('clients.getPermissions', ['client' => '__ID__']), 512) ?>,

                clientStore: <?php echo json_encode(route('clients.store'), 15, 512) ?>,
                clientUpdate: <?php echo json_encode(route('clients.update', ['client' => '__ID__']), 512) ?>,
                clientEdit: <?php echo json_encode(route('clients.edit', ['client' => '__ID__']), 512) ?>,
            };
        </script>
        <?php echo $__env->make('admin.clients.modals_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> 
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/clients/index.blade.php ENDPATH**/ ?>