<div class="space-y-6">
    <!-- 🔍 Filter Form -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <form action="<?php echo e(route('clients.index')); ?>" method="GET"
            class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-[1fr,200px,200px,200px,auto,auto] w-full">

            <!-- Search -->
            <div class="relative">
                <label for="q" class="sr-only"><?php echo e(__('Search by name or email...')); ?></label>
                <input id="q" type="text" name="query" value="<?php echo e(request('query')); ?>"
                    placeholder="<?php echo e(__('Search by name or email...')); ?>"
                    class="block w-full rounded-lg border border-slate-300 bg-white pl-10 pr-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100" />
                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M12.9 14.32a8 8 0 111.414-1.414l4.387 4.387a1 1 0 01-1.414 1.414l-4.387-4.387zM14 8a6 6 0 11-12 0 6 6 0 0112 0z"
                        clip-rule="evenodd" />
                </svg>
            </div>

            <!-- Manager -->
            <?php if($user->role === \App\Models\User::ROLES['super_admin'] || $user->role === \App\Models\User::ROLES['supervisor']): ?>
                <div class="relative">
                    <select name="manager_id"
                        class="h-11 w-full cursor-pointer rounded-lg border border-neutral-300 bg-white px-4 text-[16px] text-neutral-900 outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100">
                        <option value=""><?php echo e(__('Select Manager')); ?></option>
                        <?php $__currentLoopData = $managers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $manager): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($manager->id); ?>" <?php if(request('manager_id') == $manager->id): echo 'selected'; endif; ?>>
                                <?php echo e($manager->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php endif; ?>

            <!-- Supervisor -->
            <?php if($user->role === \App\Models\User::ROLES['super_admin'] || $user->role === \App\Models\User::ROLES['manager']): ?>
                <div class="relative">
                    <select name="supervisor_id"
                        class="h-11 w-full cursor-pointer rounded-lg border border-neutral-300 bg-white px-4 text-[16px] text-neutral-900 outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100">
                        <option value=""><?php echo e(__('Select Supervisor')); ?></option>
                        <?php $__currentLoopData = $supervisors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supervisor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($supervisor->id); ?>" <?php if(request('supervisor_id') == $supervisor->id): echo 'selected'; endif; ?>>
                                <?php echo e($supervisor->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php endif; ?>

            <!-- DEO -->
            <div class="relative">
                <select name="data_entry_operator_id"
                    class="h-11 w-full cursor-pointer rounded-lg border border-neutral-300 bg-white px-4 text-[16px] text-neutral-900 outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100">
                    <option value=""><?php echo e(__('Select Data Entry Operator')); ?></option>
                    <?php $__currentLoopData = $data_entry_operators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($deo->id); ?>" <?php if(request('data_entry_operator_id') == $deo->id): echo 'selected'; endif; ?>>
                            <?php echo e($deo->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Status -->
            <div class="relative">
                <select name="status"
                    class="h-11 w-full cursor-pointer rounded-lg border border-neutral-300 bg-white px-4 text-[16px] text-neutral-900 outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100">
                    <option value=""><?php echo e(__('Select Status')); ?></option>
                    <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>Active</option>
                    <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>Inactive</option>
                </select>
            </div>

            <!-- Search / Reset Buttons -->
            <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 mt-1 sm:mt-0">
                <button type="submit"
                    class="inline-flex flex-1 sm:flex-none items-center justify-center rounded-lg bg-primary-600 px-4 py-2 text-[16px] font-semibold text-white transition hover:bg-primary-700 cursor-pointer">
                    <?php echo e(__('Search')); ?>

                </button>

                <?php if(request()->hasAny(['query', 'manager_id', 'supervisor_id', 'data_entry_operator_id'])): ?>
                    <a href="<?php echo e(route('clients.index')); ?>"
                        class="inline-flex flex-1 sm:flex-none items-center justify-center rounded-lg border border-danger-600 bg-danger-50 px-4 py-2 text-[16px] font-semibold text-danger-600 transition hover:bg-danger-100 cursor-pointer dark:bg-danger-600/20 dark:hover:bg-danger-600/30">
                        <?php echo e(__('Reset')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- 🧾 Table Section -->
    <div
        class="overflow-hidden rounded-xl border border-neutral-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y text-sm">
                <thead class="bg-neutral-50/80 dark:bg-neutral-800/60">
                    <tr class="text-left font-semibold text-neutral-700 dark:text-neutral-200">
                        <th class="min-w-[160px] px-2 py-1"><?php echo e(__('Name')); ?></th>
                        <th class="min-w-[180px] px-2 py-1"><?php echo e(__('Email')); ?></th>
                        <th class="min-w-[180px] px-2 py-1"><?php echo e(__('Managers')); ?></th>
                        <th class="min-w-[180px] px-2 py-1"><?php echo e(__('Supervisors')); ?></th>
                        <th class="min-w-[200px] px-2 py-1"><?php echo e(__('Data Entry Operators')); ?></th>
                        <th class="min-w-[160px] px-2 py-1"><?php echo e(__('Groups')); ?></th>
                        <th class="min-w-[100px] px-2 py-1"><?php echo e(__('Status')); ?></th>
                        <th class="min-w-[120px] px-2 py-1 text-center"><?php echo e(__('Actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php if($clients->count()): ?>
                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('admin.clients.client_row', ['client' => $client], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-neutral-500 dark:text-neutral-400">
                                <div class="flex flex-col items-center justify-center">
                                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'heroicons-outline-document-magnifying-glass','class' => 'mb-3 h-10 w-10 text-neutral-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'heroicons-outline-document-magnifying-glass','class' => 'mb-3 h-10 w-10 text-neutral-400']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                                    <!-- <p class="mb-1 text-lg font-medium">
                                        <?php echo e(__('admin.clients.table.no_clients_title')); ?></p>
                                    <p class="text-sm"><?php echo e(__('admin.clients.table.no_clients_description')); ?></p> -->
                                    <p class="text-base font-medium">No managers found</p>
                                    <p class="text-sm">Click “New Client” to create one.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/clients/client_table.blade.php ENDPATH**/ ?>