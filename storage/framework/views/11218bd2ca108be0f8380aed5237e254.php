
<?php $__env->startSection('content'); ?>
<div data-controller="confirm-delete" x-data="{ openUpload:false }">
    <div class="container mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h6 class="font-semibold mb-0 dark:text-white"><?php echo e(__("Sales")); ?></h6>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-12">
            <div class="col-span-12">
                <div class="card !border-0 rounded-lg overflow-hidden bg-white dark:bg-neutral-800">
                    <div class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 p-4 sm:p-6">
                        <div class="flex flex-col gap-3">
                            <div class="flex flex-col sm:flex-row gap-3 w-full">
                                <div class="flex flex-col sm:flex-row justify-between items-center w-full">

                                    <!-- Left Side Tabs -->
                                    <div class="flex items-center bg-gray-200 dark:bg-neutral-900 rounded-lg p-1 w-fit">

                                        <!-- Active Tab -->
                                        <a href="<?php echo e(route('data_entry_operators.bulkuploadsales')); ?>"
                                            class="px-2 py-1 text-sm font-medium rounded-md
                                            text-gray-600 dark:text-gray-300
                                            hover:bg-white dark:hover:bg-neutral-700
                                            transition">
                                            Sales
                                        </a>

                                        <!-- Purchase -->
                                        <a href="<?php echo e(route('data_entry_operators.bulkuploadpurchase')); ?>"
                                            class="px-2 py-1 text-sm font-medium rounded-md
                                            text-gray-600 dark:text-gray-300
                                            hover:bg-white dark:hover:bg-neutral-700
                                            transition">
                                            Purchase
                                        </a>

                                        <!-- Bank -->
                                        <a href="<?php echo e(route('data_entry_operators.bulkuploadbankstatement')); ?>"
                                            class="px-2 py-1 text-sm font-medium rounded-md
                                            bg-white dark:bg-neutral-700
                                            text-gray-800 dark:text-white
                                            shadow-sm transition">
                                            Bank
                                        </a>

                                    </div>

                                    <!-- Right Side Actions -->
                                    <div class="flex items-center gap-3 mt-3 sm:mt-0">

                                        <!-- Upload Button -->
                                        <button
                                            @click="openUpload = true"
                                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-md flex items-center gap-2">

                                            <i class="fa-solid fa-upload"></i>
                                            Import Statement

                                        </button>

                                    </div>

                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left text-gray-600 dark:text-gray-200">

                            <!-- Table Header -->
                            <thead class="bg-gray-200 dark:bg-neutral-700 text-gray-600 dark:text-gray-200 text-xs uppercase">
                                <tr>

                                    <th class="px-4 py-3">
                                        <input type="checkbox">
                                    </th>

                                    <th class="px-4 py-3">Sr.No</th>

                                    <th class="px-4 py-3">File Name</th>

                                    <th class="px-4 py-3">Bank Name</th>

                                    <th class="px-4 py-3">Statement Date</th>

                                    <th class="px-4 py-3">Synced Date</th>

                                    <th class="px-4 py-3 text-center">Total</th>

                                    <th class="px-4 py-3 text-center">Pending</th>

                                    <th class="px-4 py-3 text-center">Saved</th>

                                    <th class="px-4 py-3 text-center">Synced</th>

                                    <th class="px-4 py-3 text-center">Suggestion</th>

                                    <th class="px-4 py-3 text-center">Status</th>

                                    <th class="px-4 py-3 text-right">Action</th>

                                </tr>
                            </thead>

                            <!-- Table Body -->
                            <tbody class="divide-y">

                                <!-- Row 1 -->
                                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700">

                                    <td class="px-4 py-3">
                                        <input type="checkbox">
                                    </td>

                                    <td class="px-4 py-3">1</td>

                                    <td class="px-4 py-3 font-medium text-gray-700 dark:text-gray-200">
                                        BoB-1706336530764.pdf
                                    </td>

                                    <td class="px-4 py-3">
                                        Bank Of Baroda
                                    </td>

                                    <td class="px-4 py-3">
                                        02 Feb 2020 - 12 Jan 2021
                                    </td>

                                    <td class="px-4 py-3">
                                        -
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        52
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        52
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        0
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        0
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        0
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <a href="<?php echo e(route('data_entry_operators.bulkuploadbankingcompletelist')); ?>" class="text-green-600 font-semibold">
                                            <span class="text-green-600 font-semibold">Complete</span>
                                        </a>
                                        
                                    </td>

                                    <td class="px-4 py-3 text-right flex justify-end gap-3">

                                        <i class="fa-solid fa-file-pdf text-red-500 cursor-pointer"></i>

                                        <i class="fa-solid fa-trash text-red-500 cursor-pointer"></i>

                                    </td>

                                </tr>



                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div x-show="openUpload"
        x-transition
        class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">

        <div class="bg-white dark:bg-neutral-800 w-[650px] rounded-lg shadow-xl">

            <!-- Header -->
            <div class="flex justify-between items-center border-b border-gray-200 dark:border-neutral-700 px-5 py-3">

                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                    Upload Banking
                </h2>

                <button @click="openUpload=false" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>

            </div>


            <!-- Body -->
            <div class="p-6 space-y-5">

                <!-- Select Bank -->
                <div>

                    <label class="text-sm font-medium text-gray-600 dark:text-gray-300">
                        * Select Bank
                    </label>

                    <select class="mt-1 w-full border border-gray-300 dark:border-neutral-600 rounded-md px-3 py-2 bg-white dark:bg-neutral-700 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500">

                        <option>Search to Select</option>

                    </select>

                </div>


                <!-- Upload Box -->
                <div class="border-2 border-dashed border-blue-400 dark:border-blue-500 rounded-lg p-8 text-center">

                    <i class="fa-regular fa-file text-3xl text-blue-500 mb-3"></i>

                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-3">
                        Drag and drop a file here or
                    </p>

                    <input type="file"
                        id="bankUpload"
                        class="hidden">

                    <button onclick="document.getElementById('bankUpload').click()"
                        class="border border-blue-500 text-blue-600 px-4 py-2 rounded-md text-sm flex items-center gap-2 mx-auto">

                        <i class="fa-solid fa-upload"></i>
                        Click to upload

                    </button>

                </div>


                <!-- Notes -->
                <div>

                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-2">
                        Notes:
                    </p>

                    <div class="grid grid-cols-2 gap-3 text-xs">

                        <div class="bg-gray-100 dark:bg-neutral-700 p-3 rounded flex gap-2 items-start">

                            <i class="fa-solid fa-circle-xmark text-red-500 mt-1"></i>

                            <span>
                                Please don't upload Passbook, Share Market statement and Loan statement.
                            </span>

                        </div>


                        <div class="bg-gray-100 dark:bg-neutral-700 p-3 rounded flex gap-2 items-start">

                            <i class="fa-solid fa-circle-xmark text-red-500 mt-1"></i>

                            <span>
                                Please don't upload password protected, RTP and TEXT format files.
                            </span>

                        </div>

                    </div>

                </div>


                <!-- File Processing Info -->
                <div class="grid grid-cols-3 gap-3 text-center text-sm">

                    <div class="bg-gray-100 dark:bg-neutral-700 rounded p-3">

                        <p class="font-medium text-green-600">Excel</p>

                        <p class="text-xs text-gray-500 mt-1">Upto 30 min</p>

                    </div>

                    <div class="bg-gray-100 dark:bg-neutral-700 rounded p-3">

                        <p class="font-medium text-red-600">Original PDF</p>

                        <p class="text-xs text-gray-500 mt-1">Upto 1 Hour</p>

                    </div>

                    <div class="bg-gray-100 dark:bg-neutral-700 rounded p-3">

                        <p class="font-medium text-red-600">Scanned PDF</p>

                        <p class="text-xs text-gray-500 mt-1">Upto 12 Hours</p>

                    </div>

                </div>

            </div>


            <!-- Footer -->
            <div class="flex justify-end gap-3 border-t border-gray-200 dark:border-neutral-700 px-5 py-3">

                <button @click="openUpload=false"
                    class="px-4 py-2 border rounded-md text-gray-600 dark:text-gray-300">

                    Cancel

                </button>

                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">

                    Upload

                </button>

            </div>

        </div>
    </div>

    <script>
        function showFileName(event) {
            const file = event.target.files[0];
            if (file) {
                document.getElementById("fileNameText").innerText = file.name;
                document.getElementById("uploadedFileName").classList.remove("hidden");
            }
        }
    </script>
    <script>
        $(document).ready(function() {

            $('#sampleDownload').change(function() {

                let type = $(this).val();

                if (type == 'with-item') {
                    window.location.href = "/samples/Sales-with-item-sample-file.xlsx";
                }

                if (type == 'without-item') {
                    window.location.href = "/samples/Sales-without-item-sample-file.xlsx";
                }

                // reset dropdown
                $(this).val('');

            });

        });
    </script>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\data_entry_operators\bulkuploadbankstatement.blade.php ENDPATH**/ ?>