<div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}" id="message-{{ $message->id }}">
    <div class="flex max-w-[80%] gap-3">
        @unless($message->sender_id === auth()->id())
            <div class="avatar-lg object-fit-cover rounded-full bg-primary-500 text-white flex items-center justify-center flex-shrink-0" style="width: 32px; height: 32px;">
                {{ 'B' }}
            </div>
        @endunless

        <div class="flex flex-col {{ $message->sender_id === auth()->id() ? 'items-end' : 'items-start' }}">
            @if($message->documents->isNotEmpty())
                <div class="grid gap-2 mb-2">
                    @foreach($message->documents as $document)
                        <div class="flex items-center gap-2 bg-white dark:bg-neutral-700 rounded-lg p-2 border border-neutral-200 dark:border-neutral-600">
                            @if(str_starts_with($document->mime_type, 'image/'))
                                <iconify-icon icon="heroicons:photo" class="text-4xl text-primary-600 dark:text-primary-400"></iconify-icon>
                            @else
                                <iconify-icon icon="heroicons:document" class="text-4xl text-primary-600 dark:text-primary-400"></iconify-icon>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-neutral-800 dark:text-neutral-200 truncate max-w-[45px]">{{ $document->file_name }}</div>
                                <div class="text-xs text-neutral-500 dark:text-neutral-400">
                                    {{ formatBytes($document->size) }}
                                </div>
                            </div>
                            <div class="ml-2">
                                <a href="{{ route('file.download', $document) }}" 
                                    class="w-9 h-9 text-primary-600 dark:text-primary-400 flex items-center justify-center hover:text-primary-700 dark:hover:text-primary-300 transition-colors"
                                    title="@lang('chat.messages.message.download')">
                                    <iconify-icon icon="heroicons:arrow-down-tray" class="text-[1.1rem] p-0.5"></iconify-icon>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($message->description)
                <div class="px-4 py-3 rounded-2xl {{ $message->sender_id === auth()->id() ? 'bg-primary-500 text-white rounded-br-none' : 'bg-neutral-100 dark:bg-neutral-700 text-neutral-800 dark:text-neutral-200 rounded-bl-none' }} ">
                    <p class="mb-0 whitespace-pre-wrap break-all tracking-wide">{{ $message->description }}</p>
                </div>
            @endif

            <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                @if($message->sender_id === auth()->id())
                    {{ $message->created_at->format(config('chat.time.formats.short')) }}
                @else
                    {{ $message->created_at->format(config('chat.time.formats.short')) }}
                @endif
            </div>
        </div>

        @if($message->sender_id === auth()->id())
            <div class="avatar-lg object-fit-cover rounded-full bg-primary-500 text-white flex items-center justify-center flex-shrink-0" style="width: 32px; height: 32px;">
                {{ strtoupper(substr($message->sender->name, 0, 1)) }}
            </div>
        @endif
    </div>
</div>