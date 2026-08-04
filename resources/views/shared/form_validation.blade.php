{{-- System-wide validation feedback. Server validation remains the source of truth;
     the script only provides earlier, accessible browser feedback. --}}
@if ($errors->any())
    <div id="system-validation-summary"
         class="system-validation-summary"
         role="alert"
         aria-live="assertive"
         tabindex="-1">
        <strong>Please correct the following {{ $errors->count() === 1 ? 'error' : 'errors' }}:</strong>
        <ul>
            @foreach ($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif

<style>
    .system-validation-summary {
        width: 100%;
        margin: 0 0 1rem;
        padding: .875rem 1rem;
        box-sizing: border-box;
        color: #991b1b;
        background: #fef2f2;
        border: 1px solid #fca5a5;
        border-radius: .5rem;
    }
    .system-validation-summary ul { margin: .5rem 0 0 1.25rem; list-style: disc; }
    .system-field-error { display: block; margin-top: .25rem; color: #dc2626; font-size: .875rem; }
    .system-invalid-field { border-color: #dc2626 !important; box-shadow: 0 0 0 1px #dc2626 !important; }
    .dark .system-validation-summary { color: #fecaca; background: #450a0a; border-color: #991b1b; }
</style>

<script type="application/json" id="system-validation-errors">@json($errors->getMessages())</script>
<script src="{{ asset('js/form-validation.js') }}" defer></script>
