@extends('layouts.super_admin')
@section('content')

<div x-data="{ openUpload:false, openClient: {{ session('iPartyId') ? 'false' : 'true' }} }"
    x-init="openUpload = false">

    <div class="container mx-auto">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-3">
            <h6 class="font-semibold dark:text-white">Journal Uploads</h6>
        </div>

        <div class="card bg-white dark:bg-neutral-800 rounded-lg overflow-hidden">

            <!-- TOP BAR -->
            <div class="p-4 flex justify-between items-center border-b border-gray-200 dark:border-neutral-600">

                @include('admin.transaction-processing.bulk-upload-tabs')

                <div class="flex items-center gap-3">

                    @if(session('client_name'))
                    <div class="text-green-600 font-semibold" style="font-size: 1.0rem;font-variant-caps: small-caps;">
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
                    <!-- Divider -->
                    <div class="h-4 w-px bg-gray-300 dark:bg-neutral-600"></div>

                    <!-- Select Client -->
                    <button
                        @click="openClient=true"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
                        Select Client
                    </button>

                </div>
            </div>

            <!-- TABLE -->
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left">

                    <thead class="bg-gray-200 dark:bg-neutral-700 text-gray-700 dark:text-gray-200 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3"><input type="checkbox"></th>
                            <th class="px-4 py-3">Sr.No</th>
                            <th class="px-4 py-3">File Name</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Pending</th>
                            <th class="px-4 py-3">Saved</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y text-gray-700 dark:text-gray-200">
                        @foreach($uploads as $upload)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700">

                            <td class="px-4 py-3"><input type="checkbox"></td>

                            <td class="px-4 py-3">{{ $loop->iteration }}</td>

                            <td class="px-4 py-3 font-medium">
                                {{ $upload->file_name }}
                            </td>

                            <td class="px-4 py-3">{{ $upload->total }}</td>
                            <td class="px-4 py-3">{{ $upload->pending }}</td>
                            <td class="px-4 py-3">{{ $upload->saved }}</td>

                            <td class="px-4 py-3">
                                <a href="{{ route('transaction_processing.preview_processing_journal',$upload->id) }}"
                                    class="@if($upload->status=='completed') text-green-500
                                      @elseif($upload->status=='processing') text-yellow-500
                                      @else text-blue-500 @endif font-semibold">
                                    {{ ucfirst($upload->status) }}
                                </a>
                            </td>

                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('transaction_processing.preview_processing_journal',$upload->id) }}">
                                    <i class="fa-regular fa-eye action-icon text-gray-500"></i>
                                </a>
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

    <!-- ================= UPLOAD MODAL ================= -->
    <div
        x-cloak
        x-show="openUpload"
        style="display: none;"
        data-upload-modal
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">

        <div class="bg-white dark:bg-neutral-800 text-gray-800 dark:text-gray-200 w-[700px] rounded-lg shadow-xl">

            <!-- HEADER -->
            <div class="flex justify-between items-center border-b border-gray-200 dark:border-neutral-600 px-5 py-3">
                <h2 class="text-lg font-semibold">Upload Journal</h2>
                <button @click="openUpload=false" class="text-gray-500 dark:text-gray-300">✖</button>
            </div>

            <!-- FORM -->
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf

                <div class="p-6">

                    <!-- UPLOAD BOX -->
                    <div onclick="document.getElementById('journalFile').click()"
                        class="border-2 border-dashed border-blue-400 dark:border-blue-500 
                            bg-gray-50 dark:bg-neutral-700 
                            text-gray-600 dark:text-gray-300
                            rounded-lg p-10 text-center cursor-pointer">

                        <i class="fa-regular fa-file text-3xl text-blue-500 mb-3"></i>

                        <p>Drag & Drop or Click to upload</p>

                        <input type="file"
                            name="file"
                            id="journalFile"
                            class="hidden"
                            accept=".xlsx,.xls"
                            onchange="showJournalFileName(this)">
                    </div>

                    <!-- FILE NAME -->
                    <div id="fileNameBox"
                        class="mt-3 hidden text-sm text-gray-600 dark:text-gray-300">
                        <i class="fa-solid fa-paperclip"></i>
                        <span id="fileNameText"></span>
                    </div>

                    <!-- ✅ PROGRESS BAR -->
                    <div id="progressBar"
                        class="w-full bg-gray-200 rounded mt-3 hidden overflow-hidden">

                        <div id="progressFill"
                            class="bg-blue-600 text-xs text-white text-center p-1 rounded"
                            style="width:0%">
                            0%
                        </div>
                    </div>

                    <!-- NOTES -->
                    <div class="mt-5 text-sm text-gray-600 dark:text-gray-300">
                        <b>Notes:</b>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Format: Journal No | Date | Ledger | Dr/Cr | Amount</li>
                            <li>Date format: DD/MM/YYYY</li>
                            <li>File size max 30MB</li>
                            <li>No password protected file</li>
                        </ul>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="flex justify-end gap-3 border-t border-gray-200 dark:border-neutral-600 px-5 py-3">
                    <button type="button" @click="openUpload=false"
                        class="px-4 py-2 border rounded">
                        Cancel
                    </button>

                    <!-- <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Upload
                </button> -->
                    <button type="submit"
                        id="uploadBtn"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-2">

                        <span id="uploadText">Upload</span>

                        <span id="uploadLoader" class="hidden">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                        </span>

                    </button>
                </div>

            </form>

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
    // ✅ SHOW FILE NAME
    function showJournalFileName(input) {
        let file = input.files[0];

        if (file) {
            document.getElementById('fileNameText').innerText = file.name;
            document.getElementById('fileNameBox').classList.remove('hidden');
        }
    }

    $('#uploadForm').submit(function(e) {
        e.preventDefault();

        let fileInput = $('#journalFile')[0];

        if (!fileInput.files.length) {
            alert('Please select file');
            return;
        }

        let formData = new FormData(this);

        // 🔥 DISABLE BUTTON + SHOW LOADER
        $('#uploadBtn').prop('disabled', true);
        $('#uploadText').text('Uploading...');
        $('#uploadLoader').removeClass('hidden');

        $.ajax({
            url: "{{ route('journal.upload') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            // ✅ PROGRESS CODE HERE
            xhr: function() {
                let xhr = new window.XMLHttpRequest();

                xhr.upload.addEventListener("progress", function(e) {
                    if (e.lengthComputable) {
                        let percent = Math.round((e.loaded / e.total) * 100);

                        $('#progressFill')
                            .css('width', percent + '%')
                            .text(percent + '%');
                    }
                });

                return xhr;
            },
            success: function(res) {

                $('#progressFill').css('width', '100%').text('Done ✔');

                $('#uploadText').text('Uploaded ✔');

                setTimeout(() => {
                    location.reload();
                }, 800);
            },

            error: function() {

                alert('Upload Failed');

                // 🔥 ENABLE AGAIN
                $('#uploadBtn').prop('disabled', false);
                $('#uploadText').text('Upload');
                $('#uploadLoader').addClass('hidden');
            }
        });
    });

    // ✅ AJAX UPLOAD
    // $('#uploadForm').submit(function(e){
    //     e.preventDefault();

    //     let formData = new FormData(this);

    //     $.ajax({
    //         url: "{{ route('journal.upload') }}",
    //         type: "POST",
    //         data: formData,
    //         processData: false,
    //         contentType: false,
    //         success: function(res){
    //             alert('Uploaded Successfully');
    //             location.reload();
    //         },
    //         error: function(){
    //             alert('Upload Failed');
    //         }
    //     });
    // });
    let currentId = null;

    function openDropdown(e, id) {
        e.stopPropagation();
        currentId = id;

        let dropdown = document.getElementById('globalDropdown');
        dropdown.classList.remove('hidden');

        let rect = e.target.getBoundingClientRect();
        dropdown.style.top = rect.bottom + "px";
        dropdown.style.left = (rect.left - 150) + "px";
    }

    document.addEventListener('click', function() {
        document.getElementById('globalDropdown').classList.add('hidden');
    });

    // STATUS
    function handleStatus(status) {
        $.post("{{ route('journal.upload.status') }}", {
            _token: "{{ csrf_token() }}",
            id: currentId,
            status: status
        }, function(res) {
            alert(res.message);
            location.reload();
        });
    }

    // DELETE SINGLE
    function handleDelete() {

        if (!confirm('Delete full upload?')) return;

        $.post("{{ route('journal.bulk.delete') }}", {
            _token: "{{ csrf_token() }}",
            ids: [currentId]
        }, function(res) {
            alert(res.message);
            location.reload();
        });
    }

    // BULK DELETE
    function bulkDelete() {

        let ids = $('.rowCheckbox:checked').map(function() {
            return this.value;
        }).get();

        if (ids.length == 0) {
            alert('Select at least one');
            return;
        }

        if (!confirm('Delete selected?')) return;

        $.post("{{ route('journal.bulk.delete') }}", {
            _token: "{{ csrf_token() }}",
            ids: ids
        }, function(res) {
            alert(res.message);
            location.reload();
        });
    }
</script>

@endsection
