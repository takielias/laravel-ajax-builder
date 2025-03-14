<button
    type="submit"
    {{ $attributes->merge(['class' => 'ajax-submit-button has-spinner'])->except(['askConfirmation']) }}
    @if($askConfirmation !== null)
        data-confirm="{{ $askConfirmation }}"
    @endif
>
    {{ $title ?? 'Submit' }}
</button>
