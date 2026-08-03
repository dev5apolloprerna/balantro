<?php ($isEditing = isset($blog) && $blog); ?>
<?php $__env->startSection('title', $isEditing ? 'Edit Blog' : 'Add Blog'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4">
    <div class="mb-6 flex items-center justify-between gap-4">
        <h1 class="text-lg font-semibold text-gray-800 dark:text-white">
            <?php echo e($isEditing ? 'Edit Blog' : 'Add Blog'); ?>

        </h1>

        <a href="<?php echo e(route('super-admin.blog.index')); ?>" title="Go Back" class="group btn inline-block relative text-black dark:text-white px-4 py-2 text-sm rounded-md border border-gray-700
                    hover:border-[#f472b6] hover:shadow-[0_0_15px_#f472b6] hover:scale-105 hover:-translate-y-1">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>
    <form action="<?php echo e($isEditing ? route('super-admin.blog.update', $blog->blog_id) : route('super-admin.blog.store')); ?>"
        method="POST" enctype="multipart/form-data"
        class="overflow-hidden rounded-xl border border-neutral-200 shadow-sm dark:border-neutral-700">
        <?php echo csrf_field(); ?>

        <?php if($errors->any()): ?>
        <div class="border-b border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-300" role="alert">
            <p class="font-semibold">Please correct the highlighted fields and try again.</p>
        </div>
        <?php endif; ?>
        <div class="space-y-8 p-4 sm:p-6">
            <section aria-labelledby="blog-content-heading">
                <div class="mb-4 border-b border-neutral-200 pb-3 dark:border-neutral-700">
                    <h2 id="blog-content-heading" class="font-semibold text-neutral-900 dark:text-white">Blog content</h2>
                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Add the article title, category, image, and main content.</p>
                </div>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label for="title" class="mb-1.5 block text-sm font-semibold text-neutral-700 dark:text-neutral-200">
                            Title <span class="text-rose-600">*</span>
                        </label>
                        <input id="title" type="text" name="title" value="<?php echo e(old('title', $blog->title ?? '')); ?>"
                            required autofocus aria-invalid="<?php echo e($errors->has('title') ? 'true' : 'false'); ?>"
                            class="h-11 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm text-neutral-900 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white">
                        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="category_id" class="mb-1.5 block text-sm font-semibold text-neutral-700 dark:text-neutral-200">
                            Category <span class="text-rose-600">*</span>
                        </label>
                        <select id="category_id" name="category_id" required aria-invalid="<?php echo e($errors->has('category_id') ? 'true' : 'false'); ?>"
                            class="h-11 w-full cursor-pointer rounded-lg border border-neutral-300 bg-white px-3 text-sm text-neutral-900 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white">
                            <option value="">Select Category</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->category_id); ?>" <?php if(old('category_id', $blog->category_id ?? '') == $category->category_id): echo 'selected'; endif; ?>>
                                <?php echo e($category->name); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="md:col-span-2">
                        <label for="image" class="mb-1.5 block text-sm font-semibold text-neutral-700 dark:text-neutral-200">Featured image</label>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <?php if($isEditing && !empty($blog->image)): ?>
                            <img src="<?php echo e(asset('uploads/Blog/' . $blog->image)); ?>" alt="Current featured image"
                                class="h-16 w-24 flex-none rounded-lg border border-neutral-200 object-cover dark:border-neutral-700">
                            <?php endif; ?>
                            <div class="w-full">
                                <input id="image" type="file" name="image" accept="image/*"
                                    class="block w-full rounded-lg border border-neutral-300 bg-white text-sm text-neutral-700 file:mr-4 file:border-0 file:border-r file:border-neutral-300 file:bg-neutral-50 file:px-4 file:py-2.5 file:font-semibold file:text-neutral-700 hover:file:bg-neutral-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:file:border-neutral-700 dark:file:bg-neutral-800 dark:file:text-neutral-200">
                                <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">Choose a clear landscape image for the public blog page.</p>
                            </div>
                        </div>
                        <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="mb-1.5 block text-sm font-semibold text-neutral-700 dark:text-neutral-200">Description</label>
                        <textarea id="description" name="description" rows="10"
                            class="ckeditor w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"><?php echo e(old('description', $blog->description ?? '')); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </section>

            <section aria-labelledby="blog-seo-heading">
                <div class="mb-4 border-b border-neutral-200 pb-3 dark:border-neutral-700">
                    <h2 id="blog-seo-heading" class="font-semibold text-neutral-900 dark:text-white">Search engine details</h2>
                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Optional metadata used when the article appears in search results.</p>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label for="metaTitle" class="mb-1.5 block text-sm font-semibold text-neutral-700 dark:text-neutral-200">Meta title</label>
                        <input id="metaTitle" type="text" name="metaTitle" value="<?php echo e(old('metaTitle', $blog->metaTitle ?? '')); ?>"
                            class="h-11 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm text-neutral-900 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white">
                        <?php $__errorArgs = ['metaTitle'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="metaKeyword" class="mb-1.5 block text-sm font-semibold text-neutral-700 dark:text-neutral-200">Meta keywords</label>
                        <input id="metaKeyword" type="text" name="metaKeyword" value="<?php echo e(old('metaKeyword', $blog->metaKeyword ?? '')); ?>"
                            class="h-11 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm text-neutral-900 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white">
                        <?php $__errorArgs = ['metaKeyword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="md:col-span-2">
                        <label for="metaDescription" class="mb-1.5 block text-sm font-semibold text-neutral-700 dark:text-neutral-200">Meta description</label>
                        <textarea id="metaDescription" name="metaDescription" rows="3"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"><?php echo e(old('metaDescription', $blog->metaDescription ?? '')); ?></textarea>
                        <?php $__errorArgs = ['metaDescription'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </section>

            <section aria-labelledby="blog-advanced-heading">
                <div class="mb-4 border-b border-neutral-200 pb-3 dark:border-neutral-700">
                    <h2 id="blog-advanced-heading" class="font-semibold text-neutral-900 dark:text-white">Advanced code</h2>
                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Optional markup or scripts inserted into the page head and body.</p>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label for="head" class="mb-1.5 block text-sm font-semibold text-neutral-700 dark:text-neutral-200">Head</label>
                        <textarea id="head" name="head" rows="6" spellcheck="false"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 font-mono text-sm text-neutral-900 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"><?php echo e(old('head', $blog->head ?? '')); ?></textarea>
                        <?php $__errorArgs = ['head'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="body" class="mb-1.5 block text-sm font-semibold text-neutral-700 dark:text-neutral-200">Body</label>
                        <textarea id="body" name="body" rows="6" spellcheck="false"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 font-mono text-sm text-neutral-900 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"><?php echo e(old('body', $blog->body ?? '')); ?></textarea>
                        <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </section>
        </div>
        <div class="flex items-center justify-end gap-3 border-t border-neutral-200 px-4 py-3 dark:border-neutral-700 sm:px-6">
            <a href="<?php echo e(route('super-admin.blog.index')); ?>"
                class="rounded-lg border border-neutral-300 px-4 py-2 text-sm font-semibold text-neutral-700 transition hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800">
                Cancel
            </a>
            <button type="submit"
                class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <?php echo e($isEditing ? 'Update Blog' : 'Create Blog'); ?>

            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/blog/add-edit.blade.php ENDPATH**/ ?>