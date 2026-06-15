<div class="lg:col-span-3">
    <div class="shadow p-3">
        <div class="flex items-center justify-between gap-3">
            <div class="mb-3">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Profile</h2>
                <p class="text-gray-600 dark:text-gray-400">Update your profile information</p>
            </div>
            <div>
                <a href="<?php echo e(url()->previous()); ?>" title="Go Back" class="group btn inline-block relative text-black dark:text-white px-4 py-2 text-sm rounded-md border border-gray-700
                                    transition duration-1000 ease-in-out
                                    transition-property: all;
                                    hover:border-[#f472b6]
                                    hover:shadow-[0_0_15px_#f472b6]
                                    hover:scale-105
                                    hover:-translate-y-1" style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">
                        <i class="fa-solid fa-arrow-left mr-1"></i>
                    </a>
            </div>
        </div>
        <style>
            .erp-input {
                @apply w-full h-11 px-4 rounded-xl bg-white/5 border border-cyan-400/20 text-white placeholder-gray-500 transition-all duration-300;
            }

            .erp-input:focus {
                @apply outline-none border-cyan-400 ring-1 ring-cyan-400/20;
            }

            .erp-label {
                @apply text-sm font-medium text-gray-300;
            }

            .erp-card {
                @apply rounded-2xl border border-cyan-400/10 bg-white/5 backdrop-blur-xl p-6;
            }

            .erp-title {
                @apply text-xl font-semibold text-cyan-400 border-b border-cyan-400/20 pb-3 mb-6;
            }
        </style>
        <form method="POST" action="<?php echo e(route('profile.update', $profile)); ?>" class="space-y-3"
            enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <?php
            $profile = auth()->user()->profile;
            ?>

            <!-- Profile Image Upload -->
            <div class="flex flex-col items-center mb-6">
                <div class="relative w-32 h-32">
                    <?php
                    $defaultImage = $profile->gender == 'female' ? 'images/female.png' : 'images/male.png';
                    ?>
                    <img src="<?php echo e($profile->profile_image ? asset($profile->profile_image) : asset($defaultImage)); ?>"
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
            <div class="space-y-8">

                <div class="rounded-2xl border border-cyan-400/10 bg-white/5 backdrop-blur-xl p-6 relative z-10">
                    <h3 class="text-xl font-semibold text-cyan-400 border-b border-cyan-400/20 pb-3 mb-6">
                        Basic Details
                    </h3>

                    <div class="relative grid grid-cols-12 items-center gap-4 mb-5"
                            x-data="{
                            open: false,
                            selected: '<?php echo e(old('business_type', $profile->business_type) ?? ''); ?>',
                            options: {
                                '': 'Select Business Type',
                                'individual': 'Individual/Proprietor',
                                'partnership': 'Partnership Firm',
                                'llc': 'LLC',
                                'huf': 'HUF',
                                'private_limited': 'Private Limited',
                                'limited_company': 'Limited Company', 
                                'one_person_company': 'One Person Company',
                                'aop': 'AOP',
                                'trust': 'Trust',
                                'society': 'Society',
                                'other': 'Other'
                            }
                        }">

                        <!-- Label -->
                        <label class="col-span-12 md:col-span-4 erp-label block text-sm font-semibold text-gray-700 dark:text-gray-200 ">
                            Constitution of Business
                        </label>

                        <!-- Hidden input (IMPORTANT for form submit) -->
                        <input type="hidden" name="business_type" :value="selected">
                        <div class="col-span-12 md:col-span-8 relative">
                            <!-- Button -->
                            <button type="button" @click="open = !open"
                                class="w-full text-left
                                        bg-white
                                        dark:bg-black
                                        border border-gray-300 dark:border-cyan-400/30
                                        text-gray-900 dark:text-white
                                        rounded-xl px-3 py-2 pr-10 text-sm
                                        focus:outline-none
                                        focus:ring-2 focus:ring-cyan-400">

                                <span x-text="options[selected]"></span>
                            </button>

                            <!-- Arrow -->
                            <div class="pointer-events-none absolute right-3 top-[18px] -translate-y-1/2 text-gray-500 dark:text-gray-300">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>

                            <!-- Dropdown -->
                            <ul x-show="open"
                                @click.outside="open = false"
                                x-transition
                                class="absolute left-0 right-0 top-full z-[9999] mt-2
                                max-h-64 overflow-y-auto
                                rounded-xl
                                bg-white dark:bg-black
                                border border-gray-300 dark:border-cyan-400/30
                                shadow-[0_10px_30px_rgba(0,0,0,0.8)]">

                                <!-- Loop options -->
                                <template x-for="(label, key) in options" :key="key">
                                    <li>
                                        <button type="button"
                                            @click="selected = key; open = false"
                                            class="w-full text-left px-4 py-3 text-sm
                                                text-gray-800 dark:text-white
                                                hover:bg-cyan-50 dark:hover:bg-cyan-500/20"
                                                
                                                :class="selected === key
                                                ? 'bg-cyan-500/20 text-cyan-400'
                                                : ''">

                                            <span x-text="label"></span>
                                        </button>
                                    </li>
                                </template>

                            </ul>
                        </div>
                    </div>

                    <!-- Name No -->
                    <div class="grid grid-cols-12 items-center gap-4 mb-5">
                        <label for="name" class="col-span-12 md:col-span-4 erp-label block text-sm font-semibold text-gray-700 dark:text-gray-200 ">Legal Name</label>
                        <div class="col-span-12 md:col-span-8">
                            <input type="text" name="name" id="name" value="<?php echo e(old('name', auth()->user()->name ?? '')); ?>"
                                placeholder="Enter Name"
                                class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 items-center gap-4 mb-5">
                        <label for="trade_name" class="col-span-12 md:col-span-4 erp-label block text-sm font-semibold text-gray-700 dark:text-gray-200 ">Trade Name</label>
                        <div class="col-span-12 md:col-span-8">
                            <input type="text" name="trade_name" id="trade_name" value="<?php echo e(old('trade_name', auth()->user()->trade_name ?? '')); ?>"
                                placeholder="Enter Trade Name"
                                class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 items-center gap-4 mb-5">
                        <label for="short_name" class="col-span-12 md:col-span-4 erp-label block text-sm font-semibold text-gray-700 dark:text-gray-200 ">Short Name</label>
                        <div class="col-span-12 md:col-span-8">
                            <input type="text" name="short_name" id="short_name" value="<?php echo e(old('short_name', auth()->user()->short_name ?? '')); ?>"
                                placeholder="Enter Short Name"
                                class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                    </div>                    
                </div>

                <div class="rounded-2xl border border-cyan-400/10 bg-white/5 backdrop-blur-xl p-6">
                    <h3 class="text-xl font-semibold text-cyan-400 border-b border-cyan-400/20 pb-3 mb-6">
                        Contact Details
                    </h3>
                    <!-- Email  -->
                    <div class="grid grid-cols-12 items-center gap-4 mb-5">
                        <label for="email"
                            class="col-span-12 md:col-span-4 erp-label block text-sm font-semibold text-gray-700 dark:text-gray-200 ">Email</label>
                        <div class="col-span-12 md:col-span-8">
                            <input type="email" readonly name="email" id="email" value="<?php echo e(old('email', auth()->user()->email ?? '')); ?>"
                                placeholder="Enter Email"
                                class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">

                        </div>
                    </div>

                    <div class="grid grid-cols-12 items-center gap-4 mb-5">
                        <label for="email"
                            class="col-span-12 md:col-span-4 erp-label block text-sm font-semibold text-gray-700 dark:text-gray-200 ">Alternative Email</label>
                        <div class="col-span-12 md:col-span-8">
                            <input type="email" name="alternative_email" id="alternative_email" value="<?php echo e(old('alternative_email', $profile->alternative_email ?? '')); ?>"
                                placeholder="Enter Alternative Email"
                                class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                    </div>

                    <!-- Mobile No -->
                    <div class="grid grid-cols-12 items-center gap-4 mb-5">
                        <label for="mobile_no"
                            class="col-span-12 md:col-span-4 erp-label block text-sm font-semibold text-gray-700 dark:text-gray-200 ">Mobile
                            No</label>
                        <div class="col-span-12 md:col-span-8">
                            <input type="tel" name="mobile_no" id="mobile_no" maxlength="10"
                                value="<?php echo e(old('mobile_no', $profile->mobile_no)); ?>" placeholder="9876543210"
                                class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)">
                        </div>
                    </div>

                    <!-- Whatsapp No -->
                    <div class="grid grid-cols-12 items-center gap-4 mb-5">
                        <label for="whatsapp_no"
                            class="col-span-12 md:col-span-4 erp-label block text-sm font-semibold text-gray-700 dark:text-gray-200 ">Whatsapp
                            No</label>
                        <div class="col-span-12 md:col-span-8">
                            <input type="tel" name="whatsapp_no" id="whatsapp_no" maxlength="10"
                                value="<?php echo e(old('whatsapp_no', $profile->whatsapp_no)); ?>" placeholder="9876543210"
                                class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)">
                        </div>
                    </div>

                    <!-- Address - Fixed spacing issue -->
                    <div class="grid grid-cols-12 items-center gap-4 mb-5">
                        <label for="address"
                            class="col-span-12 md:col-span-4 erp-label block text-sm font-semibold text-gray-700 dark:text-gray-200 ">Address Line 1</label>
                        <div class="col-span-12 md:col-span-8">
                            <input name="address" id="address" placeholder="Enter your complete address" value="<?php echo e(old('address', $profile->address)); ?>"
                                class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                    </div>
                    <div class="grid grid-cols-12 items-center gap-4 mb-5">
                        <label for="address"
                            class="col-span-12 md:col-span-4 erp-label block text-sm font-semibold text-gray-700 dark:text-gray-200 ">Address Line 2</label>
                        <div class="col-span-12 md:col-span-8">
                            <input name="address_2" id="address_2" placeholder="Enter your complete address" value="<?php echo e(old('address', $profile->address_2)); ?>"
                                class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                    </div>
                    <div class="grid grid-cols-12 items-center gap-4 mb-5">
                        <label for="pincode"
                            class="col-span-12 md:col-span-4 erp-label block text-sm font-semibold text-gray-700 dark:text-gray-200 ">Pin code </label>
                        <div class="col-span-12 md:col-span-8">
                            <input name="pincode" id="pincode" placeholder="Enter your Pin code" value="<?php echo e(old('pincode', $profile->pincode)); ?>"
                                class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                    </div>
                    <div class="grid grid-cols-12 items-center gap-4 mb-5">
                        <label for="city"
                            class="col-span-12 md:col-span-4 erp-label block text-sm font-semibold text-gray-700 dark:text-gray-200 ">City </label>
                        <div class="col-span-12 md:col-span-8">
                            <input
                                type="text"
                                name="city_name"
                                id="city_name"
                                list="cityList"
                                placeholder="Type City Name"
                                 value="<?php echo e(optional($cities->where('id',$profile->city)->first())->city_name); ?>"
                                class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">

                            <input type="hidden" name="city" id="city" value="<?php echo e($profile->city); ?>">

                            <datalist id="cityList">
                                <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option
                                        data-id="<?php echo e($city->id); ?>"
                                        value="<?php echo e($city->city_name); ?>">
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </datalist>
                            <!-- <select name="city" id="city" class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Select City</option>
                                <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?= $city->id ?>" data-city-name="<?= strtolower(trim($city->city_name)) ?>" data-state-id="<?= $city->state_id ?>" <?php echo e(old('city', $profile->city) == $city->id ? 'selected' : ''); ?>><?=  $city->city_name ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select> -->
                        </div>
                    </div>
                    <div class="grid grid-cols-12 items-center gap-4 mb-5">
                        <label for="district"
                            class="col-span-12 md:col-span-4 erp-label block text-sm font-semibold text-gray-700 dark:text-gray-200 ">District </label>
                        <div class="col-span-12 md:col-span-8">
                            <input
                                type="text"
                                name="district_name"
                                id="district_name"
                                list="districtList"
                                placeholder="Type District Name"
                                value="<?php echo e(optional($districts->where('district_id',$profile->district)->first())->district_name); ?>"
                                class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">

                            <input type="hidden" name="district" id="district" value="<?php echo e($profile->district); ?>">

                            <datalist id="districtList">
                                <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option
                                        data-id="<?php echo e($district->district_id); ?>"
                                        value="<?php echo e($district->district_name); ?>">
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </datalist>
                            <!-- <input name="district" id="district" placeholder="Enter your District" value="<?php echo e(old('district', $profile->district)); ?>"
                                class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"> -->
                            <!-- <select name="district" id="district"
                                class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Select District</option>
                                <?php if(old('district', $profile->district)): ?>
                                    <option value="<?php echo e(old('district', $profile->district)); ?>" selected><?php echo e(old('district', $profile->district)); ?></option>
                                <?php endif; ?>
                            </select> -->
                        </div>
                    </div>
                    <div class="grid grid-cols-12 items-center gap-4 mb-5">
                        <label for="state"
                            class="col-span-12 md:col-span-4 erp-label block text-sm font-semibold text-gray-700 dark:text-gray-200 ">State </label>
                        <div class="col-span-12 md:col-span-8">
                            <input
                                type="text"
                                name="state_name"
                                id="state_name"
                                list="stateList"
                                placeholder="Type State Name"
                                value="<?php echo e(optional($states->where('stateId',$profile->state)->first())->stateName); ?>"
                                class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">

                            <input type="hidden" name="state" id="state" value="<?php echo e($profile->state); ?>">

                            <datalist id="stateList">
                                <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option
                                        data-id="<?php echo e($state->stateId); ?>"
                                        value="<?php echo e($state->stateName); ?>">
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </datalist>
                            <!-- <select name="state" id="state" class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Select State</option>
                                <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?= $state->stateId ?>" data-state-name="<?= strtolower(trim($state->stateName)) ?>" <?php echo e(old('state', $profile->state) == $state->stateId ? 'selected' : ''); ?>><?=  $state->stateName ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select> -->
                            <!-- <input name="state" id="state" placeholder="Enter your State" value="<?php echo e(old('state', $profile->state)); ?>"
                                class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"> -->
                        </div>
                    </div>
                    
                </div>

                <div class="rounded-2xl border border-cyan-400/10 bg-white/5 backdrop-blur-xl p-6">

                    <h3 class="text-xl font-semibold text-cyan-400 border-b border-cyan-400/20 pb-3 mb-6">
                        Statutory Details
                    </h3>

                    <div class="grid grid-cols-12 items-center gap-4 mb-5">
                        <label for="TAN_no" class="col-span-12 md:col-span-4 erp-label block text-sm font-semibold text-gray-700 dark:text-gray-200 ">TAN No</label>
                        <div class="col-span-12 md:col-span-8">
                            <input type="text" name="TAN_no" id="TAN_no" value="<?php echo e(old('TAN_no', $profile->TAN_no ?? '')); ?>"
                                placeholder="Enter TAN No"
                                class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                    </div>


                    <!-- PAN No -->
                    <div class="grid grid-cols-12 items-center gap-4 mb-5">
                        <label for="pan_no" class="col-span-12 md:col-span-4 erp-label block text-sm font-semibold text-gray-700 dark:text-gray-200 ">PAN
                            No</label>
                        <div class="col-span-12 md:col-span-8">
                            <input type="text" name="pan_no" id="pan_no" value="<?php echo e(old('pan_no', $profile->pan_no)); ?>"
                                placeholder="ABCDE1234F"
                                class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                    </div>

                    <!-- GST No -->
                    <div class="grid grid-cols-12 items-center gap-4 mb-5">
                        <label for="gst_no" class="col-span-12 md:col-span-4 erp-label block text-sm font-semibold text-gray-700 dark:text-gray-200 ">GST
                            No</label>
                        <div class="col-span-12 md:col-span-8">
                            <input type="text" name="gst_no" id="gst_no" value="<?php echo e(old('gst_no', $profile->gst_no)); ?>"
                                placeholder="27ABCDE1234F1Z4"
                                class="outline-none border-cyan-400 ring-1 ring-cyan-400/20 w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                    </div>
                </div>

            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit"
                    class="rounded-md border border-gray-700 text-black dark:text-white  px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#22d3ee]
                                hover:shadow-[0_0_15px_#22d3ee]
                                hover:scale-105
                                hover:-translate-y-1"
                    style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">Save
                </button>
                <a href="<?php echo e(route('profile.show', $profile)); ?>"
                    class="rounded-md border border-gray-700 text-black dark:text-white px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#a78bfa]
                                hover:shadow-[0_0_15px_#a78bfa]
                                hover:scale-105
                                hover:-translate-y-1"
                    style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">Cancel</a>

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
    
    function bindHiddenId(inputId, listId, hiddenId) {
        const input = document.getElementById(inputId);
        const hidden = document.getElementById(hiddenId);

        input.addEventListener('change', function () {
            const option = [...document.querySelectorAll(`#${listId} option`)]
                .find(o => o.value === this.value);

            hidden.value = option ? option.dataset.id : '';
        });
    }

    bindHiddenId('state_name', 'stateList', 'state');
    bindHiddenId('district_name', 'districtList', 'district');
    bindHiddenId('city_name', 'cityList', 'city');
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pincodeInput = document.getElementById('pincode');
        const cityInput = document.getElementById('city_name');
        const cityHidden = document.getElementById('city');

        const districtInput = document.getElementById('district_name');
        const districtHidden = document.getElementById('district');

        const stateInput = document.getElementById('state_name');
        const stateHidden = document.getElementById('state');

       const normalizeName = (value) => String(value || '').trim().toLowerCase();
        // const initialSelectedCity = String(citySelect?.value || '').trim();
        // const allCityOptions = Array.from(citySelect.options)
        //     .filter((option) => option.value !== '')
        //     .map((option) => ({
        //         value: option.value,
        //         label: option.textContent.trim(),
        //         cityName: normalizeName(option.dataset.cityName || option.textContent),
        //         stateId: String(option.dataset.stateId || '').trim(),
        //     }));

        const isCityMatch = (cityName, pincodeName) => {
            if (!cityName || !pincodeName) {
                return false;
            }

            if (cityName === pincodeName) {
                return true;
            }

            return cityName.includes(pincodeName) || pincodeName.includes(cityName);
        };
            
        const setOptions = (select, values, placeholder) => {
            select.innerHTML = `<option value="">${placeholder}</option>`;
            const uniqueValues = [...new Map(
                (values || [])
                    .map((value) => String(value || '').trim())
                    .filter(Boolean)
                    .map((value) => [value.toLowerCase(), value])
            ).values()];

            uniqueValues.forEach((value) => {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = value;
                select.appendChild(option);
            });

            if (uniqueValues.length === 1) {
                select.value = uniqueValues[0];
            }
        };

        // const bindCityDropdown = (pincodeCities = []) => {
        //     const stateId = String(stateSelect.value || '').trim();
        //     const normalizedPincodeCities = (pincodeCities || []).map(normalizeName).filter(Boolean);
        //     const pincodeCitySet = new Set(normalizedPincodeCities);

        //     const stateCities = allCityOptions.filter((city) => city.stateId === stateId);

        //     citySelect.innerHTML = '<option value="">Select City</option>';

        //     stateCities.forEach((city) => {
        //         const option = document.createElement('option');
        //         option.value = city.value;
        //         option.textContent = city.label;
        //         option.dataset.cityName = city.cityName;
        //         option.dataset.stateId = city.stateId;
        //         citySelect.appendChild(option);
        //     });

        //     const matchedCity = stateCities.find((city) => pincodeCitySet.has(city.cityName));

        //     if (matchedCity) {
        //         citySelect.value = matchedCity.value;
        //     }
        // };

        // const selectState = (stateId, stateName) => {
        //     if (stateId) {
        //         stateSelect.value = String(stateId);
        //         return;
        //     }

        //     if (!stateName) {
        //         return;
        //     }

        //     const normalizedStateName = stateName.trim().toLowerCase();
        //     const matchingOption = Array.from(stateSelect.options).find((option) => {
        //         return option.dataset.stateName === normalizedStateName;
        //     });

        //     if (matchingOption) {
        //         stateSelect.value = matchingOption.value;
        //     }
        // };

        const fetchPincodeDetails = async (pincode) => {
            try {
                const response = await fetch(`<?php echo e(route('profile.pincode-details')); ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        pincode: pincode
                    })
                });

                const data = await response.json();
                // if (!response.ok) return;

                // setSingleOption(citySelect, data.city, 'Select City');
                // setSingleOption(districtSelect, data.district, 'Select District');

                // if (data.state_id) {
                //     stateSelect.value = data.state_id;
                if (!response.ok) {
                    return;
                }

                setDatalistValue(
                    'state_name',
                    'state',
                    'stateList',
                    data.state_name
                );

                if (data.districts?.length) {
                    setDatalistValue(
                        'district_name',
                        'district',
                        'districtList',
                        data.districts[0]
                    );
                }

                if (data.cities?.length) {
                    setDatalistValue(
                        'city_name',
                        'city',
                        'cityList',
                        data.cities[0]
                    );
                }
            } catch (error) {
                console.error('Failed to fetch pincode details', error);
            }
        };

        // stateSelect?.addEventListener('change', function() {
        //     // bindCityDropdown();
        // });

        //pincodeInput?.addEventListener('input', function() {
        if (pincodeInput) {
            pincodeInput.addEventListener('input', function() {
                const pincode = this.value.replace(/\D/g, '').slice(0, 6);
                this.value = pincode;

                if (pincode.length === 6) {
                    fetchPincodeDetails(pincode);
                }
            });
        }
    });

    function setDatalistValue(inputId, hiddenId, listId, textValue) {

        const input = document.getElementById(inputId);
        const hidden = document.getElementById(hiddenId);

        input.value = textValue || '';

        const option = [...document.querySelectorAll(`#${listId} option`)]
            .find(o => o.value.toLowerCase() === String(textValue).toLowerCase());

        hidden.value = option ? option.dataset.id : '';
    }
</script>
<?php $__env->stopPush(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\profiles\_form.blade.php ENDPATH**/ ?>