@php $sel = $selectedClient ?? ($selected_conversation ?? ($selected ?? ($selected_client ?? null))); @endphp
<div class="flex flex-col h-full bg-white dark:bg-neutral-800" data-controller="message">
    <!-- Header with back button -->
    <div
        class="flex items-center gap-4 px-4 py-3 border-b border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800">
        <a href="{{ route('supervisor.messages.index') }}"
            class="flex items-center justify-center text-neutral-700 dark:text-neutral-300">
            <iconify-icon icon="heroicons:arrow-left" class="text-xl"></iconify-icon>
        </a>

        <div class="flex items-center gap-3 flex-1 min-w-0">
            <div class="avatar-lg object-fit-cover rounded-full bg-primary-500 text-white flex items-center justify-center flex-shrink-0"
                style="width: 40px; height: 40px;">
                {{ strtoupper(substr($sel?->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <h5 class="text-lg font-semibold mb-0 text-neutral-800 dark:text-white truncate">{{ $sel?->name }}
                </h5>
            </div>
        </div>
    </div>

    <!-- Messages -->
    <div class="flex-1 overflow-y-auto p-4 bg-white dark:bg-neutral-800 messageBox-{{ request('client_id') }}"
        id="messages" data-message-target="messagesContainer" data-action="scroll->message#handleScroll">
        <div id="supervisor_message_blocks">
            @if ($messages && count($messages) > 0)
                @php $lastMessageDate = null @endphp
                <div class="space-y-3">
                    @foreach ($messages as $message)
                        @php $currentMessageDate = $message->created_at->toDateString() @endphp
                        @if ($lastMessageDate != $currentMessageDate)
                            <div class="flex justify-center my-4">
                                <span
                                    class="px-3 py-1 text-xs bg-neutral-200 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-full">
                                    @if ($currentMessageDate == now()->toDateString())
                                        Today
                                    @elseif($currentMessageDate == now()->subDay()->toDateString())
                                        Yesterday
                                    @else
                                        {{ $message->created_at->format('F j, Y') }}
                                    @endif
                                </span>
                            </div>
                            @php $lastMessageDate = $currentMessageDate @endphp
                        @endif

                        @if ($message->sender == auth()->user() || $message->sender == $selectedClient)
                            <div class="flex {{ $message->sender == auth()->user() ? 'justify-end' : 'justify-start' }}"
                                id="message-{{ $message->id }}">
                                <div class="flex max-w-[85%] gap-2">
                                    @unless ($message->sender == auth()->user())
                                        <div class="flex flex-col items-center flex-shrink-0">
                                            <div class="avatar-lg object-fit-cover rounded-full bg-primary-500 text-white flex items-center justify-center"
                                                style="width: 32px; height: 32px;">
                                                {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                                            </div>
                                        </div>
                                    @endunless

                                    <div
                                        class="flex-1 flex flex-col {{ $message->sender == auth()->user() ? 'items-end' : 'items-start' }}">
                                        @if ($message->documents->count() > 0)
                                            <div class="grid gap-2 mb-1 w-full">
                                                @foreach ($message->documents as $document)
                                                    <div
                                                        class="flex items-center gap-2 bg-white dark:bg-neutral-700 rounded-lg p-2 border border-neutral-200 dark:border-neutral-600">
                                                        @if (str_starts_with($document->mime_type, 'image/'))
                                                            <iconify-icon icon="heroicons:photo"
                                                                class="text-4xl text-primary-600 dark:text-primary-400"></iconify-icon>
                                                        @else
                                                            <iconify-icon icon="heroicons:document"
                                                                class="text-4xl text-primary-600 dark:text-primary-400"></iconify-icon>
                                                        @endif
                                                        <div class="flex-1 min-w-0">
                                                            <div
                                                                class="text-sm font-medium text-neutral-800 dark:text-neutral-200 truncate max-w-[45px]">
                                                                {{ $document->file_name }}</div>
                                                            <div class="text-xs text-neutral-500 dark:text-neutral-400">
                                                                {{ formatBytes($document->size) }}
                                                            </div>
                                                        </div>
                                                        <div class="ml-2">
                                                            <a href="{{ route('file.download', $document->id) }}"
                                                                class="w-9 h-9 text-primary-600 dark:text-primary-400 flex items-center justify-center hover:text-primary-700 dark:hover:text-primary-300 transition-colors"
                                                                title="Download">
                                                                <iconify-icon icon="heroicons:arrow-down-tray"
                                                                    class="text-[1.1rem] p-0.5"></iconify-icon>
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if ($message->description)
                                            <div
                                                class="px-3 py-2 rounded-lg {{ $message->sender == auth()->user() ? 'bg-primary-500 text-white rounded-br-none' : 'bg-neutral-100 dark:bg-neutral-700 text-neutral-800 dark:text-neutral-200 rounded-bl-none' }}">
                                                <p class="mb-0 break-all overflow-wrap-anywhere text-sm">
                                                    {{ $message->description }}</p>
                                            </div>
                                        @endif

                                        <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                                            {{ $message->created_at->format('h:i A') }}
                                        </div>
                                    </div>

                                    @if ($message->sender == auth()->user())
                                        <div class="flex flex-col items-center flex-shrink-0">
                                            <div class="avatar-lg object-fit-cover rounded-full bg-primary-500 text-white flex items-center justify-center"
                                                style="width: 32px; height: 32px;">
                                                {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="flex justify-end" id="message-{{ $message->id }}">
                                <div class="flex max-w-[85%] gap-2">
                                    <div class="flex-1 flex flex-col items-end">
                                        @if ($message->documents->count() > 0)
                                            <div class="grid gap-2 mb-1 w-full">
                                                @foreach ($message->documents as $document)
                                                    <div
                                                        class="flex items-center gap-2 bg-white dark:bg-neutral-700 rounded-lg p-2 border border-neutral-200 dark:border-neutral-600">
                                                        @if (str_starts_with($document->mime_type, 'image/'))
                                                            <iconify-icon icon="heroicons:photo"
                                                                class="text-4xl text-primary-600 dark:text-primary-400"></iconify-icon>
                                                        @else
                                                            <iconify-icon icon="heroicons:document"
                                                                class="text-4xl text-primary-600 dark:text-primary-400"></iconify-icon>
                                                        @endif
                                                        <div class="flex-1 min-w-0">
                                                            <div
                                                                class="text-sm font-medium text-neutral-800 dark:text-neutral-200 truncate max-w-[45px]">
                                                                {{ $document->file_name }}</div>
                                                            <div class="text-xs text-neutral-500 dark:text-neutral-400">
                                                                {{ formatBytes($document->size) }}
                                                            </div>
                                                        </div>
                                                        <div class="ml-2">
                                                            <a href="{{ route('file.download', $document->id) }}"
                                                                class="w-9 h-9 text-primary-600 dark:text-primary-400 flex items-center justify-center hover:text-primary-700 dark:hover:text-primary-300 transition-colors"
                                                                title="Download">
                                                                <iconify-icon icon="heroicons:arrow-down-tray"
                                                                    class="text-[1.1rem] p-0.5"></iconify-icon>
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if ($message->description)
                                            <div class="px-3 py-2 rounded-lg bg-primary-500 text-white rounded-br-none">
                                                <p class="mb-0 break-all overflow-wrap-anywhere text-sm">
                                                    {{ $message->description }}</p>
                                            </div>
                                        @endif

                                        <div class="flex items-center gap-1 mt-1">
                                            <div class="text-xs text-neutral-500 dark:text-neutral-400">
                                                {{ $message->created_at->format('h:i A') }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-center flex-shrink-0">
                                        <div class="avatar-lg object-fit-cover rounded-full bg-primary-500 text-white flex items-center justify-center"
                                            style="width: 32px; height: 32px;">
                                            {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
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
                        No messages yet
                    </h3>
                    <p class="text-neutral-600 dark:text-neutral-300 text-sm">
                        Start a conversation with {{ $sel?->name }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Message Form -->
    <div class="border-t border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-3" id="message_form">
        <form action="{{ route('supervisor.messages.store') }}" method="POST" class="flex flex-col gap-2"
            data-controller="message" data-action="submit->message#submitForm input->message#toggleSubmitButton">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $sel?->id }}">
            <input type="hidden" name="is_first_message" value="{{ !$messages ? 'true' : 'false' }}">

            <div id="file-preview-container" class="hidden mb-2" data-message-target="filePreviewContainer">
                <div
                    class="flex items-center gap-2 bg-neutral-100 dark:bg-neutral-700 rounded-lg px-3 py-2 overflow-x-scroll">
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
                    class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center hover:bg-primary-700 transition-colors cursor-pointer"
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
    </div>
</div>
