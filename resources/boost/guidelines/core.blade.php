## Laravel Ajax Builder (Lab)

@verbatim
takielias/lab is a fluent server-side AJAX response builder paired with native fetch + auto-binding JS helpers. The PHP `Lab` class returns a `JsonResponse` carrying message, alert HTML, validation errors, redirect, and view fragments. The JS layer binds to `.ajax-submit-button` clicks (emitted by the `@submit` Blade directive) and renders results into `@alert` placeholders. Phase-7 revamp dropped jQuery — pure fetch + native DOM.
@endverbatim

### Install

@verbatim
<code-snippet name="install" lang="bash">
composer require takielias/lab
php artisan lab:install
npm install && npm run dev
</code-snippet>
@endverbatim

For guided install + smoke test, type `/laravel-boost:install-laravel-ajax-builder` in Claude Code.

### Conventions

- Composer name is `takielias/lab` (NOT `takielias/laravel-ajax-builder` — repo URL uses the long form, package name is short).
- Service provider `Takielias\Lab\LabServiceProvider` auto-discovered. Facade alias `Lab` → `Takielias\Lab\Facades\Lab` registered.
- `lab:install` patches `resources/js/app.js` to import the helpers and scaffolds `config/lab.php`.
- Server-side: build the response with the `Lab` facade. Always end with `->toJsonResponse()`.
- Client-side: drop `@alert` directive where alert messages should appear, `@submit` directive for the submit button. JS auto-binds `.ajax-submit-button` clicks via fetch.
- CSRF token meta tag REQUIRED in layout `<head>`: `<meta name="csrf-token" content="{{ csrf_token() }}">`. Read by the JS for non-GET requests.
- Convenience setters: `setSuccess($msg)`, `setInfo($msg)`, `setWarning($msg)`, `setDanger($msg)` — set message + alert type in one call.
- Validation: in a `FormRequest::failedValidation()`, throw `HttpResponseException(Lab::setStatus(422)->setValidationError($validator)->toJsonResponse())`. Field errors auto-populate per-input AND a top alert (configurable).
- Outcome routing: `setRedirect($url)` triggers post-success navigation. `enableScrollToTop()` scrolls to alert. `setRedirectDelay(ms)` waits before navigating.

### Common pitfalls

- **`@submit` button does nothing** — `npm run build`/`dev` not run after `lab:install`. JS bindings missing.
- **419 Page Expired on POST** — CSRF meta tag missing in active layout.
- **Plain JSON renders in browser** — `lab:install` failed silently or app.js not built. Inspect `resources/js/app.js` for the Lab import.
- **Validation errors don't show per field** — `disableIndividualValidationError()` was called somewhere; remove or invert via `enableTopValidationError()` for layout choice.
- **Blade `@submit('Save', 'btn-success')`** — second arg is EXTRA classes; defaults `btn btn-primary ajax-submit-button has-spinner` are appended automatically.

### See also

- `laravel-ajax-builder-development` — full Lab method catalog, Blade directives, JS helpers, validation flow, recipes.
- Slash command `/laravel-boost:install-laravel-ajax-builder`.
- Source: `src/Lab.php`, `src/LabServiceProvider.php`, `src/Commands/InstallLAB.php`, `resources/js/load.js`.
