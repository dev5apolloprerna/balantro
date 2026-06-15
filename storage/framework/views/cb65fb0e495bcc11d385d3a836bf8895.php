
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <form action="<?php echo e(route('supervisors.index')); ?>" method="GET"
            class="grid gap-3 sm:grid-cols-2 lg:flex lg:flex-wrap lg:items-end lg:gap-4 w-full">

            
            <div class="relative w-full lg:w-64">
                <input id="q" type="text" name="query" value="<?php echo e(request('query')); ?>"
                    placeholder="<?php echo e(__('Search by name or email...')); ?>"
                    class="block w-full rounded-lg border border-slate-300 bg-white pl-10 pr-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-yellow-500 focus:ring-2 focus:yellow-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100" />
                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M12.9 14.32a8 8 0 111.414-1.414l4.387 4.387a1 1 0 01-1.414 1.414l-4.387-4.387zM14 8a6 6 0 11-12 0 6 6 0 0112 0z"
                        clip-rule="evenodd" />
                </svg>
            </div>

            
            <?php if($user->role === \App\Models\User::ROLES['super_admin']): ?>
                <div class="w-full lg:w-56">
                    <select id="manager_id" name="manager_id"
                        class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                        <option value=""><?php echo e(__('Select Manager')); ?></option>
                        <?php $__currentLoopData = $managers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $manager): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($manager->id); ?>" <?php if(request('manager_id') == $manager->id): echo 'selected'; endif; ?>>
                                <?php echo e($manager->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php endif; ?>

            
            <div class="flex gap-3 w-full sm:w-auto">
                <button type="submit"
                    class="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:w-auto">
                    <?php echo e(__('Search')); ?>

                </button>

                <?php if(request('query') || request('manager_id')): ?>
                    <a href="<?php echo e(route('supervisors.index')); ?>"
                        class="inline-flex w-full items-center justify-center rounded-lg border border-rose-300 bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-700 hover:bg-rose-100 focus:outline-none focus:ring-2 focus:ring-rose-400 sm:w-auto">
                        <?php echo e(__('Reset')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div
        class="overflow-hidden rounded-xl border border-neutral-200 shadow-sm dark:border-neutral-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-50/80 dark:bg-neutral-800/60">
                    <tr class="text-left text-sm font-semibold text-neutral-700 dark:text-neutral-200">
                        <th class="px-2 py-1">Name</th>
                        <th class="px-2 py-1">Email</th>
                        <th class="px-2 py-1">Managers</th>
                        <th class="px-2 py-1">Groups</th>
                        <th class="px-2 py-1 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                    <?php if($supervisors->count()): ?>
                        <?php $__currentLoopData = $supervisors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supervisor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('admin.supervisors.supervisor_row', ['supervisor' => $supervisor], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center text-neutral-500 dark:text-neutral-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mb-3 h-10 w-10" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor">
                                        <path d="M21 21l-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14Z"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="text-base font-medium">No managers found</p>
                                    <p class="text-sm">Click “New Manager” to create one.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\supervisors\supervisor_table.blade.php ENDPATH**/ ?>