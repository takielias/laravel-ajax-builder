<button
    type="submit"
    {{ $attributes->merge(['class' => $class ?? 'btn btn-primary ajax-submit-button has-spinner']) }}
>
    {{ $title ?? 'Submit' }}
</button>
