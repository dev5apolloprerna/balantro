<?php $__env->startSection('content'); ?>
    <div class="lg:col-span-3">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Profile</h2>
                <p class="text-gray-600 dark:text-gray-400">Update your profile information</p>
            </div>
            <form method="POST" action="<?php echo e(route('profile.userProfileUpdate')); ?>" class="space-y-6"
                enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <!-- Profile Image Upload -->
                <div class="flex flex-col items-center mb-6">
                    <div class="relative w-32 h-32">
                        <?php
                            $defaultImage =
                                isset($profile->gender) && $profile->gender == 'female'
                                    ? 'images/female.png'
                                    : 'images/male.png';
                        ?>
                        <img src="<?php echo e(isset($profile->profile_image) && $profile->profile_image != '' ? asset($profile->profile_image) : asset($defaultImage)); ?>"
                            id="imagePreview"
                            class="w-32 h-32 rounded-full object-cover border-4 border-white dark:border-gray-700 shadow">

                        <div class="absolute bottom-0 right-0">
                            <input type="file" name="profile_image" id="imageUpload" accept=".png,.jpg,.jpeg"
                                class="hidden">
                            <label for="imageUpload"
                                class="w-10 h-10 flex justify-center items-center bg-blue-600 text-white border-2 border-white dark:border-gray-700 hover:bg-blue-700 rounded-full shadow-sm cursor-pointer transition-colors">
                                <iconify-icon icon="solar:camera-outline" class="text-xl"></iconify-icon>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Mobile No -->
                    <div>
                        <label for="mobile_no"
                            class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Name</label>
                        <input type="tel" name="name" id="name"
                            value="<?php echo e(old('name', auth()->user()->name ?? '')); ?>" placeholder="Enter Name"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender"
                            class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Gender</label>
                        <select name="gender" id="gender"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Select Gender</option>
                            <option value="male" <?php echo e(old('gender', $profile?->gender ?? '') == 'male' ? 'selected' : ''); ?>>
                                Male</option>
                            <option value="female"
                                <?php echo e(old('gender', $profile?->gender ?? '') == 'female' ? 'selected' : ''); ?>>Female
                            </option>
                            <option value="other"
                                <?php echo e(old('gender', $profile?->gender ?? '') == 'other' ? 'selected' : ''); ?>>
                                Other</option>
                        </select>
                    </div>

                    <!-- Mobile No -->
                    <div>
                        <label for="mobile_no"
                            class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Mobile
                            No</label>
                        <input type="tel" name="mobile_no" id="mobile_no" maxlength="10"
                            value="<?php echo e(old('mobile_no', $profile->mobile_no ?? '')); ?>" placeholder="9876543210"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)">
                    </div>

                    <!-- Whatsapp No -->
                    <div>
                        <label for="whatsapp_no"
                            class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Whatsapp
                            No</label>
                        <input type="tel" name="whatsapp_no" id="whatsapp_no" maxlength="10"
                            value="<?php echo e(old('whatsapp_no', $profile->whatsapp_no ?? '')); ?>" placeholder="9876543210"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)">
                    </div>

                    <!-- Address - Fixed spacing issue -->
                    <div class="md:col-span-2 mt-4">
                        <label for="address"
                            class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Address</label>
                        <textarea name="address" id="address" rows="4" placeholder="Enter your complete address"
                            class="w-full px-4 py-4 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-vertical leading-relaxed"><?php echo e(old('address', $profile->address ?? '')); ?></textarea>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="<?php echo e(route('home')); ?>"
                        class="px-6 py-3 rounded-lg border border-red-600 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors font-medium">Cancel</a>
                    <button type="submit"
                        class="px-6 py-3 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors font-medium shadow-sm">Save
                        Changes</button>
                </div>
            </form>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            document.getElementById('imageUpload').addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('imagePreview').src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\profiles\userProfileEdit.blade.php ENDPATH**/ ?>