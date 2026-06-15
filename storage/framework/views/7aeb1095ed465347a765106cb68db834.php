<?php $__env->startSection('title', 'Blog List'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h6 class="text-lg font-semibold text-gray-800 dark:text-white">
                Blog List
            </h6>

            <a href="<?php echo e(route('super-admin.blog.create')); ?>"
                class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Add New
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12">
            <div class="col-span-12">
                <div class="bg-white dark:bg-gray-800 shadow rounded-2xl overflow-hidden">
                    <div class="p-6">

                        <div class="mb-5 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <form method="GET" action="<?php echo e(route('super-admin.blog.index')); ?>" class="w-full md:max-w-xl">
                                <div class="flex overflow-hidden rounded-lg border border-gray-300 dark:border-gray-700">
                                    <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                                        placeholder="Search blog title..."
                                        class="w-full border-0 bg-white px-4 py-2 text-sm text-gray-700 outline-none focus:ring-0 dark:bg-gray-900 dark:text-white">

                                    <button type="submit"
                                        class="bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">
                                        Search
                                    </button>

                                    <a href="<?php echo e(route('super-admin.blog.index')); ?>"
                                        class="bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                                        Reset
                                    </a>
                                </div>
                            </form>

                            <button type="button" id="bulkDeleteBtn"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">
                                <i class="fas fa-trash"></i>
                                Bulk Delete
                            </button>
                        </div>

                        <form id="bulkDeleteForm" method="POST" action="<?php echo e(route('super-admin.blog.bulkDelete')); ?>">
                            <?php echo csrf_field(); ?>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-900">
                                        <tr>
                                            <th class="px-4 py-3 text-left">
                                                <input type="checkbox" id="select_all"
                                                    class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                                                Image
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                                                Title
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                                                Description
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                                                Created At
                                            </th>
                                            <th
                                                class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                        <?php $__empty_1 = true; $__currentLoopData = $blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                <td class="px-4 py-4">
                                                    <input type="checkbox" name="ids[]" value="<?php echo e($blog->blog_id); ?>"
                                                        class="row_checkbox h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                                </td>

                                                <td class="px-4 py-4">
                                                    <?php if(!empty($blog->image)): ?>
                                                        <img src="<?php echo e(asset('uploads/Blog/' . $blog->image)); ?>"
                                                            alt="Blog Image"
                                                            class="h-14 w-14 rounded-lg object-cover shadow-sm">
                                                    <?php else: ?>
                                                        <div
                                                            class="flex h-14 w-14 items-center justify-center rounded-lg bg-gray-100 text-xs text-gray-400 dark:bg-gray-700">
                                                            No Image
                                                        </div>
                                                    <?php endif; ?>
                                                </td>

                                                <td class="px-4 py-4">
                                                    <div class="font-medium text-gray-900 dark:text-white">
                                                        <?php echo e($blog->title); ?>

                                                    </div>
                                                </td>

                                                <td class="px-4 py-4">
                                                    <div class="max-w-md text-sm text-gray-600 dark:text-gray-300">
                                                        <?php echo \Illuminate\Support\Str::limit(strip_tags($blog->description), 100); ?>

                                                    </div>
                                                </td>

                                                <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">
                                                    <?php echo e($blog->created_at ? \Carbon\Carbon::parse($blog->created_at)->format('d-m-Y h:i A') : '—'); ?>

                                                </td>

                                                <td class="px-4 py-4 text-right">
                                                    <div class="inline-flex items-center gap-2">
                                                        <a href="<?php echo e(route('super-admin.blog.edit', $blog->blog_id)); ?>"
                                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-sky-600 text-white hover:bg-sky-700"
                                                            title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <button type="button"
                                                            onclick="openBlogDeleteModal('<?php echo e(route('super-admin.blog.delete', $blog->blog_id)); ?>')"
                                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-rose-600 text-white hover:bg-rose-700"
                                                            title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="6"
                                                    class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                                    No record found.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </form>

                        <?php if($blogs->count()): ?>
                            <div class="mt-4">
                                <?php echo e($blogs->withQueryString()->links('pagination::tailwind')); ?>

                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="blogDeleteModal" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-slate-900/60" onclick="closeBlogDeleteModal()"></div>

        <div class="relative mx-auto mt-32 w-full max-w-md px-4">
            <div class="rounded-2xl bg-white p-6 shadow-xl dark:bg-slate-800">
                <form id="blogDeleteForm" method="POST">
                    <?php echo csrf_field(); ?>

                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
                        Delete blog?
                    </h3>

                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                        This action cannot be undone.
                    </p>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeBlogDeleteModal()"
                            class="rounded-lg border border-slate-300 px-4 py-2 text-sm dark:border-slate-700 dark:text-white">
                            Cancel
                        </button>

                        <button type="submit"
                            class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">
                            Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select_all');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const bulkDeleteForm = document.getElementById('bulkDeleteForm');

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    document.querySelectorAll('.row_checkbox').forEach(function(checkbox) {
                        checkbox.checked = selectAll.checked;
                    });
                });
            }

            if (bulkDeleteBtn) {
                bulkDeleteBtn.addEventListener('click', function() {
                    const checked = document.querySelectorAll('.row_checkbox:checked').length;

                    if (checked === 0) {
                        alert('Please select at least one record.');
                        return;
                    }

                    if (confirm('Are you sure you want to delete selected records?')) {
                        bulkDeleteForm.submit();
                    }
                });
            }
        });

        function openBlogDeleteModal(actionUrl) {
            document.getElementById('blogDeleteForm').action = actionUrl;
            document.getElementById('blogDeleteModal').classList.remove('hidden');
            document.documentElement.classList.add('overflow-hidden');
        }

        function closeBlogDeleteModal() {
            document.getElementById('blogDeleteModal').classList.add('hidden');
            document.documentElement.classList.remove('overflow-hidden');
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\blog\index.blade.php ENDPATH**/ ?>