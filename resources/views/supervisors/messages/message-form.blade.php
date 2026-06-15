@php $sel = $selectedClient ?? ($selected_user ?? ($selected_client ?? ($selected ?? null))); @endphp
<form action="{{ route('supervisor.messages.store') }}" method="POST" class="flex flex-col gap-2" data-controller="message"
    data-action="submit->message#submitForm input->message#toggleSubmitButton">
    @csrf
    <input type="hidden" name="receiver_id" value="{{ $sel?->id }}">
    <input type="hidden" name="is_first_message" value="{{ !$messages ? 'true' : 'false' }}">

    <div id="file-preview-container" class="hidden mb-2" data-message-target="filePreviewContainer">
        <div class="flex items-center gap-2 bg-neutral-100 dark:bg-neutral-700 rounded-lg px-3 py-2 overflow-x-scroll">
            <div class="flex items-center gap-2" data-message-target="filePreviews">
            </div>
            <button type="button"
                class="ml-auto text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200"
                data-action="click->message#clearAllFiles" title="Clear all">
                <iconify-icon icon="heroicons:x-mark" class="text-lg"></iconify-icon>
            </button>
        </div>
    </div>

    <div class="flex items-center gap-2">
        <input type="file" name="attachments[]" multiple class="hidden" id="file-upload-input"
            data-message-target="fileInput" data-action="change->message#displayFilePreview">

        <button type="button"
            class="w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center hover:bg-primary-700 transition-colors cursor-pointer"
            data-action="click->message#triggerFileInput" title="Attach files">
            <iconify-icon icon="heroicons:paper-clip" class="text-lg"></iconify-icon>
        </button>

        <div class="flex-1 relative">
            <input type="text" name="description"
                class="w-full py-2 pl-4 pr-12 rounded-full border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 placeholder-neutral-400 dark:placeholder-neutral-500 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                autocomplete="off" placeholder="Type a message..." data-message-target="input">

            <button type="submit"
                class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center hover:bg-primary-700 cursor-pointer"
                style="display: none;" data-message-target="submitButton">
                <iconify-icon icon="heroicons:paper-airplane" class="text-lg"></iconify-icon>
            </button>
        </div>
    </div>
</form>
