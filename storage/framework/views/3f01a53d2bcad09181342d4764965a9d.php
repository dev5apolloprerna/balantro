

<?php $__env->startSection('content'); ?>

<div class="p-6 shadow ">

    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">
        Master Documents
    </h2>

    <?php if($errors->any()): ?>
        <div class="mb-4 p-3 bg-red-500 text-white rounded">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('profile.documents.upload')); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- PAN -->
            <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                    PAN Card
                </label>

                <div class="flex items-center gap-3 file-input-wrapper">
                    <label class="cursor-pointer px-4 py-2 rounded-lg 
                            border border-gray-300 dark:border-gray-600 
                            bg-white dark:bg-black 
                            text-gray-700 dark:text-gray-200 
                            hover:bg-gray-100 dark:hover:bg-gray-900">
                        Choose File
                        <input type="file" name="pan_card_file" class="hidden file-input">
                    </label>

                    <span class="text-sm text-gray-500 dark:text-gray-400 file-name">
                        No file selected
                    </span>
                </div>

                <?php if($profile->pan_card_file): ?>
                    <a href="<?php echo e(route('profile.documents.download', 'pan')); ?>"
                        class="inline-block mt-2 px-3 py-1 text-sm rounded-md 
                               bg-blue-100 dark:bg-blue-900/20 
                               text-blue-600 dark:text-blue-400 
                               hover:shadow-[0_0_10px_#3b82f6]">
                        Download PAN Card
                    </a>
                <?php endif; ?>
            </div>

            <!-- GST -->
            <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                    GST Certificate
                </label>

                <div class="flex items-center gap-3 file-input-wrapper">
                    <label class="cursor-pointer px-4 py-2 rounded-lg 
                        border border-gray-300 dark:border-gray-600 
                        bg-white dark:bg-black 
                        text-gray-700 dark:text-gray-200 
                        hover:bg-gray-100 dark:hover:bg-gray-900">
                        Choose File
                        <input type="file" name="gst_certificate_file" class="hidden file-input">
                    </label>

                    <span class="text-sm text-gray-500 dark:text-gray-400 file-name">
                        No file selected
                    </span>
                </div>

                <?php if($profile->gst_certificate_file): ?>
                    <a href="<?php echo e(route('profile.documents.download', 'gst')); ?>"
                        class="inline-block mt-2 px-3 py-1 text-sm rounded-md 
                               bg-blue-100 dark:bg-blue-900/20 
                               text-blue-600 dark:text-blue-400 
                               hover:shadow-[0_0_10px_#3b82f6]">
                        Download GST Certificate
                    </a>
                <?php endif; ?>
            </div>

        </div>

        <div class="flex justify-end gap-3 mt-8">
            <button type="submit"
                class="rounded-md border border-gray-700 text-black dark:text-white  px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#22d3ee]
                                hover:shadow-[0_0_15px_#22d3ee]
                                hover:scale-105
                                hover:-translate-y-1"
                                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">
                Save
            </button>

            <a href="<?php echo e(route('home')); ?>"
                class="rounded-md border border-gray-700 text-black dark:text-white px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#a78bfa]
                                hover:shadow-[0_0_15px_#a78bfa]
                                hover:scale-105
                                hover:-translate-y-1"
                                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">
                Cancel
            </a>

            

        </div>
    </form>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script>
const allowedExtensions = ['jpg','jpeg','png','pdf','heic','heif'];
const maxSize = 5 * 1024 * 1024; // 5MB

function validateFile(file) {
    if (!file) return true;

    const ext = file.name.split('.').pop().toLowerCase();

    if (!allowedExtensions.includes(ext)) {
        return { valid: false, message: 'Invalid file type' };
    }

    if (file.size > maxSize) {
        return { valid: false, message: 'Max file size is 5MB' };
    }

    return { valid: true };
}

let hasError = false;

document.querySelectorAll('.file-input').forEach(input => {
    input.addEventListener('change', function () {
        const file = this.files[0];
        const wrapper = this.closest('.file-input-wrapper');
        const fileNameEl = wrapper.querySelector('.file-name');

        fileNameEl.classList.remove('text-red-500');

        if (!file) {
            fileNameEl.innerText = 'No file selected';
            return;
        }

        const result = validateFile(file);

        if (!result.valid) {
            fileNameEl.innerText = result.message;
            fileNameEl.classList.add('text-red-500');
            this.dataset.invalid = "1";   // 👈 mark invalid
            hasError = true;
            return;
        }

        this.dataset.invalid = "0";
        fileNameEl.innerText = file.name;
    });
});

document.querySelector('form').addEventListener('submit', function(e) {

    let hasInvalid = false;

    document.querySelectorAll('.file-input').forEach(input => {
        if (input.dataset.invalid === "1") {
            hasInvalid = true;
        }
    });

    if (hasInvalid) {
        e.preventDefault();
        alert("Please fix invalid files before submitting.");
        return false;
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\profiles\documents.blade.php ENDPATH**/ ?>