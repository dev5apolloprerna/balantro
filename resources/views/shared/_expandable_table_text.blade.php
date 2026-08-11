@php
    $displayText = filled($text) ? $text : ($fallback ?? '—');
@endphp

<button type="button"
    data-expandable-table-text
    aria-expanded="false"
    onclick="const expanded = this.getAttribute('aria-expanded') === 'true'; const text = this.querySelector('span'); this.setAttribute('aria-expanded', String(!expanded)); text.classList.toggle('truncate', expanded); text.classList.toggle('whitespace-normal', !expanded); text.classList.toggle('break-words', !expanded);"
    title="{{ $displayText }}"
    class="block w-full cursor-pointer text-left focus-visible:rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 {{ $class ?? '' }}">
    <span class="block truncate">
        {{ $displayText }}
    </span>
</button>