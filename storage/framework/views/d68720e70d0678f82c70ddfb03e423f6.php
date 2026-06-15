<?php $__empty_1 = true; $__currentLoopData = $data_entry_operators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php
        $managerId = $deo->managers->pluck('id')->first();
        $supervisorId = $deo->supervisors->pluck('id')->first();
        $groupIds = $deo->groups->pluck('id')->values();
    ?>
    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30">
        <td class="px-2 py-1 font-medium text-slate-900 dark:text-slate-100"><?php echo e($deo->name); ?></td>
        <td class="px-2 py-1 text-slate-700 dark:text-slate-200"><?php echo e($deo->email); ?></td>
        <td class="px-2 py-1 text-slate-700 dark:text-slate-200"><?php echo e($deo->managers->pluck('name')->join(', ') ?: '—'); ?>

        </td>
        <td class="px-2 py-1 text-slate-700 dark:text-slate-200">
            <?php echo e($deo->supervisors->pluck('name')->join(', ') ?: '—'); ?></td>
        <td class="px-2 py-1">
            <?php $__empty_2 = true; $__currentLoopData = $deo->groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                <span
                    class="mr-1 inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 dark:bg-slate-700/60 dark:text-slate-200"><?php echo e($g->name); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                <span class="text-slate-400">—</span>
            <?php endif; ?>
        </td>
        <?php if($user->role === \App\Models\User::ROLES['super_admin'] || $user->role === \App\Models\User::ROLES['manager']): ?>
            <td class="px-2 py-1 text-right">

                <div class="inline-flex items-center gap-2">
                    <?php if($user->role === \App\Models\User::ROLES['super_admin']): ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('data_entry_operators.assign-groups')): ?>
                            <button type="button" title="Assign Groups"
                                class="rounded-full bg-amber-100 p-2 text-amber-700 ring-1 ring-inset ring-amber-200 hover:bg-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:ring-amber-800"
                                data-open-assign-groups data-id="<?php echo e($deo->id); ?>"
                                data-group-ids='<?php echo json_encode($groupIds, 15, 512) ?>'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M12 5a3.5 3.5 0 0 0-3.5 3.5A3.5 3.5 0 0 0 12 12a3.5 3.5 0 0 0 3.5-3.5A3.5 3.5 0 0 0 12 5m0 2a1.5 1.5 0 0 1 1.5 1.5A1.5 1.5 0 0 1 12 10a1.5 1.5 0 0 1-1.5-1.5A1.5 1.5 0 0 1 12 7M5.5 8A2.5 2.5 0 0 0 3 10.5c0 .94.53 1.75 1.29 2.18c.36.2.77.32 1.21.32s.85-.12 1.21-.32c.37-.21.68-.51.91-.87A5.42 5.42 0 0 1 6.5 8.5v-.28c-.3-.14-.64-.22-1-.22m13 0c-.36 0-.7.08-1 .22v.28c0 1.2-.39 2.36-1.12 3.31c.12.19.25.34.4.49a2.48 2.48 0 0 0 1.72.7c.44 0 .85-.12 1.21-.32c.76-.43 1.29-1.24 1.29-2.18A2.5 2.5 0 0 0 18.5 8M12 14c-2.34 0-7 1.17-7 3.5V19h14v-1.5c0-2.33-4.66-3.5-7-3.5m-7.29.55C2.78 14.78 0 15.76 0 17.5V19h3v-1.93c0-1.01.69-1.85 1.71-2.52m14.58 0c1.02.67 1.71 1.51 1.71 2.52V19h3v-1.5c0-1.74-2.78-2.72-4.71-2.95M12 16c1.53 0 3.24.5 4.23 1H7.77c.99-.5 2.7-1 4.23-1">
                                    </path>
                                </svg>

                            </button>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('data_entry_operators.assign-permissions')): ?>
                            <button type="button" title="Assign Permissions"
                                class="rounded-full bg-violet-100 p-2 text-violet-700 ring-1 ring-inset ring-violet-200 hover:bg-violet-200 dark:bg-violet-900/30 dark:text-violet-300 dark:ring-violet-800"
                                data-open-assign-permissions data-id="<?php echo e($deo->id); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12c5.16-1.26 9-6.45 9-12V5zm0 2.18l7 3.12v4.92c0 1.7-.5 3.43-1.35 4.95C16 14.94 13.26 14.5 12 14.5s-4 .44-5.65 1.67C5.5 14.65 5 12.92 5 11.22V6.3zM12 6a3.5 3.5 0 0 0-3.5 3.5A3.5 3.5 0 0 0 12 13a3.5 3.5 0 0 0 3.5-3.5A3.5 3.5 0 0 0 12 6m0 2a1.5 1.5 0 0 1 1.5 1.5A1.5 1.5 0 0 1 12 11a1.5 1.5 0 0 1-1.5-1.5A1.5 1.5 0 0 1 12 8m0 8.5c1.57 0 3.64.61 4.53 1.34C15.29 19.38 13.7 20.55 12 21c-1.7-.45-3.29-1.62-4.53-3.16c.9-.73 2.96-1.34 4.53-1.34">
                                    </path>
                                </svg>
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('data_entry_operators.manager-supervisors')): ?>
                        <button type="button" title="Assign Manager & Supervisor"
                            class="rounded-full bg-indigo-100 p-2 text-indigo-700 ring-1 ring-inset ring-indigo-200 hover:bg-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300 dark:ring-indigo-800"
                            data-open-assign-owners data-id="<?php echo e($deo->id); ?>" data-manager-id="<?php echo e($managerId ?? ''); ?>"
                            data-supervisor-id="<?php echo e($supervisorId ?? ''); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 1 1-8 0a4 4 0 0 1 8 0M3 20a6 6 0 0 1 12 0v1H3z">
                                </path>
                            </svg>
                        </button>
                    <?php endif; ?>
                    <?php if($user->role === \App\Models\User::ROLES['super_admin']): ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('data_entry_operators.update')): ?>
                            <button type="button" title="Edit"
                                class="rounded-full bg-emerald-100 p-2 text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-800"
                                data-open-edit data-id="<?php echo e($deo->id); ?>" data-name="<?php echo e($deo->name); ?>"
                                data-email="<?php echo e($deo->email); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2">
                                        <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path
                                            d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z">
                                        </path>
                                    </g>
                                </svg>
                            </button>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('data_entry_operators.delete')): ?>
                            <button type="button" title="Delete"
                                class="rounded-full bg-rose-100 p-2 text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-200 dark:bg-rose-900/30 dark:text-rose-300 dark:ring-rose-800"
                                data-open-delete data-id="<?php echo e($deo->id); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                    <g fill="none">
                                        <path
                                            d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z">
                                        </path>
                                        <path fill="currentColor"
                                            d="M14.28 2a2 2 0 0 1 1.897 1.368L16.72 5H20a1 1 0 1 1 0 2l-.003.071l-.867 12.143A3 3 0 0 1 16.138 22H7.862a3 3 0 0 1-2.992-2.786L4.003 7.07L4 7a1 1 0 0 1 0-2h3.28l.543-1.632A2 2 0 0 1 9.721 2zm3.717 5H6.003l.862 12.071a1 1 0 0 0 .997.929h8.276a1 1 0 0 0 .997-.929zM10 10a1 1 0 0 1 .993.883L11 11v5a1 1 0 0 1-1.993.117L9 16v-5a1 1 0 0 1 1-1m4 0a1 1 0 0 1 1 1v5a1 1 0 1 1-2 0v-5a1 1 0 0 1 1-1m.28-6H9.72l-.333 1h5.226z">
                                        </path>
                                    </g>
                                </svg>
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </td>
        <?php endif; ?>
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <tr>
        <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-300">No data entry operators
            found.</td>
    </tr>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\data_entry_operators\data_entry_operator_row.blade.php ENDPATH**/ ?>