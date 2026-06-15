<div class="flex flex-col h-full bg-white dark:bg-neutral-800" data-controller="message">
    <div
        class="flex items-center gap-4 px-4 py-3 border-b border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800">
        <a href="{{ route('manager.messages.index') }}"
            class="flex items-center justify-center text-neutral-700 dark:text-neutral-300">
            <iconify-icon icon="heroicons:arrow-left" class="text-xl"></iconify-icon>
        </a>

        <div class="flex items-center gap-3 flex-1 min-w-0">
            <div class="avatar-lg object-fit-cover rounded-full bg-primary-500 text-white flex items-center justify-center flex-shrink-0"
                style="width: 40px; height: 40px;">
                {{ strtoupper(substr($selectedClient->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <h5 class="text-lg font-semibold mb-0 text-neutral-800 dark:text-white truncate">
                    {{ $selectedClient->name }}</h5>
            </div>
        </div>
    </div>

    <!-- Messages -->
    <div class="flex-1 overflow-y-auto p-4 bg-white dark:bg-neutral-800 messageBox-{{ request('client_id') }}"
        id="messages" data-message-target="messagesContainer" data-action="scroll->message#handleScroll">
        <div id="manager_message_blocks">
            @if ($messages && $messages->count() > 0)
                @php $lastMessageDate = null; @endphp
                <div class="space-y-3">
                    @foreach ($messages as $message)
                        @php $currentMessageDate = $message->created_at->toDateString(); @endphp
                        @if ($lastMessageDate != $currentMessageDate)
                            <div class="flex justify-center my-4">
                                <span
                                    class="px-3 py-1 text-xs bg-neutral-200 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-full">
                                    @if ($currentMessageDate == now()->toDateString())
                                        {{ __('chat.messages.today') }}
                                    @elseif ($currentMessageDate == now()->subDay()->toDateString())
                                        {{ __('chat.messages.yesterday') }}
                                    @else
                                        {{ $message->created_at->format('F j, Y') }}
                                    @endif
                                </span>
                            </div>
                            @php $lastMessageDate = $currentMessageDate; @endphp
                        @endif

                        @include('manager.messages.mobile_message', [
                            'message' => $message,
                            'selectedClient' => $selectedClient,
                        ])
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-full text-center py-10 px-6">
                    <div
                        class="w-20 h-20 mx-auto bg-neutral-200 dark:bg-neutral-700 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-neutral-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-medium text-neutral-800 dark:text-white mb-2">
                        {{ __('chat.messages.no_messages.title') }}
                    </h3>
                    <p class="text-neutral-600 dark:text-neutral-300 text-sm">
                        {{ __('chat.messages.no_messages.description', ['name' => $selectedClient->name]) }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Message Form -->
    <div class="border-t border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-3" id="message_form">
        <form action="{{ route('manager.messages.store') }}" method="POST" class="flex flex-col gap-2"
            data-controller="message" data-action="submit->message#submitForm input->message#toggleSubmitButton">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $selectedClient->id }}">
            <input type="hidden" name="is_first_message" value="{{ !$messages->count() }}">

            <div id="file-preview-container" class="hidden mb-2" data-message-target="filePreviewContainer">
                <div
                    class="flex items-center gap-2 bg-neutral-100 dark:bg-neutral-700 rounded-lg px-3 py-2 overflow-x-scroll">
                    <div class="flex items-center gap-2" data-message-target="filePreviews">
                    </div>
                    <button type="button"
                        class="ml-auto text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200"
                        data-action="click->message#clearAllFiles"
                        title="{{ __('chat.messages.file_input.clear_all') }}">
                        <iconify-icon icon="heroicons:x-mark" class="text-lg"></iconify-icon>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="file" name="attachments" multiple class="hidden" id="file-upload-input"
                    data-message-target="fileInput" data-action="change->message#displayFilePreview">

                <button type="button"
                    class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center hover:bg-primary-700 transition-colors cursor-pointer"
                    data-action="click->message#triggerFileInput" title="{{ __('chat.messages.file_input.attach') }}">
                    <iconify-icon icon="heroicons:paper-clip" class="text-lg"></iconify-icon>
                </button>

                <div class="flex-1 relative">
                    <input type="text" name="description"
                        class="w-full py-2 pl-4 pr-12 rounded-full border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 placeholder-neutral-400 dark:placeholder-neutral-500 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                        autocomplete="off" placeholder="{{ __('chat.messages.input.placeholder') }}"
                        data-message-target="input">

                    <button type="submit"
                        class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center hover:bg-primary-700 cursor-pointer"
                        style="display: none;" data-message-target="submitButton">
                        <iconify-icon icon="heroicons:paper-airplane" class="text-lg"></iconify-icon>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
