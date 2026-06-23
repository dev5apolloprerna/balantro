
<!-- Header -->
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h6 class="text-lg font-semibold text-gray-800 dark:text-white">
            <?php echo e(__('Clients')); ?>

        </h6>

        
        <?php if(auth()->user()->can('clients.create') || $user->role === \App\Models\User::ROLES['super_admin']): ?>
                <button type="button" data-modal-open="client-form"
                    class="inline-flex items-center gap-2 rounded-lg border border-primary-600 bg-primary-600 px-2 py-1 font-semibold text-white transition hover:bg-primary-700 cursor-pointer">

                    
                    <span><?php echo e(__('New Client')); ?></span>
                </button>
        <?php endif; ?>
        
    </div>

    
    <?php echo $__env->make('admin.clients.modals.create', ['client' => $client ?? null], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Card -->
    <div class="grid grid-cols-1 lg:grid-cols-12">
        <div class="col-span-12">
            <div class="shadow rounded-2xl overflow-hidden">
                <div class="">
                    
                    <div id="supervisor-table">
                        <?php echo $__env->make('admin.clients.client_table', ['clients' => $clients], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


                        <!-- Table -->
                        

                        

                        <div class="border-t border-neutral-200 p-4 dark:border-neutral-700">
                            <?php echo e($clients->links()); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="deoDelete" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/60" onclick="closeModal('deoDelete')"></div>
    <div class="relative mx-auto mt-32 w-full max-w-md px-4">
        <div class="rounded-2xl bg-white p-6 shadow-xl dark:bg-slate-800">
            <form id="deoDeleteForm" method="POST"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Delete operator?</h3>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">This action cannot be undone.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button"
                        class="rounded-lg border border-slate-300 22 te1t-sm dark:border-slate-700"
                        onclick="closeModal('deoDelete')">Cancel</button>
                    <button
                        class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Mount modals ONCE for the page (not per row) -->


<?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/clients/client_list.blade.php ENDPATH**/ ?>