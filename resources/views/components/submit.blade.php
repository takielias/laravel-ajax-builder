<button
    type="submit"
    {{ $attributes->merge(['class' => 'ajax-submit-button has-spinner'])->except(['askConfirmation']) }}
    @if(isset($askConfirmation) &&  $askConfirmation !== null)
        data-confirm="{{ $askConfirmation }}"
    @endif
>
    {{ $title ?? 'Submit' }}
</button>
