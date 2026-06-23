@extends('layouts.super_admin')
@section('content')
<div data-controller="confirm-delete"
    x-data="{ openUpload:false, openClient: {{ session('iPartyId') ? 'false' : 'true' }} }"
    x-init="openUpload = false">
    <div class="container mx-auto">
        <div class="flex justify-between items-center mb-3">
            <h6 class="font-semibold mb-0 dark:text-white">{{ __("Sales") }}</h6>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-12">
            <div class="col-span-12">
                <div class="card !border-0 rounded-lg overflow-hidden bg-white dark:bg-neutral-800">
                    <div class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 p-2 sm:p-3">
                        <div class="flex flex-col gap-3">
                            <div class="flex flex-col sm:flex-row gap-3 w-full">
                                <div class="bulk-toolbar-row flex items-center justify-between w-full gap-3 flex-nowrap">
                                    <!-- Left Side Tabs -->
                                    @include('admin.transaction-processing.bulk-upload-tabs')
                                    <!-- Right Side Actions -->
                                    <div class="bulk-toolbar-actions flex items-center justify-end gap-2 mt-0 w-full flex-nowrap">
                                        @if(session('client_name'))
                                        <div class="bulk-client-name text-sm text-green-600 font-semibold whitespace-nowrap truncate max-w-[140px]" style="font-variant-caps: small-caps;">
                                            {{ session('client_name') }}
                                        </div>
                                        @endif
                                        <!-- Divider -->
                                        <div class="h-4 w-px bg-gray-300 dark:bg-neutral-600"></div>
                                        <!-- Year Dropdown -->
                                        @if(!empty($years) && count($years))
                                        <select
                                            onchange="window.location.href=this.value"
                                            class="text-xs bg-transparent border-0 focus:ring-0 outline-none">
                                            @foreach($years as $key => $year)
                                            <option
                                                value="{{ route('sales.select.year', $year->strYear) }}"
                                                {{ session('year') == $year->strYear ? 'selected' : '' }}>
                                                {{ $year->strYear }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @endif
                                        <div class="h-4 w-px bg-gray-300 dark:bg-neutral-600"></div>
                                        
                                        <button
                                            @click="openClient=true"
                                            class="bulk-text-btn bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-md flex items-center gap-2 shadow-sm whitespace-nowrap">
                                            <i class="fa-solid fa-building"></i>
                                            Select Client
                                        </button>

                                        @if(session('guid'))
                                            <a href="{{ route('clients.Gstindex', session('guid')) }}" class="bulk-settings-btn rounded-full bg-cyan-100 p-2 text-cyan-700 ring-1 ring-inset ring-cyan-200 hover:bg-cyan-200 dark:bg-cyan-900/30 dark:text-cyan-300 dark:ring-cyan-800 shrink-0" 
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
                                            @endif
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
                                @foreach($uploads as $upload)
                                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700">
                                    <td class="px-4 py-3">
                                        <input type="checkbox">
                                    </td>
                                    <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-700 dark:text-gray-200">
                                        {{ $upload->file_name }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ ucfirst(str_replace('_',' ',$upload->type)) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $upload->statement_date ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $upload->synced_date ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $upload->total ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $upload->pending ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $upload->saved ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $upload->synced ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($upload->status == 'Completed')
                                        <a href="{{ route('transaction_processing.preview_processing_sales',$upload->id) }}"
                                            class="text-green-500 font-semibold hover:underline">
                                            Completed
                                        </a>
                                        @elseif($upload->status == 'Processing')
                                        <a href="{{ route('transaction_processing.preview_processing_sales',$upload->id) }}"
                                            class="text-yellow-500 font-semibold hover:underline">
                                            Processing
                                        </a>
                                        @elseif($upload->status == 'Pending')
                                        <a href="{{ route('transaction_processing.preview_processing_sales',$upload->id) }}"
                                            class="text-yellow-500 font-semibold hover:underline">
                                            Pending
                                        </a>
                                        @else
                                        <a href="{{ route('transaction_processing.preview_processing_sales',$upload->id) }}"
                                            class="text-red-500 font-semibold hover:underline">
                                            Failed
                                        </a>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right flex justify-end gap-4">
                                        <a href="{{ route('transaction_processing.preview_processing_sales',$upload->id) }}">
                                            <i class="fa-regular fa-eye action-icon text-gray-500 cursor-pointer"></i>
                                        </a>
                                        <!-- <i class="fa-regular fa-file-lines text-gray-500 cursor-pointer"></i>
                                        <i class="fa-solid fa-ellipsis-vertical text-gray-500 cursor-pointer"></i> -->
                                        <div x-data="{ open:false }" class="relative inline-block">

                                            <!-- Button -->
                                            <button onclick="openDropdown(event, {{ $upload->id }})"
                                                class="text-gray-500 hover:text-gray-700 px-2">
                                                <i class="fa-solid fa-ellipsis-vertical action-icon"></i>
                                            </button>

                                            <!-- Dropdown -->
                                            <div id="globalDropdown"
                                                class="hidden fixed bg-white dark:bg-neutral-800 border rounded-xl shadow-xl w-48 z-[99999]">

                                                <div class="px-4 py-2 text-xs font-semibold text-gray-400 border-b">
                                                    Change Status
                                                </div>

                                                <button onclick="handleStatus('Pending')" class="dropdown-item">Pending</button>
                                                <button onclick="handleStatus('Processing')" class="dropdown-item">Processing</button>
                                                <button onclick="handleStatus('Completed')" class="dropdown-item">Completed</button>

                                                <div class="border-t my-1"></div>

                                                <button onclick="handleDelete()" class="dropdown-item text-red-500" style="color: indianred;">
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Sales Modal -->
    <div
        x-cloak
        x-show="openUpload"
        x-transition
        style="display: none;"
        data-upload-modal
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
        <div class="bg-white dark:bg-neutral-800 w-[720px] rounded-lg shadow-xl">
            <!-- Header -->
            <div class="flex justify-between items-center border-b px-5 py-3">
                <h2 class="text-lg font-semibold">Upload sales</h2>
                <button @click="openUpload=false" class="text-gray-500 hover:text-gray-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('sales.upload') }}" enctype="multipart/form-data">
                @csrf
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
                                name="sales_file"
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
                <div class="flex justify-end gap-3 border-t px-5 py-3">
                    <button
                        @click="openUpload=false"
                        class="px-4 py-2 border rounded-md text-gray-600">
                        Cancel
                    </button>
                    <button
                        class="px-4 py-2 bg-blue-600 text-white rounded-md">
                        Upload
                    </button>
                    <!-- <button type="submit"
                        class="px-4 py-2 bg-blue-800 text-white rounded-md">
                        Upload & Preview
                    </button> -->
                </div>
            </form>
        </div>
    </div>

    <!-- CLIENT DRAWER -->
    <div
        x-cloak
        style="display: none;"
        x-show="openClient"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-0 z-50 flex justify-end">
        <!-- Background -->
        <div
            class="absolute inset-0 bg-black/50"
            @click="openClient=false">
        </div>
        <!-- Drawer -->
        <div class="relative w-[420px] h-full bg-white dark:bg-neutral-800 shadow-xl flex flex-col">
            <!-- Header -->
            <div class="flex justify-between items-center px-5 py-4 border-b">
                <h2 class="text-lg font-semibold">
                    My Company
                </h2>
                <button @click="openClient=false">
                    <i class="fa-solid fa-xmark text-gray-500"></i>
                </button>
            </div>
            <!-- Search -->
            <div class="p-4 border-b">
                <input
                    type="text"
                    id="clientSearch"
                    placeholder="Search by Name"
                    class="w-full border px-3 py-2 rounded-md">
            </div>
            <!-- Client List -->
            <div class="flex-1 min-h-0 overflow-y-auto">
                @foreach($clients as $client)
                <a
                    href="{{ route('sales.select.company',$client->id) }}"
                    class="flex items-center justify-between px-4 py-3 border-b hover:bg-gray-100 dark:hover:bg-neutral-700 client-item">
                    <div class="flex items-center gap-3">
                        <div class="bg-gray-200 dark:bg-neutral-700 p-2 rounded">
                            <i class="fa-solid fa-building text-gray-600"></i>
                        </div>
                        <div>
                            <div class="font-medium">
                                {{ $client->name }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $client->code ?? '' }}
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <style>
        .dropdown-item {
            width: 100%;
            text-align: left;
            padding: 10px 16px;
            font-size: 14px;
            display: block;
            border: none;
            background: transparent;
        }

        .dropdown-item:hover {
            background: #374151;
            color: white;
        }

        /* DARK MODE FIX */
        .dark .dropdown-item {
            color: #e5e7eb;
            /* light text */
        }

        .dark .dropdown-item:hover {
            background: #374151;
            /* dark hover bg */
            color: #ffffff;
            /* white text */
        }

        #globalDropdown {
            border-radius: 10px;
            overflow: hidden;
            /* 🔥 IMPORTANT FIX */
        }
    </style>
    @endsection
    @section('scripts')
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let input = document.getElementById('clientSearch');
            if (input) {
                input.addEventListener('keyup', function() {
                    let filter = this.value.toLowerCase();
                    document.querySelectorAll('.client-item').forEach(function(item) {
                        item.style.display =
                            item.innerText.toLowerCase().includes(filter) ?
                            '' :
                            'none';
                    });
                });
            }
        });

        let currentId = null;

        function openDropdown(e, id) {
            e.stopPropagation();
            currentId = id;

            let dropdown = document.getElementById('globalDropdown');
            dropdown.classList.remove('hidden');

            let rect = e.target.getBoundingClientRect();
            let dropdownHeight = 180; // approx height
            let spaceBelow = window.innerHeight - rect.bottom;

            // 👉 If no space below → open upward
            if (spaceBelow < dropdownHeight) {
                dropdown.style.top = (rect.top - dropdownHeight) + "px";
            } else {
                dropdown.style.top = rect.bottom + "px";
            }
            dropdown.style.left = (rect.left - 150) + "px";
        }


        document.addEventListener('click', function() {
            document.getElementById('globalDropdown').classList.add('hidden');
        });

        function handleStatus(status) {
            changeStatus(currentId, status);
        }

        function changeStatus(id, status) {
            $.post("{{ route('sales.change.status') }}", {
                _token: "{{ csrf_token() }}",
                id: id,
                status: status
            }, function(res) {
                showToast(res.message,'success');
                location.reload();
            });
        }

        function handleDelete() {
            deleteUpload(currentId);
        }

        function deleteUpload(id) {

            if (!confirm('Delete full upload?')) return;

            $.post("{{ route('sales.bulk.delete') }}", {
                _token: "{{ csrf_token() }}",
                ids: [id] // 🔥 important (array)
            }, function(res) {
                showToast(res.message,'success');
                location.reload();
            });

        }
    </script>
    @endsection
