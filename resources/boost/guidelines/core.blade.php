## Laravel Ajax Builder (Lab)

@verbatim
takielias/lab — fluent server-side AJAX response builder + native fetch JS helpers. Composer name is `takielias/lab` (NOT `laravel-ajax-builder`). Phase-7 revamp dropped jQuery.
@endverbatim

### Install

@verbatim
<code-snippet name="install" lang="bash">
composer require takielias/lab
php artisan lab:install
npm install && npm run dev
</code-snippet>
@endverbatim

Guided install: `/laravel-boost:install-laravel-ajax-builder`.

### Conventions

- `Lab` facade (auto-discovered) builds the JsonResponse. Always end with `->toJsonResponse()`. Convenience: `setSuccess($msg)`, `setInfo`, `setWarning`, `setDanger` — set message + alert type in one call.
- Blade directives: `@alert` (target div), `@submit('Title','extra-class')` (emits `.ajax-submit-button`), `@invalid` (per-field error label).
- JS: `window.ajaxPost(url, data, success, error, complete)`, `window.ajaxGet(...)`. Auto-binds `.ajax-submit-button` clicks to fetch + FormData. CSRF token meta tag REQUIRED in layout.
- Validation in FormRequest: `throw new HttpResponseException(Lab::setStatus(422)->setValidationError($validator)->toJsonResponse())`.

### See also

- `laravel-ajax-builder-development` for full Lab method catalog, AlertType enum, recipes.
