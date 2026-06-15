@extends('layouts.super_admin')
@section('content')
<div data-controller="confirm-delete" x-data="{ openUpload:false }">
    <div class="container mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h6 class="font-semibold mb-0 dark:text-white">{{ __("Purchase") }}</h6>
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
                                        <a href="{{ route('data_entry_operators.bulkuploadsales') }}"
                                            class="px-2 py-1 text-sm font-medium rounded-md
                                            text-gray-600 dark:text-gray-300
                                            hover:bg-white dark:hover:bg-neutral-700
                                            transition">
                                            Sales
                                        </a>

                                        <!-- Purchase -->
                                        <a href="{{ route('data_entry_operators.bulkuploadpurchase') }}"
                                            class="px-2 py-1 text-sm font-medium rounded-md
                                            bg-white dark:bg-neutral-700
                                            text-gray-800 dark:text-white
                                            shadow-sm transitionn">
                                            Purchase
                                        </a>

                                        <!-- Bank -->
                                        <a href="{{ route('data_entry_operators.bulkuploadbankstatement') }}"
                                            class="px-2 py-1 text-sm font-medium rounded-md
                                            text-gray-600 dark:text-gray-300
                                            hover:bg-white dark:hover:bg-neutral-700
                                            transition">
                                            Bank
                                        </a>

                                    </div>

                                    <!-- Right Side Actions -->
                                    <div class="flex items-center gap-3 mt-3 sm:mt-0">

                                        <!-- Dropdown -->
                                        <select id="sampleDownload"
                                            class="border border-gray-300 dark:border-neutral-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">Download sample</option>
                                            <option value="with-item">Item Invoice (With Item)</option>
                                            <option value="without-item">Accounting Invoice (Without Item)</option>
                                        </select>

                                        <!-- Upload Button -->
                                        <button
                                            @click="openUpload = true"
                                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-md flex items-center gap-2">

                                            <i class="fa-solid fa-upload"></i>
                                            Upload

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
                                <tr >
                                    <th class="px-4 py-3">
                                        <input type="checkbox">
                                    </th>
                                    <th class="px-4 py-3 ">Sr.No.</th>
                                    <th class="px-4 py-3">File Name</th>
                                    <th class="px-4 py-3">Type</th>
                                    <th class="px-4 py-3">Statement Date</th>
                                    <th class="px-4 py-3">Synced Date</th>
                                    <th class="px-4 py-3">Total</th>
                                    <th class="px-4 py-3">Pending</th>
                                    <th class="px-4 py-3">Saved</th>
                                    <th class="px-4 py-3">Synced</th>
                                    <th class="px-4 py-3">Status</th>
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
                                        sales Register 02.2022.xlsx
                                    </td>
                                    <td class="px-4 py-3">Item Invoice</td>
                                    <td class="px-4 py-3">-</td>
                                    <td class="px-4 py-3">-</td>
                                    <td class="px-4 py-3">58</td>
                                    <td class="px-4 py-3">-</td>
                                    <td class="px-4 py-3">-</td>
                                    <td class="px-4 py-3">-</td>

                                    <td class="px-4 py-3">
                                        <a href="{{ route('data_entry_operators.bulkuploadcompletelist') }}" class="text-green-600 font-semibold">
                                            Complete
                                        </a>
                                    </td>

                                    <td class="px-4 py-3 text-right flex justify-end gap-4">
                                        <i class="fa-regular fa-eye text-gray-500 cursor-pointer"><a href=""></a></i>
                                        <i class="fa-regular fa-file-lines text-gray-500 cursor-pointer"><a href=""></a></i>
                                        <i class="fa-solid fa-ellipsis-vertical text-gray-500 cursor-pointer"><a href=""></a></i>
                                    </td>
                                </tr>

                                <!-- Row 2 -->
                                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700">
                                    <td class="px-4 py-3"><input type="checkbox"></td>
                                    <td class="px-4 py-3">2</td>
                                    <td class="px-4 py-3 font-medium text-gray-700 dark:text-gray-200">
                                        sales March 2022.xlsx
                                    </td>
                                    <td class="px-4 py-3">Accounting Invoice</td>
                                    <td class="px-4 py-3">12/03/2022</td>
                                    <td class="px-4 py-3">14/03/2022</td>
                                    <td class="px-4 py-3">32</td>
                                    <td class="px-4 py-3">2</td>
                                    <td class="px-4 py-3">20</td>
                                    <td class="px-4 py-3">10</td>

                                    <td class="px-4 py-3">
                                        <a href="#" class="text-yellow-600 font-semibold">
                                            Processing
                                        </a>
                                    </td>

                                    <td class="px-4 py-3 text-right flex justify-end gap-4">
                                        <i class="fa-regular fa-eye text-gray-500 cursor-pointer"></i>
                                        <i class="fa-regular fa-file-lines text-gray-500 cursor-pointer"></i>
                                        <i class="fa-solid fa-ellipsis-vertical text-gray-500 cursor-pointer"></i>
                                    </td>
                                </tr>

                                <!-- Row 3 -->
                                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700">
                                    <td class="px-4 py-3"><input type="checkbox"></td>
                                    <td class="px-4 py-3">3</td>
                                    <td class="px-4 py-3 font-medium text-gray-700 dark:text-gray-200">
                                        purchase data 2022.xlsx
                                    </td>
                                    <td class="px-4 py-3">Item Invoice</td>
                                    <td class="px-4 py-3">05/02/2022</td>
                                    <td class="px-4 py-3">-</td>
                                    <td class="px-4 py-3">80</td>
                                    <td class="px-4 py-3">5</td>
                                    <td class="px-4 py-3">60</td>
                                    <td class="px-4 py-3">15</td>

                                    <td class="px-4 py-3">
                                        <a href="#" class="text-red-500 font-semibold">
                                            Failed
                                        </a>
                                    </td>

                                    <td class="px-4 py-3 text-right flex justify-end gap-4">
                                        <i class="fa-regular fa-eye text-gray-500 cursor-pointer"></i>
                                        <i class="fa-regular fa-file-lines text-gray-500 cursor-pointer"></i>
                                        <i class="fa-solid fa-ellipsis-vertical text-gray-500 cursor-pointer"></i>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Sales Modal -->
    <div
        x-show="openUpload"
        x-transition
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">

        <div class="bg-white dark:bg-neutral-800 w-[720px] rounded-lg shadow-xl">

            <!-- Header -->
            <div class="flex justify-between items-center border-b px-2 py-1">
                <h2 class="text-lg font-semibold">Upload sales</h2>

                <button @click="openUpload=false" class="text-gray-500 hover:text-gray-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6">

                <!-- Upload Area -->
                <div class="border-2 border-dashed border-blue-400 dark:border-blue-500 rounded-lg p-10 text-center">

                    <i class="fa-regular fa-file text-3xl text-blue-500 mb-3"></i>

                    <p class="text-gray-600 mb-3">
                        Drag and drop a file here or
                    </p>

                    <div class="text-center">

                        <!-- Hidden File Input -->
                        <input
                            type="file"
                            id="salesFileUpload"
                            class="hidden"
                            accept=".xlsx,.xls"
                            onchange="showFileName(this)">

                        <!-- Styled Button -->
                        <button
                            type="button"
                            onclick="document.getElementById('salesFileUpload').click()"
                            class="border border-blue-500 text-blue-600 px-4 py-2 rounded-md text-sm flex items-center gap-2 mx-auto">

                            <i class="fa-solid fa-upload"></i>
                            Click to upload

                        </button>

                    </div>

                </div>

                <!-- File name -->
                <div id="uploadedFileName" class="text-sm text-gray-500 dark:text-gray-300 mt-3">
                    <i class="fa-solid fa-paperclip"></i>
                    <span id="fileNameText"></span>
                </div>

                <!-- Notes -->
                <div class="mt-6 text-sm text-gray-600 dark:text-gray-300">

                    <p class="font-semibold mb-2">Notes:</p>

                    <ul class="list-disc ml-5 space-y-1">

                        <li>Please make sure the uploaded excel file does not contain the dot(.) and dollar($) symbol in the column header and other then sales/purchase field do not add anything above header.</li>

                        <li>Please make sure the file size must not exceed 30MB.</li>

                        <li>Sync the ledger before uploading the file.</li>

                        <li>Please don't upload password protected excel files.</li>

                        <li>Date format should be DD/MM/YYYY.</li>

                    </ul>

                </div>

            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-3 border-t px-2 py-1">

                <button
                    @click="openUpload=false"
                    class="px-4 py-2 border rounded-md text-gray-600">
                    Cancel
                </button>

                <button
                    class="px-4 py-2 bg-blue-600 text-white rounded-md">
                    Upload
                </button>

                <button
                    class="px-4 py-2 bg-blue-800 text-white rounded-md">
                    Upload & Preview
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
$(document).ready(function(){

    $('#sampleDownload').change(function(){

        let type = $(this).val();

        if(type == 'with-item'){
            window.location.href = "/samples/Purchase-with-item-sample-file.xlsx";
        }

        if(type == 'without-item'){
            window.location.href = "/samples/Purchase-without-item-sample-file.xlsx";
        }

        // reset dropdown
        $(this).val('');

    });

});
</script>
    @endsection