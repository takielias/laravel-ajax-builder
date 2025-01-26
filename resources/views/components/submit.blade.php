<button
    type="submit"
    {{ $attributes->merge(['class' => 'ajax-submit-button has-spinner']) }}
>
    {{ $title ?? 'Submit' }}
</button>
