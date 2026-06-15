<tr>
    <td><?php echo e($client->name); ?></td>
    <td><?php echo e($client->email); ?></td>
    <td><?php echo e($client->supervisors->count() ? $client->supervisors->pluck('name')->join(', ') : '-'); ?></td>
    <td><?php echo e($client->managers->count() ? $client->managers->pluck('name')->join(', ') : '-'); ?></td>
</tr><?php /**PATH D:\xampp\htdocs\balantro\resources\views\data_entry_operators\clients\_client_row.blade.php ENDPATH**/ ?>