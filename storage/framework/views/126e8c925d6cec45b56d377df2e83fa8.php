<?php $__env->startSection('title', 'Blogs'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-2">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-lg font-semibold text-gray-800 dark:text-white">Blogs</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Create and manage the articles shown on the public website.</p>
        </div>

        <a href="<?php echo e(route('super-admin.blog.create')); ?>"
            class="inline-flex items-center justify-center gap-2 self-start rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-black">
            <i class="fas fa-plus" aria-hidden="true"></i>
            New Blog
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
        <div class="flex flex-col gap-3 border-b border-slate-200 p-4 dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="<?php echo e(route('super-admin.blog.index')); ?>" class="w-full sm:max-w-md">
                <label for="blog-search" class="sr-only">Search blogs</label>
                <div class="flex rounded-lg border border-slate-300 bg-white transition focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-500/20 dark:border-slate-600 dark:bg-slate-800">
                    <span class="flex items-center pl-3 text-slate-400"><i class="fas fa-search" aria-hidden="true"></i></span>
                    <input id="blog-search" type="search" name="search" value="<?php echo e(request('search')); ?>"
                        placeholder="Search by title…"
                        class="min-w-0 flex-1 border-0 bg-transparent px-3 py-2 text-sm text-slate-800 outline-none focus:ring-0 dark:text-white">
                    <?php if(request('search')): ?>
                    <a href="<?php echo e(route('super-admin.blog.index')); ?>" title="Clear search"
                        class="flex items-center px-3 text-slate-400 transition hover:text-slate-700 dark:hover:text-white">
                        <i class="fas fa-times" aria-hidden="true"></i><span class="sr-only">Clear search</span>
                    </a>
                    <?php endif; ?>
                    <button type="submit" class="rounded-r-lg bg-primary-600 px-4 text-sm font-medium text-white transition hover:bg-primary-700">Search</button>
                </div>
            </form>
            <button type="button" id="bulkDeleteBtn" disabled
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-rose-200 px-3 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-rose-900/70 dark:text-rose-400 dark:hover:bg-rose-950/40">
                <i class="fas fa-trash-alt" aria-hidden="true"></i>
                Delete selected <span id="selectedCount" class="hidden rounded-full bg-rose-100 px-2 py-0.5 text-xs dark:bg-rose-950">0</span>
            </button>
        </div>

        <form id="bulkDeleteForm" method="POST" action="<?php echo e(route('super-admin.blog.bulkDelete')); ?>">
            <?php echo csrf_field(); ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-800/80">
                        <tr>
                            <th class="w-12 px-4 py-3 text-left">
                                <input type="checkbox" id="select_all" aria-label="Select all blogs"
                                    class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 dark:border-slate-600 dark:bg-slate-800">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-300">Blog</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-300">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-300">Published</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        <?php $__empty_1 = true; $__currentLoopData = $blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="transition hover:bg-slate-50/80 dark:hover:bg-slate-800/60">
                            <td class="px-4 py-4 align-middle">
                                <input type="checkbox" name="ids[]" value="<?php echo e($blog->blog_id); ?>" aria-label="Select <?php echo e($blog->title); ?>"
                                    class="row_checkbox h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 dark:border-slate-600 dark:bg-slate-800">
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex min-w-[260px] items-center gap-3">
                                    <?php if(!empty($blog->image)): ?>
                                    <img src="<?php echo e(asset('uploads/Blog/' . $blog->image)); ?>" alt=""
                                        class="h-14 w-20 flex-none rounded-lg border border-slate-200 object-cover dark:border-slate-700">
                                    <?php else: ?>
                                    <div class="flex h-14 w-20 flex-none items-center justify-center rounded-lg bg-slate-100 text-slate-400 dark:bg-slate-800">
                                        <i class="far fa-image text-xl" aria-hidden="true"></i>
                                    </div>
                                    <?php endif; ?>
                                    <div class="min-w-0">
                                        <p class="max-w-sm truncate font-semibold text-slate-900 dark:text-white" title="<?php echo e($blog->title); ?>"><?php echo e($blog->title); ?></p>
                                        <p class="mt-1 max-w-sm truncate text-sm text-slate-500 dark:text-slate-400"><?php echo e(\Illuminate\Support\Str::limit(strip_tags($blog->description), 75) ?: 'No description provided'); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <span class="inline-flex rounded-full bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-700 dark:bg-sky-950/60 dark:text-sky-300">
                                    <?php echo e($blog->category->name ?? 'Uncategorised'); ?>

                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-600 dark:text-slate-300">
                                <div><?php echo e(optional($blog->created_at)->format('d M Y') ?: '—'); ?></div>
                                <div class="mt-0.5 text-xs text-slate-400"><?php echo e(optional($blog->created_at)->format('h:i A')); ?></div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="<?php echo e(route('super-admin.blog.edit', $blog->blog_id)); ?>" title="Edit blog"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-sky-600 text-white transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500">
                                        <i class="fas fa-edit action-icon" aria-hidden="true"></i><span class="sr-only">Edit</span>
                                    </a>
                                    <button type="button" title="Delete blog"
                                        data-delete-url="<?php echo e(route('super-admin.blog.delete', $blog->blog_id)); ?>" data-delete-title="<?php echo e($blog->title); ?>"
                                        class="delete-blog inline-flex h-9 w-9 items-center justify-center rounded-lg bg-rose-600 text-white transition hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500">
                                        <i class="fas fa-trash action-icon" aria-hidden="true"></i><span class="sr-only">Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 dark:bg-slate-800"><i class="fas fa-newspaper text-xl" aria-hidden="true"></i></div>
                                <p class="mt-3 font-medium text-slate-700 dark:text-slate-200">No blogs found</p>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400"><?php echo e(request('search') ? 'Try a different search term.' : 'Create your first blog to get started.'); ?></p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>

        <?php if($blogs->hasPages()): ?>
        <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-700"><?php echo e($blogs->withQueryString()->links('pagination::tailwind')); ?></div>
        <?php endif; ?>

    </div>
</div>

<div id="blogDeleteModal" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
    <button type="button" class="absolute inset-0 h-full w-full bg-slate-950/60 backdrop-blur-sm" data-close-delete aria-label="Close dialog"></button>

    <div class="relative mx-auto mt-32 w-full max-w-md px-4">
        <div class="rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-800">
            <form id="blogDeleteForm" method="POST"><?php echo csrf_field(); ?>
                <div class="flex items-start gap-4">
                    <div class="flex h-11 w-11 flex-none items-center justify-center rounded-full bg-rose-100 text-rose-600 dark:bg-rose-950 dark:text-rose-400"><i class="fas fa-trash-alt" aria-hidden="true"></i></div>
                    <div>
                        <h2 id="deleteModalTitle" class="text-lg font-semibold text-slate-900 dark:text-white">Delete blog?</h2>
                        <p id="deleteModalMessage" class="mt-1 text-sm text-slate-600 dark:text-slate-300">This action cannot be undone.</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" data-close-delete class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-600 dark:text-white dark:hover:bg-slate-700">Cancel</button>
                    <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-700">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selectAll = document.getElementById('select_all');
        const checkboxes = [...document.querySelectorAll('.row_checkbox')];
        const bulkButton = document.getElementById('bulkDeleteBtn');
        const countBadge = document.getElementById('selectedCount');
        const modal = document.getElementById('blogDeleteModal');
        const deleteForm = document.getElementById('blogDeleteForm');
        const deleteMessage = document.getElementById('deleteModalMessage');

        const updateSelection = () => {
            const count = checkboxes.filter(checkbox => checkbox.checked).length;
            bulkButton.disabled = count === 0;
            countBadge.textContent = count;
            countBadge.classList.toggle('hidden', count === 0);
            selectAll.checked = checkboxes.length > 0 && count === checkboxes.length;
            selectAll.indeterminate = count > 0 && count < checkboxes.length;
        };
        const closeModal = () => {
            modal.classList.add('hidden');
            document.documentElement.classList.remove('overflow-hidden');
        };

        selectAll?.addEventListener('change', () => {
            checkboxes.forEach(checkbox => checkbox.checked = selectAll.checked);
            updateSelection();
        });
        checkboxes.forEach(checkbox => checkbox.addEventListener('change', updateSelection));
        document.querySelectorAll('.delete-blog').forEach(button => button.addEventListener('click', () => {
            deleteForm.action = button.dataset.deleteUrl;
            deleteMessage.textContent = `“${button.dataset.deleteTitle}” will be permanently deleted.`;
            modal.classList.remove('hidden');
            document.documentElement.classList.add('overflow-hidden');
        }));
        document.querySelectorAll('[data-close-delete]').forEach(button => button.addEventListener('click', closeModal));
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') closeModal();
        });
        bulkButton?.addEventListener('click', () => {
            if (!checkboxes.some(checkbox => checkbox.checked)) return;
            if (window.confirm('Permanently delete the selected blogs?')) document.getElementById('bulkDeleteForm').submit();
        });
        updateSelection();
    });

    // function openBlogDeleteModal(actionUrl) {
    //     document.getElementById('blogDeleteForm').action = actionUrl;
    //     document.getElementById('blogDeleteModal').classList.remove('hidden');
    //     document.documentElement.classList.add('overflow-hidden');
    // }

    // function closeBlogDeleteModal() {
    //     document.getElementById('blogDeleteModal').classList.add('hidden');
    //     document.documentElement.classList.remove('overflow-hidden');
    // }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/blog/index.blade.php ENDPATH**/ ?>