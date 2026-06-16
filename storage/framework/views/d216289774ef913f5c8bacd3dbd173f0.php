    
    <?php $__env->startSection('title', 'Documents'); ?>

    <?php $__env->startSection('content'); ?>
    
    <div class="px-2 py-3 sm:px-3">
        <div class="group flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100 sm:text-2xl"></h1>

            <?php if($user->role == \App\Models\User::ROLES['client']): ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('documents.create')): ?>
            <button type="button" id="openUploadModal"
                class="inline-flex items-center justify-center gap-2 rounded-md px-2 py-1 text-sm border border-gray-700 font-medium  text-black dark:text-white  focus:outline-none w-full sm:w-auto hover:border-[#22d3ee]
                                    hover:shadow-[0_0_15px_#22d3ee]
                                    hover:scale-105
                                    hover:-translate-y-1" style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">
                <svg xmlns="http://www.w3.org/2000/svg" width="0.88em" height="1em" viewBox="0 0 448 512">
                    <path fill="currentColor"
                        d="M64 80c-8.8 0-16 7.2-16 16v320c0 8.8 7.2 16 16 16h320c8.8 0 16-7.2 16-16V96c0-8.8-7.2-16-16-16zM0 96c0-35.3 28.7-64 64-64h320c35.3 0 64 28.7 64 64v320c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64zm200 248v-64h-64c-13.3 0-24-10.7-24-24s10.7-24 24-24h64v-64c0-13.3 10.7-24 24-24s24 10.7 24 24v64h64c13.3 0 24 10.7 24 24s-10.7 24-24 24h-64v64c0 13.3-10.7 24-24 24s-24-10.7-24-24">
                    </path>
                </svg>
                Add Document
            </button>
            <?php endif; ?>
            <?php endif; ?>
        </div>

        <?php if($user->role == \App\Models\User::ROLES['client']): ?>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mt-3">

            
            <div class="group relative p-3 rounded-xl text-center
                    border border-gray-700
                    transition duration-1000 ease-in-out
                                    transition-property: all;
                                    hover:border-[#22d3ee]
                                    hover:shadow-[0_0_15px_#22d3ee]
                                    hover:scale-105
                                    hover:-translate-y-1"
                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">

                <div class="absolute inset-0 "></div>

                <p class="relative text-xs text-gray-700 dark:text-gray-400 group-hover:text-[#22d3ee] dark:group-hover:text-[#22d3ee]">Uploaded</p>
                <p class="relative text-lg font-bold text-gray-900 dark:text-white"><?php echo e($uploaded_count); ?></p>
            </div>

            
            <div class="group relative p-3 rounded-xl text-center
                    border border-gray-700
                transition duration-1000 ease-in-out
                                    transition-property: all;
                                    hover:border-[#fbbf24]
                                    hover:shadow-[0_0_15px_#fbbf24]
                                    hover:scale-105
                                    hover:-translate-y-1"
                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">

                <div class="absolute inset-0 "></div>

                <p class="relative text-xs text-gray-700 dark:text-gray-400 group-hover:text-[#fbbf24] dark:group-hover:text-[#fbbf24]">In Progress</p>
                <p class="relative text-lg font-bold text-gray-900 dark:text-white"><?php echo e($in_progress_count); ?></p>
            </div>

            
            <div class="group relative p-3 rounded-xl text-center
                    border border-gray-700
                    transition duration-1000 ease-in-out
                                    transition-property: all;
                                    hover:border-[#f472b6]
                                    hover:shadow-[0_0_15px_#f472b6]
                                    hover:scale-105
                                    hover:-translate-y-1"
                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">

                <div class="absolute inset-0 "></div>

                <p class="relative text-xs text-gray-700 dark:text-gray-400 group-hover:text-[#f472b6] dark:group-hover:text-[#f472b6]">Completed</p>
                <p class="relative text-lg font-bold text-gray-900 dark:text-white"><?php echo e($completed_count); ?></p>
            </div>

            
            <div class="group relative p-3 rounded-xl text-center
                    border border-gray-700
                    transition duration-1000 ease-in-out
                                    transition-property: all;
                                    hover:border-[#a78bfa]
                                    hover:shadow-[0_0_15px_#a78bfa]
                                    hover:scale-105
                                    hover:-translate-y-1"
                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">

                <div class="absolute inset-0 "></div>

                <p class="relative text-xs text-gray-700 dark:text-gray-400 group-hover:text-[#a78bfa] dark:group-hover:text-[#a78bfa]">Rejected</p>
                <p class="relative text-lg font-bold text-gray-900 dark:text-white"><?php echo e($rejected_count); ?></p>
            </div>

            
            <div class="grou[ relative p-3 rounded-xl text-center
                    border border-gray-700
                    transition duration-1000 ease-in-out
                                    transition-property: all;
                                    hover:border-[#34d399]
                                    hover:shadow-[0_0_15px_#34d399]
                                    hover:scale-105
                                    hover:-translate-y-1"
                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">

                <div class="absolute inset-0"></div>

                <p class="relative text-xs text-gray-700 dark:text-gray-400 group-hover:text-[#34d399] dark:group-hover:text-[#34d399]">Accepted</p>
                <p class="relative text-lg font-bold text-gray-900 dark:text-white"><?php echo e($accepted_count); ?></p>
            </div>

        </div>
        <?php endif; ?>

        
        <form method="GET" action="<?php echo e(route('documents.index')); ?>"
            class="mt-2 rounded-lg p-2 flex flex-col sm:flex-row gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4 w-full">
                
                <?php if($user->role != \App\Models\User::ROLES['client']): ?>
                <!-- <div class="w-full relative">
                    <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">Client</label>
                    <select name="client_id"
                        class="w-full appearance-none bg-white/30 dark:bg-[rgba(10,20,35,0.6)] backdrop-blur-xl border border-gray-300/80 dark:border-cyan-400/20 shadow-[inset_0_1px_2px_rgba(255,255,255,0.4)] dark:shadow-[inset_0_1px_2px_rgba(255,255,255,0.05)] text-gray-900 dark:text-white rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#22d3ee] focus:border-[#22d3ee] focus:shadow-[0_0_12px_rgba(34,211,238,0.6)] transition-all duration-300">
                        <option value="">All Clients</option>
                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>"
                            <?php echo e((int) request('client_id') === (int) $c->id ? 'selected' : ''); ?>>
                            <?php echo e($c->name); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500 dark:text-gray-300">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div> -->
                <div class="relative"
                        x-data="{
                            open: false,
                            selected: '<?php echo e(request('client_id', '')); ?>',
                            options: {
                                '': 'All Clients',
                                <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    '<?php echo e($c->id); ?>': '<?php echo e($c->name); ?>',
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            }
                        }">

                        <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">Client</label>

                        <!-- Hidden input -->
                        <input type="hidden" name="client_id" :value="selected">

                        <!-- Button -->
                        <button type="button" @click="open = !open"
                            class="w-full text-left
                            bg-gradient-to-br from-white/60 to-white/30
                            dark:from-white/10 dark:to-transparent
                            backdrop-blur-xl
                            border border-gray-300/80 dark:border-cyan-400/20
                            text-gray-900 dark:text-white
                            rounded-xl px-3 py-2 pr-10 text-sm
                            focus:outline-none
                            focus:ring-2 focus:ring-[#22d3ee]
                            transition-all duration-300">

                            <span x-text="options[selected] ?? 'All Clients'"></span>
                        </button>

                        <!-- Arrow -->
                        <div class="mt-2 pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-300">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>

                        <!-- Dropdown -->
                        <ul x-show="open" @click.outside="open = false"
                            x-transition
                            class="absolute z-50 mt-2 w-full rounded-xl overflow-hidden

                            bg-white/10 dark:bg-white/5
                            backdrop-blur-2xl

                            border border-white/20
                            ring-1 ring-white/10

                            shadow-[0_8px_40px_rgba(0,0,0,0.4)]"
                            style="backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);">

                            <!-- All Clients -->
                            <li>
                                <button type="button"
                                    @click="selected = ''; open = false"
                                    class="w-full text-left px-4 py-2 text-sm
                                        transition-all duration-200
                                        text-gray-800 dark:text-white
                                        hover:bg-black/10 dark:hover:bg-white/10
                                        hover:text-[#22d3ee]"
                                    :class="selected === '' ? 'bg-[#22d3ee]/20 text-[#22d3ee]' : ''">

                                    All Clients
                                </button>
                            </li>

                            <!-- Clients -->
                            <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <button type="button"
                                    @click="selected = '<?php echo e($c->id); ?>'; open = false"
                                    class="w-full text-left px-4 py-2 text-sm
                                        transition-all duration-200
                                        text-gray-800 dark:text-white
                                        hover:bg-black/10 dark:hover:bg-white/10
                                        hover:text-[#22d3ee]"
                                    :class="selected === '<?php echo e($c->id); ?>'
                                        ? 'bg-[#22d3ee]/20 text-[#22d3ee]'
                                        : ''">

                                    <?php echo e($c->name); ?>

                                </button>
                            </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- <div class="relative">
                    <label class="block text-xs text-black-600 dark:text-gray-300 mb-1">Status</label>
                    <select name="status"
                        class="w-full appearance-none bg-gradient-to-br from-white/60 to-white/30 dark:from-white/10 dark:to-transparent backdrop-blur-xl border border-gray-300/80 dark:border-cyan-400/20 shadow-sm dark:shadow-none text-gray-900 dark:text-white rounded-xl px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-[#22d3ee] focus:border-[#22d3ee] focus:shadow-[0_0_12px_rgba(34,211,238,0.6)] transition-all duration-300">
                        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php echo e(request('status', 'all') === $val ? 'selected' : ''); ?>>
                            <?php echo e($label); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500 dark:text-gray-300">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div> -->
                <div class="relative"
                    x-data="{ open: false, selected: '<?php echo e(request('status', 'all')); ?>', options: <?php echo e(json_encode($statuses)); ?> }">

                    <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">Status</label>

                    <!-- Hidden input -->
                    <input type="hidden" name="status" :value="selected">

                    <!-- Button -->
                    <button type="button" @click="open = !open"
                        class="w-full text-left
                        bg-gradient-to-br from-white/60 to-white/30
                        dark:from-white/10 dark:to-transparent
                        backdrop-blur-xl
                        border border-gray-300/80 dark:border-cyan-400/20
                        text-gray-900 dark:text-white
                        rounded-xl px-3 py-2 pr-10 text-sm
                        focus:outline-none
                        focus:ring-2 focus:ring-[#22d3ee]
                        transition-all duration-300">

                        <span x-text="options[selected] ?? 'Select'"></span>
                    </button>

                    <!-- Arrow -->
                    <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-300">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>

                    <!-- 🔥 EXACT PROFILE STYLE DROPDOWN -->
                    <ul x-show="open" @click.outside="open = false"
                        x-transition
                        class="absolute z-50 mt-2 w-full rounded-xl overflow-hidden
                        bg-white/10 dark:bg-white/5   /* 👈 VERY LIGHT */
                        backdrop-blur-2xl             /* 👈 STRONG BLUR */
                        border border-white/20
                        ring-1 ring-white/10
                        shadow-[0_8px_40px_rgba(0,0,0,0.4)]"
                        style="backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);">
                        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <button type="button"
                                @click="selected = '<?php echo e($val); ?>'; open = false"
                                class="w-full text-left px-4 py-2 text-sm
                                    transition-all duration-200
                                    text-gray-800 dark:text-white
                                    hover:bg-black/10 dark:hover:bg-white/10   /* 👈 IMPORTANT */
                                    hover:text-[#22d3ee]"
                                    :class="selected === '<?php echo e($val); ?>'
                                    ? 'bg-[#22d3ee]/20 text-[#22d3ee]'
                                    : ''">

                                <?php echo e($label); ?>

                            </button>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>

                <div class="w-full">
                    <label class="block text-xs text-black-600 dark:text-gray-300 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" min="1900-01-01"
       max="2099-12-31"
                        class="w-full appearance-none bg-gradient-to-br from-white/50 to-white/20 dark:from-white/10 dark:to-transparent backdrop-blur-xl border border-gray-300/80 dark:border-cyan-400/20 shadow-[inset_0_1px_2px_rgba(255,255,255,0.4)] dark:shadow-[inset_0_1px_2px_rgba(255,255,255,0.05)] text-gray-900 dark:text-white rounded-xl px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-[#22d3ee] focus:border-[#22d3ee] focus:shadow-[0_0_12px_rgba(34,211,238,0.6)] transition-all duration-300">
                </div>

                <div class="w-full">
                    <label class="block text-xs text-black-600 dark:text-gray-300 mb-1">End Date</label>
                    <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>" min="1900-01-01"
       max="2099-12-31"
                        class="w-full appearance-none bg-gradient-to-br from-white/50 to-white/20 dark:from-white/10 dark:to-transparent backdrop-blur-xl border border-gray-300/80 dark:border-cyan-400/20 shadow-[inset_0_1px_2px_rgba(255,255,255,0.4)] dark:shadow-[inset_0_1px_2px_rgba(255,255,255,0.05)] text-gray-900 dark:text-white rounded-xl px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-[#22d3ee] focus:border-[#22d3ee] focus:shadow-[0_0_12px_rgba(34,211,238,0.6)] transition-all duration-300">
                </div>

                

            <div class="flex flex-col items-end sm:flex-row gap-2 sm:col-span-2 lg:col-span-1 xl:col-span-1 justify-end">
                <button type="submit"
                    class="rounded-md border border-gray-700 text-black dark:text-white h-9.5 px-4 py-2 text-sm transition duration-1000 ease-in-out
                                    transition-property: all;
                                    hover:border-[#22d3ee]
                                    hover:shadow-[0_0_15px_#22d3ee]
                                    hover:scale-105
                                    hover:-translate-y-1"
                    style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">
                    Search
                </button>
                <a href="<?php echo e(route('documents.index')); ?>"
                    class="rounded-md border border-gray-700 flex items-center text-black dark:text-white h-9.5 px-4 py-2 text-sm transition duration-1000 ease-in-out
                                    transition-property: all;
                                    hover:border-[#a78bfa]
                                    hover:shadow-[0_0_15px_#a78bfa]
                                    hover:scale-105
                                    hover:-translate-y-1"
                    style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">
                    Reset
                </a>
            </div>
    </div>
    </form>

    
    <!-- <div class="mt-1 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700"> -->
    <div class="mt-5 overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden group-block">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-[rgba(10,20,35,0.20)] dark:bg-[rgba(10,20,35,0.6)] dark:bg-gray-900/40 sticky top-0 z-10">
                <tr class="text-black-900 dark:text-gray-300">
                    <th class="px-4 py-2 font-bold">
                        Document
                    </th>
                    <th class="px-4 py-2 font-bold">
                        Upload Date
                    </th>
                    <th class="px-4 py-2 font-bold">
                        Status
                    </th>
                    <th class="px-4 py-2 font-bold">
                        Status Update
                    </th>
                    <th class="px-4 py-2 font-bold">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 tabular-nums">
                <?php $__empty_1 = true; $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="group transition-all duration-300 hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] [&>*]:group-hover:text-black [&_*]:group-hover:text-black">
                    <td class="px-2 py-1.5 group-hover:text-black">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-5 w-5 sm:h-8 sm:w-8 flex items-center justify-center rounded-md bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 flex-shrink-0">
                                <?php
                                $fileName = str_replace("documents/", "", $doc->file_name);
                                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                                $icon = 'fa-file';
                                $color = 'text-gray-500';

                                switch ($ext) {
                                case 'png':
                                case 'jpg':
                                case 'jpeg':
                                case 'gif':
                                case 'webp':
                                $icon = 'fa-file-image';
                                $color = 'text-pink-500';
                                break;

                                case 'pdf':
                                $icon = 'fa-file-pdf';
                                $color = 'text-red-500';
                                break;

                                case 'xls':
                                case 'xlsx':
                                case 'csv':
                                $icon = 'fa-file-excel';
                                $color = 'text-green-500';
                                break;

                                case 'doc':
                                case 'docx':
                                $icon = 'fa-file-word';
                                $color = 'text-blue-500';
                                break;

                                case 'zip':
                                case 'rar':
                                $icon = 'fa-file-zipper';
                                $color = 'text-yellow-500';
                                break;
                                }
                                ?>
                                <i class="fa-solid <?php echo e($icon); ?> <?php echo e($color); ?> text-sm"></i>
                                <!-- <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5"
                                            viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                            <path d="M14 2v6h6" />
                                        </svg> -->
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                    <?php $fileName = str_replace("documents/", "", $doc->file_name); ?>
                                    <?php echo e($fileName); ?>

                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    <?php echo e(number_format(($doc->file_size ?? 0) / 1024 / 1024, 2)); ?> MB
                                </div>
                                <div class="sm:hidden text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <?php echo e(optional($doc->created_at)->timezone(config('app.timezone'))->format('M d, Y')); ?>

                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-2 group-hover:text-black">
                        <?php echo e(optional($doc->created_at)->timezone(config('app.timezone'))->format('d M, Y, h:i A')); ?>

                    </td>
                    <td class="px-4 py-2 group-hover:text-black">
                        <?php
                        $displayStatus = $doc->status;
                        $statusLabel = $doc->status;

                        if (auth()->user()->role == \App\Models\User::ROLES['client']) {
                        $processingStatuses = [
                        'accepted',
                        'data_entry_in_progress',
                        'data_entry_completed',
                        'query_raised',
                        'query_resolved',
                        ];
                        if (in_array($doc->status, $processingStatuses)) {
                        $displayStatus = 'processing';
                        $statusLabel = 'Processing';
                        } else {
                        $statusLabel = ucfirst($doc->status);
                        }
                        } else {
                        $statusLabel = ucfirst(str_replace('_', ' ', $doc->status));
                        }

                        $statusColors = [
                        'uploaded' =>
                        'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-100',
                        'accepted' =>
                        'bg-blue-200 text-blue-900 dark:bg-blue-700 dark:text-blue-100',
                        'data_entry_in_progress' =>
                        'bg-yellow-200 text-yellow-900 dark:bg-yellow-700 dark:text-yellow-100',
                        'data_entry_completed' =>
                        'bg-purple-200 text-purple-900 dark:bg-purple-700 dark:text-purple-100',
                        'query_raised' =>
                        'bg-orange-200 text-orange-900 dark:bg-orange-700 dark:text-orange-100',
                        'query_resolved' =>
                        'bg-indigo-200 text-indigo-900 dark:bg-indigo-700 dark:text-indigo-100',
                        'approved' =>
                        'bg-green-200 text-green-900 dark:bg-green-700 dark:text-green-100',
                        'rejected' => 'bg-red-200 text-red-900 dark:bg-red-700 dark:text-red-100',
                        'processing' =>
                        'bg-yellow-200 text-yellow-900 dark:bg-yellow-700 dark:text-yellow-100',
                        ];

                        $statusClass = $statusColors[$displayStatus] ?? $statusColors['uploaded'];
                        ?>
                        <span
                            class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold <?php echo e($statusClass); ?>">
                            <?php echo e($statusLabel); ?>

                        </span>
                    </td>
                    <td class="px-4 py-2 group-hover:text-black">
                        <?php echo e(optional($doc->updated_at)->timezone(config('app.timezone'))->format('d M, Y, h:i A')); ?>

                    </td>
                    <td class="px-4 py-2 group-hover:text-black">
                        <div class="flex items-center justify-end gap-1 sm:gap-2">
                            <?php if(
                            $user->role === \App\Models\User::ROLES['super_admin'] ||
                            $user->role === \App\Models\User::ROLES['manager'] ||
                            $user->role === \App\Models\User::ROLES['data_entry_operator'] ||
                            $user->role === \App\Models\User::ROLES['supervisor']
                            ): ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('documents.download')): ?>
                            <a href="<?php echo e(route('documents.download', $doc->id)); ?>"
                                class="rounded-full bg-emerald-100 p-1.5 sm:p-2 text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-800"
                                title="Download">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                    viewBox="0 0 24 24">
                                    <g fill="none" stroke="currentColor" stroke-linecap="round"
                                        stroke-width="1.5">
                                        <path
                                            d="M17 9.002c2.175.012 3.353.109 4.121.877C22 10.758 22 12.172 22 15v1c0 2.829 0 4.243-.879 5.122C20.243 22 18.828 22 16 22H8c-2.828 0-4.243 0-5.121-.878C2 20.242 2 18.829 2 16v-1c0-2.828 0-4.242.879-5.121c.768-.768 1.946-.865 4.121-.877">
                                        </path>
                                        <path stroke-linejoin="round" d="M12 2v13m0 0l-3-3.5m3 3.5l3-3.5">
                                        </path>
                                    </g>
                                </svg>
                            </a>
                            <?php endif; ?>
                            <?php endif; ?>

                            
                            <?php if($user->role === \App\Models\User::ROLES['data_entry_operator']): ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('documents.verify')): ?>
                            <?php if(!in_array(trim($doc->status), ['approved', 'data_entry_completed'])): ?>
                            <button type="button"
                                onclick="openVerifyModal(<?php echo e($doc->id); ?>,'<?php echo e($doc->status); ?>')"
                                class="rounded-md bg-primary-600 px-2 py-1 text-xs sm:text-sm text-white hover:bg-primary-700 whitespace-nowrap">
                                Verify
                            </button>
                            <?php endif; ?>
                            <?php endif; ?>
                            <?php endif; ?>

                            
                            <?php if(
                            $user->role === \App\Models\User::ROLES['supervisor'] ||
                            $user->role === \App\Models\User::ROLES['super_admin'] ||
                            $user->role === \App\Models\User::ROLES['manager']
                            ): ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('documents.view-activities')): ?>
                            <a href="<?php echo e(route('documents.activities', $doc)); ?>" title="Activity Log"
                                class="bg-pink-200 dark:bg-pink-400/25 hover:bg-pink-300 dark:hover:bg-pink-400/40 text-pink-700 dark:!text-pink-300 font-medium w-7 h-7 sm:w-8 sm:h-8 flex justify-center items-center rounded-full cursor-pointer flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                    viewBox="0 0 24 24">
                                    <path fill="none" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="1.5"
                                        d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0a.375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0a.375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0a.375.375 0 0 1 .75 0Z">
                                    </path>
                                </svg>
                            </a>
                            <?php endif; ?>

                            
                            <?php if($user->role === \App\Models\User::ROLES['supervisor']): ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('documents.sup-verify')): ?>
                            <?php if(
                            !in_array(trim($doc->status), ['approved', 'accepted', 'uploaded', 'data_entry_in_progress', 'rejected']) ||
                            $doc->status == 'data_entry_completed'): ?>
                            <button type="button"
                                onclick="openSupVerifyModal(<?php echo e($doc->id); ?>)"
                                class="rounded-md bg-primary-600 px-2 py-1 text-xs sm:text-sm text-white hover:bg-primary-700 whitespace-nowrap">
                                Verify
                            </button>
                            <?php endif; ?>
                            <?php endif; ?>
                            <?php endif; ?>
                            <?php endif; ?>

                            
                            <?php if($user->role == \App\Models\User::ROLES['client']): ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('documents.update')): ?>
                            <?php if($doc->status === 'rejected'): ?>
                            <button type="button"
                                class="rounded-full bg-blue-100 p-1.5 sm:p-2 text-blue-700 ring-1 ring-inset ring-blue-200 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:ring-blue-800"
                                title="Edit" data-edit-id="<?php echo e($doc->id); ?>"
                                data-edit-name="<?php echo e($doc->display_name ?? str_replace('documents/', '', $doc->file_name)); ?>"
                                data-edit-action="<?php echo e(route('documents.update', $doc->id)); ?>"
                                onclick="openEditModal(this)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                    viewBox="0 0 24 24">
                                    <g fill="none" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2">
                                        <path
                                            d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                        <path
                                            d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z" />
                                    </g>
                                </svg>
                            </button>
                            <?php endif; ?>
                            <?php endif; ?>
                            <?php endif; ?>

                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('documents.delete')): ?>
                            <?php if($user->role === \App\Models\User::ROLES['client']): ?>
                            <?php if($doc->status === 'uploaded'): ?>
                            <form action="<?php echo e(route('documents.destroy', $doc->id)); ?>" method="POST"
                                onsubmit="return confirm('Delete this document?');" class="inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit"
                                    class="rounded-full bg-rose-100 p-1.5 sm:p-2 text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-200 dark:bg-rose-900/30 dark:text-rose-300 dark:ring-rose-800"
                                    title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em"
                                        height="1em" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M10 5h4a2 2 0 1 0-4 0M8.5 5a3.5 3.5 0 1 1 7 0h5.75a.75.75 0 0 1 0 1.5h-1.32l-1.17 12.111A3.75 3.75 0 0 1 15.026 22H8.974a3.75 3.75 0 0 1-3.733-3.389L4.07 6.5H2.75a.75.75 0 0 1 0-1.5zm2 4.75a.75.75 0 0 0-1.5 0v7.5a.75.75 0 0 0 1.5 0zM14.25 9a.75.75 0 0 1 .75.75v7.5a.75.75 0 0 1-1.5 0v-7.5a.75.75 0 0 1 .75-.75m-7.516 9.467a2.25 2.25 0 0 0 2.24 2.033h6.052a2.25 2.25 0 0 0 2.24-2.033L18.424 6.5H5.576z" />
                                    </svg>
                                </button>
                            </form>
                            <?php else: ?>
                            <button type="button" disabled
                                class="rounded-full bg-rose-100/40 p-1.5 sm:p-2 text-rose-400 ring-1 ring-inset ring-rose-200/50 cursor-not-allowed flex-shrink-0"
                                title="Delete allowed only for 'Uploaded' status">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill="currentColor"
                                        d="M10 5h4a2 2 0 1 0-4 0M8.5 5a3.5 3.5 0 1 1 7 0h5.75a.75.75 0 0 1 0 1.5h-1.32l-1.17 12.111A3.75 3.75 0 0 1 15.026 22H8.974a3.75 3.75 0 0 1-3.733-3.389L4.07 6.5H2.75a.75.75 0 0 1 0-1.5z" />
                                </svg>
                            </button>
                            <?php endif; ?>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="px-4 py-3 text-sm text-black-900 dark:text-gray-300">
                        No documents found.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- </div> -->

    <div class="mt-4 px-4 sm:px-0">
        <?php echo e($documents->links()); ?>

    </div>
    </div>

    
    <!-- <div id="uploadModal" class="fixed inset-0 z-50 hidden">
                                    <div class="absolute inset-0 bg-black/50"></div>

                                    <div
                                        class="relative mx-auto mt-20 w-[680px] max-w-[92vw] rounded-2xl
                        bg-white dark:bg-gray-900 shadow-xl border border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Documents Upload</h3>
                                            <button type="button" id="closeUploadModal"
                                                class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300">
                                                ✕
                                            </button>
                                        </div>

                                        <form id="uploadForm" action="<?php echo e(route('documents.store')); ?>" method="POST" enctype="multipart/form-data">
                                            <?php echo csrf_field(); ?>
                                            <div class="px-6 py-5">
                                                
                                                <div id="dropArea"
                                                    class="border-2 border-dashed rounded-xl px-6 py-10 text-center
                                    border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800">
                                                    <div
                                                        class="mx-auto mb-4 flex h-12 w-12 items-center justify-center
                                        rounded-full bg-gray-200 dark:bg-gray-700">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 dark:text-gray-300"
                                                            viewBox="0 0 24 24" fill="currentColor">
                                                            <path d="M7 20h10a3 3 0 0 0 3-3 3 3 0 0 0-3-3h-.26A8 8 0 1 0 4 12" />
                                                        </svg>
                                                    </div>
                                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                                        <span class="font-medium">Drag &amp; drop files here</span> or click to browse
                                                    </p>
                                                    <input id="fileInput" type="file" name="files[]" multiple class="hidden" />
                                                </div>

                                                
                                                <ul id="fileList" class="mt-4 space-y-2 max-h-48 overflow-y-auto"></ul>

                                                
                                                <div id="progressWrap" class="hidden mt-4">
                                                    <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-2.5">
                                                        <div id="progressBar" class="h-2.5 rounded-full bg-blue-600" style="width:0%"></div>
                                                    </div>
                                                    <div id="progressText" class="mt-1 text-xs text-gray-500 dark:text-gray-400">0%</div>
                                                </div>
                                            </div>

                                            <div class="flex justify-end gap-2 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                                                <button type="button" id="cancelUpload"
                                                    class="rounded-md px-4 py-2 text-sm bg-gray-200 dark:bg-gray-700 dark:text-gray-100">
                                                    Cancel
                                                </button>
                                                <button type="submit" id="uploadBtn"
                                                    class="rounded-md px-4 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-60">
                                                    Upload
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div> -->
    <div id="uploadModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50"></div>

        <div
            class="relative mx-auto mt-20 w-[680px] max-w-[92vw] rounded-2xl bg-white dark:bg-gray-900 shadow-xl border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between px-3 py-2 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Documents Upload</h3>
                <button type="button" id="closeUploadModal"
                    class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300">
                    ✕
                </button>
            </div>

            <form id="uploadForm" action="<?php echo e(route('documents.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="px-6 py-5">
                    
                    <div id="dropArea"
                        class="border-2 border-dashed rounded-xl px-6 py-10 text-center border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 transition-colors">
                        <div
                            class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 dark:text-gray-300"
                                viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 20h10a3 3 0 0 0 3-3 3 3 0 0 0-3-3h-.26A8 8 0 1 0 4 12" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-1">
                            <span class="font-medium">Drag &amp; drop files here</span> or click to browse
                        </p>
                        <p class="text-xs text-red-500 dark:text-red-400">
                            <!-- Supported file types: .png, .jpg, .pdf, .xl (max 30MB) -->
                            Supported file types: : .png, .jpg, .pdf, .xl, .HEIC, .PDF, .xl (Max 30MB)
                        </p>
                        <input id="fileInput" type="file" name="files[]" multiple class="hidden"
                            accept=".png,.jpg,.jpeg,.pdf,.xls,.xlsx" />
                    </div>

                    
                    <div id="fileListContainer" class="mt-4 hidden">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Selected Files</h4>
                        <ul id="fileList" class="space-y-2 max-h-48 overflow-y-auto"></ul>
                    </div>

                    
                    <div id="progressWrap" class="hidden mt-4">
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div id="progressBar" class="h-2.5 rounded-full bg-blue-600 transition-all duration-300"
                                style="width:0%"></div>
                        </div>
                        <div id="progressText" class="mt-1 text-xs text-gray-500 dark:text-gray-400">0%</div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 px-3 py-2 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" id="cancelUpload"
                        class="rounded-md px-4 py-2 text-sm bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="submit" id="uploadBtn"
                        class="rounded-md px-4 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 disabled:opacity-60">
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="verifyDocumentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg dark:bg-neutral-900">
            <div class="flex items-center justify-between border-b pb-3">
                <h2 class="text-lg font-semibold">Verify Document</h2>
                <button type="button" onclick="closeVerifyModal()"
                    class="text-neutral-400 hover:text-neutral-600">&times;</button>
            </div>

            <form method="POST" action="<?php echo e(route('documents.verify')); ?>" class="mt-2 space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="document_id" id="verify_document_id">

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="verify_status" required
                        class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                        <option value="">-- Select --</option>
                        <option value="accepted">Accepted</option>
                        <option value="rejected">Rejected</option>
                        <option value="data_entry_in_progress">Data Entry In Progress</option>
                        <option value="data_entry_completed">Data Entry Completed</option>
                        <option value="query_resolved">Query Resolved</option>
                    </select>
                </div>

                <!-- Rejection Reason -->
                <div id="reasonWrapper" class="hidden">
                    <label class="block text-sm font-medium">Reason <span class="text-red-500">*</span></label>
                    <textarea name="reason" id="reason" placeholder="Please provide reason for rejection..."
                        class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        rows="3"></textarea>
                </div>

                <!-- Query Resolved Description -->
                <div id="descriptionWrapper" class="hidden">
                    <label class="block text-sm font-medium">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" id="description" placeholder="Please provide some details..."
                        class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        rows="3"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeVerifyModal()"
                        class="rounded-md border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800">
                        Cancel
                    </button>
                    <button type="submit"
                        class="rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="supVerifyModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg dark:bg-neutral-900">
            <div class="flex items-center justify-between border-b pb-3">
                <h2 class="text-lg font-semibold">Verify Document</h2>
                <button type="button" onclick="closeSupVerifyModal()"
                    class="text-neutral-400 hover:text-neutral-600">&times;</button>
            </div>

            <form method="POST" action="<?php echo e(route('documents.sup_verify')); ?>" class="mt-4 space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="document_id" id="sup_verify_document_id">

                <div>
                    <label class="block text-sm font-medium">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="sup_verify_status" required
                        class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                        <option value="">-- Select --</option>
                        <option value="approved">Approved</option>
                        <option value="query_raised">Query Raised</option>
                    </select>
                </div>

                <!-- Description (only for Query Raised) -->
                <div id="sup_description_wrapper" class="hidden">
                    <label class="block text-sm font-medium">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" id="sup_description" rows="3"
                        class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeSupVerifyModal()"
                        class="rounded-md border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800">
                        Cancel
                    </button>
                    <button type="submit"
                        class="rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 hidden">
        
    </div>

    
    <script>
    // function showError(msg) {
    //     const div = document.createElement('div');
    //     div.className = "text-red-500 text-xs mt-1";
    //     div.innerText = msg;

    //     document.getElementById('dropArea').appendChild(div);

    //     setTimeout(() => div.remove(), 3000);
    // }
    
    // function appendFiles(list) {

    //     const allowedTypes = [
    //         'image/jpeg',
    //         'image/png',
    //         'image/heic',
    //         'image/heif',
    //         'application/pdf',
    //         'application/vnd.ms-excel',
    //         'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    //     ];

    //     const maxSize = 30 * 1024 * 1024; // 30MB

    //     for (const file of list) {

    //         // 🔹 Validate size
    //         if (file.size > maxSize) {
    //             alert(`❌ ${file.name} exceeds 30MB limit`);
    //             continue;
    //         }

    //         // 🔹 Validate type (fallback for HEIC issue)
    //         const ext = file.name.split('.').pop().toLowerCase();

    //         const allowedExt = ['jpg','jpeg','png','heic','heif','pdf','xls','xlsx'];

    //         if (!allowedTypes.includes(file.type) && !allowedExt.includes(ext)) {
    //             alert(`❌ Invalid file type: ${file.name}`);
    //             continue;
    //         }

    //         // ✅ Add valid file
    //         filesBuffer.push(file);
    //     }

    //     renderList();
    // }

        (function() {
            const openBtn = document.getElementById('openUploadModal');
            const closeBtn = document.getElementById('closeUploadModal');
            const cancelBtn = document.getElementById('cancelUpload');
            const modal = document.getElementById('uploadModal');
            const dropArea = document.getElementById('dropArea');
            const fileInput = document.getElementById('fileInput');
            const fileList = document.getElementById('fileList');
            const form = document.getElementById('uploadForm');
            const uploadBtn = document.getElementById('uploadBtn');
            const progressWrap = document.getElementById('progressWrap');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');

            let filesBuffer = [];
            const maxFileSize = 30 * 1024 * 1024; // 30MB
            const allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/heic',
                'image/heif',
                'application/pdf',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ];
            const allowedExt = ['jpg', 'jpeg', 'png', 'heic', 'heif', 'pdf', 'xls', 'xlsx'];

            function openModal() {
                modal.classList.remove('hidden');
            }

            function closeModal() {
                modal.classList.add('hidden');
                reset();
            }

            function reset() {
                filesBuffer = [];
                fileList.innerHTML = '';
                fileInput.value = '';
                progressWrap.classList.add('hidden');
                progressBar.style.width = '0%';
                progressText.textContent = '0%';
                uploadBtn.disabled = false;
            }

            openBtn?.addEventListener('click', openModal);
            closeBtn?.addEventListener('click', closeModal);
            cancelBtn?.addEventListener('click', closeModal);
            modal?.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });

            // Click to browse
            dropArea.addEventListener('click', () => fileInput.click());

            // Drag & drop
            ;
            ['dragenter', 'dragover'].forEach(evt => {
                dropArea.addEventListener(evt, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropArea.classList.add('ring-2', 'ring-blue-500');
                });
            });;
            ['dragleave', 'drop'].forEach(evt => {
                dropArea.addEventListener(evt, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropArea.classList.remove('ring-2', 'ring-blue-500');
                });
            });
            dropArea.addEventListener('drop', (e) => {
                const items = e.dataTransfer.files;
                appendFiles(items);
            });
            fileInput.addEventListener('change', (e) => {
                appendFiles(e.target.files);
            });

            function appendFiles(list) {
                for (const f of list) {
                    if (f.size > maxFileSize) {
                        alert(`❌ ${f.name} exceeds 30MB limit.`);
                        continue;
                    }

                    const ext = (f.name.split('.').pop() || '').toLowerCase();
                    if (!allowedTypes.includes(f.type) && !allowedExt.includes(ext)) {
                        alert(`❌ Invalid file type: ${f.name}`);
                        continue;
                    }
                    filesBuffer.push(f);
                }
                renderList();
            }

            function renderList() {
                fileList.innerHTML = '';
                filesBuffer.forEach((f, idx) => {
                    const li = document.createElement('li');
                    li.className =
                        'flex items-center justify-between rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2';
                    li.innerHTML = `
                            <div class="text-sm text-gray-800 dark:text-gray-100">
                                <span class="font-medium">${escapeHtml(f.name)}</span>
                                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">(${(f.size/1024/1024).toFixed(2)} MB)</span>
                            </div>
                            <button type="button" data-i="${idx}"
                                class="remove-file text-red-600 hover:text-red-700 text-xs">Remove</button>`;
                    fileList.appendChild(li);
                });
                // remove handlers
                fileList.querySelectorAll('.remove-file').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const i = parseInt(btn.getAttribute('data-i'));
                        filesBuffer.splice(i, 1);
                        renderList();
                    });
                });
            }

            function escapeHtml(s) {
                return s.replace(/[&<>'"]/g, c => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    "'": '&#39;',
                    '"': '&quot;'
                } [c]));
            }

            // Submit via XHR (to track progress)
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (filesBuffer.length === 0) {
                    alert('Please select at least one file.');
                    return;
                }

                const oversized = filesBuffer.find((f) => f.size > maxFileSize);
                if (oversized) {
                    alert(`❌ ${oversized.name} exceeds 30MB limit.`);
                    return;
                }
                
                uploadBtn.disabled = true;
                progressWrap.classList.remove('hidden');

                const fd = new FormData();
                filesBuffer.forEach((f) => fd.append('files[]', f));
                fd.append('_token', '<?php echo e(csrf_token()); ?>');

                const xhr = new XMLHttpRequest();
                xhr.open('POST', this.action, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const pct = Math.round((e.loaded / e.total) * 100);
                        progressBar.style.width = pct + '%';
                        progressText.textContent = pct + '%';
                    }
                });

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            // success: close and reload list
                            closeModal();
                            window.location.reload();
                        } else {
                            uploadBtn.disabled = false;
                            alert('Upload failed. Please try again.');
                        }
                    }
                };
                xhr.send(fd);
            });
        })();
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const statusSelect = document.getElementById("verify_status");
            const reasonWrapper = document.getElementById("reasonWrapper");
            const descriptionWrapper = document.getElementById("descriptionWrapper");
            const reason = document.getElementById("reason");
            const description = document.getElementById("description");

            if (!statusSelect) return; // stop if modal not present

            statusSelect.addEventListener("change", function() {
                const val = this.value;

                // reset
                reasonWrapper.classList.add("hidden");
                descriptionWrapper.classList.add("hidden");
                reason.required = false;
                description.required = false;

                if (val === "rejected") {
                    reasonWrapper.classList.remove("hidden");
                    reason.required = true;
                } else if (val === "query_resolved") {
                    descriptionWrapper.classList.remove("hidden");
                    description.required = true;
                }
            });
        });
    </script>
    <script>
        function openVerifyModal(docId) {
            // show modal
            document.getElementById("verifyDocumentModal").classList.remove("hidden");

            // assign doc id to hidden input
            document.getElementById("verify_document_id").value = docId;
        }

        function closeVerifyModal() {
            // hide modal
            document.getElementById("verifyDocumentModal").classList.add("hidden");

            // reset status + fields
            const statusSelect = document.getElementById("verify_status");
            const reasonWrapper = document.getElementById("reasonWrapper");
            const descriptionWrapper = document.getElementById("descriptionWrapper");
            const reason = document.getElementById("reason");
            const description = document.getElementById("description");

            if (statusSelect) statusSelect.value = "";
            if (reasonWrapper) reasonWrapper.classList.add("hidden");
            if (descriptionWrapper) descriptionWrapper.classList.add("hidden");
            if (reason) reason.value = "";
            if (description) description.value = "";
        }
    </script>

    <script>
        function openSupVerifyModal(docId) {
            const m = document.getElementById('supVerifyModal');
            const input = document.getElementById('sup_verify_document_id');
            if (!m || !input) return;
            input.value = docId;
            m.classList.remove('hidden');
        }

        function closeSupVerifyModal() {
            const m = document.getElementById('supVerifyModal');
            const status = document.getElementById('sup_verify_status');
            const wrap = document.getElementById('sup_description_wrapper');
            const desc = document.getElementById('sup_description');
            if (m) m.classList.add('hidden');
            if (status) status.value = '';
            if (wrap) wrap.classList.add('hidden');
            if (desc) {
                desc.required = false;
                desc.value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const status = document.getElementById('sup_verify_status');
            const wrap = document.getElementById('sup_description_wrapper');
            const desc = document.getElementById('sup_description');
            if (!status) return;

            status.addEventListener('change', function() {
                const v = this.value;
                wrap.classList.add('hidden');
                if (desc) desc.required = false;

                if (v === 'query_raised') {
                    wrap.classList.remove('hidden');
                    if (desc) desc.required = true;
                }
            });
        });
    </script>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/client/documents/index.blade.php ENDPATH**/ ?>