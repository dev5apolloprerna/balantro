@php
$isEdit = ($mode ?? 'create') === 'edit';
$fieldPrefix = $isEdit ? 'e' : 'c';
@endphp

<div id="{{ $modalId }}"
    class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
    role="dialog" aria-modal="true" aria-labelledby="{{ $modalId }}-title">
    <button type="button" class="absolute inset-0 cursor-default" aria-label="Close modal"
        onclick="{{ $closeAction }}"></button>

    <div class="balantro-modal-panel relative w-full max-w-md rounded-2xl bg-white text-slate-900 shadow-xl dark:bg-slate-900 dark:text-white">
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-slate-700">
            <h2 id="{{ $modalId }}-title" class="text-lg font-semibold">{{ $title }}</h2>
            <button type="button" onclick="{{ $closeAction }}"
                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-white"
                aria-label="Close modal">&times;</button>
        </div>

        <form id="{{ $formId }}" method="POST" @if (!empty($action)) action="{{ $action }}" @endif
            class="space-y-4 px-6 py-5">
            @csrf
            @if ($isEdit)
            @method($method ?? 'PUT')
            @endif

            <input type="hidden" name="fcm_token" id="{{ $fieldPrefix }}_fcm_token">
            <input type="hidden" name="device_type" id="{{ $fieldPrefix }}_device_type">
            <input type="hidden" name="browser_name" id="{{ $fieldPrefix }}_browser_name">
            <input type="hidden" name="os_name" id="{{ $fieldPrefix }}_os_name">

            <div>
                <label for="{{ $nameId }}" class="mb-1 block text-sm font-medium">
                    Name <span class="text-red-500">*</span>
                </label>
                <input id="{{ $nameId }}" name="name" type="text" required maxlength="255"
                    class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            </div>

            <div>
                <label for="{{ $emailId }}" class="mb-1 block text-sm font-medium">
                    Email <span class="text-red-500">*</span>
                </label>
                <input id="{{ $emailId }}" name="email" type="email" required maxlength="255"
                    pattern="[^\s@]+@[^\s@]+\.[A-Za-z]{2,}"
                    title="Enter an email address with a valid domain (for example, name@company.com)."
                    class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-200 pt-4 dark:border-slate-700">
                <button type="button" onclick="{{ $closeAction }}"
                    class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                    Cancel
                </button>
                <button type="submit"
                    class="rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>