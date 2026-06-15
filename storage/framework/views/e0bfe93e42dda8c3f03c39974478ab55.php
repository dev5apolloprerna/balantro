<tr id="client_<?php echo e($client->id); ?>"
    class="hover:bg-neutral-50 dark:hover:bg-neutral-700/60"
    data-client-id="<?php echo e($client->id); ?>" data-client-mobile="<?php echo e($client->profile?->mobile_no); ?>"
    data-client-pan="<?php echo e($client->profile?->pan_no); ?>" data-client-gst="<?php echo e($client->profile?->gst_no); ?>"
    data-client-address="<?php echo e($client->profile?->address); ?>"
    data-client-business-type="<?php echo e($client->profile?->business_type); ?>">
    <td class="px-2 py-1 font-medium">
        <?php echo e($client->name); ?>

    </td>

    <td class="px-2 py-1 text-neutral-700 dark:text-neutral-300">
        <?php echo e($client->email); ?>

    </td>

    <td class="px-2 py-1  text-neutral-700 dark:text-neutral-300">
        <?php if($managers->count()): ?>
            <?php echo e($client->managers->pluck('name')->join(', ') ?: '-'); ?>

        <?php else: ?>
            <span
                class="inline-flex rounded-full bg-info-100 px-3 py-1 text-xs font-semibold text-info-800 dark:bg-info-900/40 dark:text-info-300">
                <?php echo e(__('admin.clients.table.no_managers')); ?>

            </span>
        <?php endif; ?>
    </td>

    <td class="px-2 py-1  text-neutral-700 dark:text-neutral-300">
        <?php if($supervisors->count()): ?>
            <?php echo e($client->supervisors->pluck('name')->join(', ') ?: '-'); ?>

        <?php else: ?>
            <span
                class="inline-flex rounded-full bg-info-100 px-3 py-1 text-xs font-semibold text-info-800 dark:bg-info-900/40 dark:text-info-300">
                <?php echo e(__('admin.clients.table.no_supervisors')); ?>

            </span>
        <?php endif; ?>
    </td>

    <td class="px-2 py-1  text-neutral-700 dark:text-neutral-300">
        <?php if($dataEntryOperators->count()): ?>
            <?php echo e($client->dataEntryOperators->pluck('name')->join(', ') ?: '-'); ?>

        <?php else: ?>
            <span
                class="inline-flex rounded-full bg-info-100 px-3 py-1 text-xs font-semibold text-info-800 dark:bg-info-900/40 dark:text-info-300">
                <?php echo e(__('admin.clients.table.no_data_entry_operators')); ?>

            </span>
        <?php endif; ?>
    </td>

    <td class="px-2 py-1  text-neutral-700 dark:text-neutral-300">
        
        <?php $__empty_1 = true; $__currentLoopData = $client->groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <span
                class="mr-1 inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 dark:bg-slate-700/60 dark:text-slate-200"><?php echo e($g->name); ?></span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <span class="text-slate-400">—</span>
        <?php endif; ?>
    </td>
    <td class="px-2 py-1 ">
        <?php if($user->role === \App\Models\User::ROLES['super_admin']): ?>
            <form method="POST" action="<?php echo e(route('clients.toggleStatus', $client)); ?>"
                onsubmit="return confirm('Are you sure you want to <?php echo e($client->is_active ? 'deactivate' : 'activate'); ?> this client?')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>
                <button type="submit"
                    class="rounded-full p-2 ring-1 ring-inset transition
                 <?php echo e($client->is_active
                     ? 'bg-emerald-100 text-emerald-700 ring-emerald-200 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-800'
                     : 'bg-rose-100 text-rose-700 ring-rose-200 hover:bg-rose-200 dark:bg-rose-900/30 dark:text-rose-300 dark:ring-rose-800'); ?>"
                    title="<?php echo e($client->is_active ? 'Mark Inactive' : 'Mark Active'); ?>">

                    <?php if($client->is_active): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M12 6h5a6 6 0 1 1 0 12h-5A6 6 0 0 1 12 6m-5 2a4 4 0 1 0 0 8h5a4 4 0 1 0 0-8z" />
                        </svg>
                    <?php else: ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M7 8a4 4 0 1 0 0 8h5a4 4 0 1 0 0-8zm0-2h5a6 6 0 0 1 0 12H7A6 6 0 0 1 7 6" />
                        </svg>
                    <?php endif; ?>
                </button>
            </form>

            
        <?php else: ?>
            <?php if($client->is_active): ?>
                <span
                    class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                    Active
                </span>
            <?php else: ?>
                <span
                    class="inline-flex rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-semibold text-rose-700 dark:bg-rose-900/30 dark:text-rose-300">
                    Inactive
                </span>
            <?php endif; ?>
        <?php endif; ?>
    </td>
    <td class="px-2 py-1 align-middle">
        <div class="flex items-center justify-center gap-2">
            <?php if($user->role === \App\Models\User::ROLES['super_admin']): ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('clients.assign-groups')): ?>
                    <button type="button"
                        class="rounded-full bg-amber-100 p-2 text-amber-700 ring-1 ring-inset ring-amber-200 hover:bg-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:ring-amber-800"
                        title="Assign Groups" data-modal-open="assign-groups" data-client-id="<?php echo e($client->id); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M12 5a3.5 3.5 0 0 0-3.5 3.5A3.5 3.5 0 0 0 12 12a3.5 3.5 0 0 0 3.5-3.5A3.5 3.5 0 0 0 12 5m0 2a1.5 1.5 0 0 1 1.5 1.5A1.5 1.5 0 0 1 12 10a1.5 1.5 0 0 1-1.5-1.5A1.5 1.5 0 0 1 12 7M5.5 8A2.5 2.5 0 0 0 3 10.5c0 .94.53 1.75 1.29 2.18c.36.2.77.32 1.21.32s.85-.12 1.21-.32c.37-.21.68-.51.91-.87A5.42 5.42 0 0 1 6.5 8.5v-.28c-.3-.14-.64-.22-1-.22m13 0c-.36 0-.7.08-1 .22v.28c0 1.2-.39 2.36-1.12 3.31c.12.19.25.34.4.49a2.48 2.48 0 0 0 1.72.7c.44 0 .85-.12 1.21-.32c.76-.43 1.29-1.24 1.29-2.18A2.5 2.5 0 0 0 18.5 8M12 14c-2.34 0-7 1.17-7 3.5V19h14v-1.5c0-2.33-4.66-3.5-7-3.5m-7.29.55C2.78 14.78 0 15.76 0 17.5V19h3v-1.93c0-1.01.69-1.85 1.71-2.52m14.58 0c1.02.67 1.71 1.51 1.71 2.52V19h3v-1.5c0-1.74-2.78-2.72-4.71-2.95M12 16c1.53 0 3.24.5 4.23 1H7.77c.99-.5 2.7-1 4.23-1">
                            </path>
                        </svg>
                    </button>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('clients.assign-permissions')): ?>
                    <button type="button"
                        class="rounded-full bg-purple-100 p-2 text-purple-700 ring-1 ring-inset ring-purple-200 hover:bg-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:ring-purple-800"
                        title="Assign Permissions" data-modal-open="assign-permissions"
                        data-client-id="<?php echo e($client->id); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12c5.16-1.26 9-6.45 9-12V5zm0 2.18l7 3.12v4.92c0 1.7-.5 3.43-1.35 4.95C16 14.94 13.26 14.5 12 14.5s-4 .44-5.65 1.67C5.5 14.65 5 12.92 5 11.22V6.3zM12 6a3.5 3.5 0 0 0-3.5 3.5A3.5 3.5 0 0 0 12 13a3.5 3.5 0 0 0 3.5-3.5A3.5 3.5 0 0 0 12 6m0 2a1.5 1.5 0 0 1 1.5 1.5A1.5 1.5 0 0 1 12 11a1.5 1.5 0 0 1-1.5-1.5A1.5 1.5 0 0 1 12 8m0 8.5c1.57 0 3.64.61 4.53 1.34C15.29 19.38 13.7 20.55 12 21c-1.7-.45-3.29-1.62-4.53-3.16c.9-.73 2.96-1.34 4.53-1.34">
                            </path>
                        </svg>
                    </button>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('clients.manager-supervisors')): ?>
                <button type="button"
                    class="bg-indigo-100 dark:bg-indigo-600/25 hover:bg-indigo-200 !text-indigo-600 dark:!text-indigo-400 font-medium w-8 h-8 flex justify-center items-center rounded-full cursor-pointer"
                    title="<?php echo e(__('admin.clients.table.assign_users_btn')); ?>" data-modal-open="assign-users"
                    data-client-id="<?php echo e($client->id); ?>" data-manager-id="<?php echo e($client->managers->first()?->id); ?>"
                    data-supervisor-id="<?php echo e($client->supervisors->first()?->id); ?>"
                    data-deo-id="<?php echo e($client->dataEntryOperators->first()?->id); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 1 1-8 0a4 4 0 0 1 8 0M3 20a6 6 0 0 1 12 0v1H3z">
                        </path>
                    </svg>
                </button>
            <?php endif; ?>

            <?php if($user->role === \App\Models\User::ROLES['super_admin']): ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('clients.update')): ?>
                    <button type="button" data-modal-open="client-form" data-client-id="<?php echo e($client->id); ?>"
                        class="rounded-full bg-emerald-100 p-2 text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-800">

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

                
                

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('clients.delete')): ?>
                    
                <?php endif; ?>
            <?php endif; ?>
            <?php if($client->guid != ''): ?>
                <a href="<?php echo e(route('clients.dashboard', $client->guid ?? '')); ?>"
                    class="rounded-full bg-blue-100 p-2 text-blue-700 ring-1 ring-inset ring-blue-200 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:ring-blue-800"
                    title="Client Dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M12 3l9 6v12a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V9z" />
                    </svg>
                </a>
                <a href="<?php echo e(route('clients.Gstindex', $client->guid)); ?>" class="rounded-full bg-cyan-100 p-2 text-cyan-700 ring-1 ring-inset ring-cyan-200 hover:bg-cyan-200 dark:bg-cyan-900/30 dark:text-cyan-300 dark:ring-cyan-800" 
                    title="GST Settings">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">

                        <circle cx="12" cy="12" r="3"></circle>

                        <path
                            d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2
                            2 0 1 1-2.83 2.83l-.06-.06a1.65
                            1.65 0 0 0-1.82-.33 1.65
                            1.65 0 0 0-1 1.51V21a2
                            2 0 1 1-4 0v-.09a1.65
                            1.65 0 0 0-1-1.51 1.65
                            1.65 0 0 0-1.82.33l-.06.06a2
                            2 0 1 1-2.83-2.83l.06-.06a1.65
                            1.65 0 0 0 .33-1.82 1.65
                            1.65 0 0 0-1.51-1H3a2
                            2 0 1 1 0-4h.09a1.65
                            1.65 0 0 0 1.51-1 1.65
                            1.65 0 0 0-.33-1.82l-.06-.06a2
                            2 0 1 1 2.83-2.83l.06.06a1.65
                            1.65 0 0 0 1.82.33h.01a1.65
                            1.65 0 0 0 1-1.51V3a2
                            2 0 1 1 4 0v.09a1.65
                            1.65 0 0 0 1 1.51h.01a1.65
                            1.65 0 0 0 1.82-.33l.06-.06a2
                            2 0 1 1 2.83 2.83l-.06.06a1.65
                            1.65 0 0 0-.33 1.82v.01a1.65
                            1.65 0 0 0 1.51 1H21a2
                            2 0 1 1 0 4h-.09a1.65
                            1.65 0 0 0-1.51 1z">
                        </path>

                    </svg>
                </a>


                
            <?php endif; ?>
        </div>
    </td>
</tr>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\clients\client_row.blade.php ENDPATH**/ ?>