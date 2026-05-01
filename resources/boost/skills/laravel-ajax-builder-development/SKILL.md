---
name: laravel-ajax-builder-development
description: Build AJAX flows with takielias/lab — server-side fluent response builder (Lab class) for message, alert, validation errors, redirect, view fragment; @alert / @submit / @invalid Blade directives; window.ajaxPost/Get JS helpers + .ajax-submit-button auto-bind; FormRequest validation pattern; pairs with tablar-kit confirm-modal.
---

# Laravel Ajax Builder (Lab)

## When to use this skill

- Submitting a Blade form via AJAX with native fetch (no jQuery).
- Returning a styled alert + redirect from a controller.
- Wiring per-field + top-level validation errors from a `FormRequest`.
- Surfacing flash messages without a page reload.
- Pairing with tablar-kit's confirm-modal for delete flows.

## Architecture

Three layers cooperating:

```
Blade view  ── @alert + @submit ──>  fetch (POST/GET)  ──>  Controller
                  ▲                                              │
                  │                  Lab::...->toJsonResponse() <┘
                  │
                  └── window.ajaxPost / .ajax-submit-button auto-binder renders the JSON
```

- **Server side:** the `Lab` facade builds a `JsonResponse` carrying `message`, `alert` HTML, `data`, `redirect`, `validation_errors`, plus UX flags (`scroll_to_top`, `fade_out_time`, `redirect_delay`).
- **Client side:** `window.ajaxPost(url, data, success, error, complete)` handles fetch + CSRF. The auto-binder hooks `.ajax-submit-button` clicks (emitted by `@submit`) and submits the parent form via `FormData`.
- **Blade directives:** `@alert` emits the alert target div, `@submit('Save')` emits a `<button class="btn btn-primary ajax-submit-button has-spinner">Save</button>`, `@invalid` emits an inline error label.

## Installation recap

```bash
composer require takielias/lab
php artisan lab:install
npm install && npm run dev
```

`lab:install` patches `resources/js/app.js` to import the JS modules and writes `config/lab.php`. CSRF meta tag must already be in the active layout `<head>`: `<meta name="csrf-token" content="{{ csrf_token() }}">`.

## Lab fluent API (verified `src/Lab.php`)

### Status + message

| Method | Signature | Effect |
|---|---|---|
| `setStatus` | `(int $status)` | HTTP status of the response. Default 200. |
| `setMessage` | `(?string $message)` | Plain message text shown in the alert. |
| `setData` | `(array $data)` | Arbitrary payload returned to the client (read in your `success` callback). |
| `setRedirect` | `(?string $redirect)` | URL to navigate to after the alert displays. |

### Convenience (message + alert type in one call)

| Method | Signature | Sets |
|---|---|---|
| `setInfo` | `(string $message = 'Info !!!')` | message + AlertType::info |
| `setSuccess` | `(string $message = 'Success !!!')` | message + AlertType::success |
| `setWarning` | `(string $message = 'Warning !!!')` | message + AlertType::warning |
| `setDanger` | `(string $message = 'Danger !!!')` | message + AlertType::danger |

### Alert rendering

| Method | Signature | Effect |
|---|---|---|
| `setAlert` | `(?View $alert)` | Render a custom Blade view as the alert HTML. |
| `setAlertView` | `(string $type)` | Pick built-in alert template: `info\|success\|warning\|danger\|validation-error`. |
| `renderAlert` | — | Compile the alert into the response. |
| `setIconClass` | `(?string $iconClass)` | Override the alert icon class. |
| `disableFadeOut` | — | Keep alert visible until manually dismissed. |
| `setFadeOutTime` | `(int $time_out)` | Fade-out delay in ms. |

### View rendering (return rendered Blade in the response)

| Method | Signature | Effect |
|---|---|---|
| `setView` | `(?View $view)` | View instance to render. |
| `setViewPath` | `(string $path)` | Blade view name (alternative to `setView`). |
| `setViewData` | `(array $data)` | Variables for the view. |
| `renderView` | — | Compile the view into the response. |
| `getViewPath` | — | Read back the configured path. |
| `getViewData` | — | Read back the configured data. |

### UX flags

| Method | Signature | Effect |
|---|---|---|
| `enableScrollToTop` | — | Scroll the page to the alert after response. |
| `setRedirectDelay` | `(int $delay)` | ms to wait before redirect. |
| `setSubmitButtonLabel` | `(string $label)` | Override `@submit` button text from server. |

### Validation

| Method | Signature | Effect |
|---|---|---|
| `setValidationError` | `(Validator $validator)` | Map `$validator->errors()` into per-field invalid markers + top alert. |
| `setValidationAlertView` | `(Validator $validator)` | Render the validation error list as the alert HTML. |
| `enableTopValidationError` | — | Show the top alert in addition to per-field labels. |
| `disableIndividualValidationError` | — | Suppress per-field labels (top alert only). |

### Output

| Method | Returns | Effect |
|---|---|---|
| `asAjax` | `static` | Mark response as AJAX (also runs `renderView` + `renderAlert` internally). |
| `toJsonResponse` | `JsonResponse` | Final method — return this from your controller. |

## Blade directives (verified `LabServiceProvider`)

### `@alert`

Renders `view('lab-alert::alert')`. Drop where messages should land:

```blade
<div class="card-body">
    @alert
    {{-- form fields here --}}
</div>
```

### `@submit`

```blade
@submit
@submit('Save changes')
@submit('Save', 'btn-success')
@submit('Save', 'btn-success btn-lg')
```

Output: `<button type="submit" class="{custom} btn btn-primary ajax-submit-button has-spinner">{title}</button>`.

- First arg: button label (default `'Submit'`).
- Subsequent args (joined by space): EXTRA classes prepended; defaults `btn btn-primary ajax-submit-button has-spinner` are always appended.

### `@invalid`

Renders `view('lab-alert::invalid-label')`. Pair with each input to show per-field validation errors. Position immediately after the input.

## JS helpers (verified `resources/js/modules/ajax-request.js`)

```js
window.ajaxPost(url, data, successCallback, errorCallback, completeCallback);
window.ajaxGet(url, data, successCallback, errorCallback, completeCallback);
```

Signature args:

- `url` — endpoint.
- `data` — `FormData` instance OR plain object (auto-converted).
- `successCallback(response)` — runs on 2xx.
- `errorCallback(error)` — runs on 4xx/5xx or fetch rejection.
- `completeCallback()` — runs in finally.

Auto-binder: any click on `.ajax-submit-button` finds the parent `<form>`, builds `FormData`, and submits via `ajaxPost` (or `ajaxGet` if form `method="GET"`). CSRF token auto-attached for non-GET.

## End-to-end pattern

### View

```blade
<form class="card" action="{{ route('products.store') }}" method="POST">
    @csrf
    <div class="card-header">
        <h3 class="card-title">New product</h3>
    </div>
    <div class="card-body">
        @alert

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" />
            @invalid
        </div>

        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" name="price" class="form-control" />
            @invalid
        </div>
    </div>
    <div class="card-footer text-end">
        @submit('Save')
    </div>
</form>
```

### FormRequest

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Takielias\Lab\Facades\Lab;

class SaveProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => ['required'],
            'price' => ['required', 'numeric', 'gt:0'],
        ];
    }

    protected function failedValidation($validator)
    {
        throw new HttpResponseException(
            Lab::setStatus(422)
                ->enableScrollToTop()
                ->setValidationError($validator)
                ->toJsonResponse()
        );
    }
}
```

### Controller

```php
public function store(SaveProductRequest $request)
{
    $validated = $request->validated();
    Product::create($validated);

    return Lab::setData(['success' => true])
        ->enableScrollToTop()
        ->setRedirect(route('products.index'))
        ->setSuccess('Product created successfully')
        ->setStatus(201)
        ->toJsonResponse();
}
```

## Recipes

### 1. Success + redirect with delay

```php
return Lab::setSuccess('Saved!')
    ->setRedirect(route('products.index'))
    ->setRedirectDelay(800)
    ->toJsonResponse();
```

### 2. Inline alert without redirect

```php
return Lab::setSuccess('Note added')
    ->disableFadeOut()
    ->toJsonResponse();
```

### 3. Render a partial view in the response

Useful when you want the JS to swap a target div with server-rendered HTML:

```php
return Lab::setViewPath('partials.product-card')
    ->setViewData(['product' => $product])
    ->renderView()
    ->setSuccess('Product saved')
    ->toJsonResponse();
```

Client side: `window.ajaxPost(url, data, (response) => { document.getElementById('target').innerHTML = response.view; })`.

### 4. Pair with tablar-kit confirm-modal

```blade
<x-confirm
    :url="route('products.destroy', $product)"
    method="DELETE"
    :title="'Delete '.$product->name.'?'"
    button="Delete"
    class="btn btn-sm btn-danger">
    <i class="ti ti-trash"></i>
</x-confirm>
```

```php
public function destroy(Product $product)
{
    $product->delete();

    return Lab::setSuccess('Product deleted')
        ->setRedirect(route('products.index'))
        ->toJsonResponse();
}
```

Both packages expect the same JSON `message` field — the confirm-modal toast displays it on success.

### 5. Custom validation alert HTML

```php
protected function failedValidation($validator)
{
    throw new HttpResponseException(
        Lab::setStatus(422)
            ->setValidationAlertView($validator)  // renders error list AS the alert
            ->disableIndividualValidationError()  // skip per-field labels
            ->enableScrollToTop()
            ->toJsonResponse()
    );
}
```

## AlertType enum (`src/Enums/AlertType.php`)

```php
case info = 'info';
case success = 'success';
case warning = 'warning';
case danger = 'danger';
case validationError = 'validation-error';
```

Pass these values to `setAlertView(string $type)` — typically you'd use the `setSuccess/setInfo/...` shortcuts instead.

## Pitfalls

- **`@submit('Save')` clicked but no fetch fires** — `npm run build` not run after `lab:install`. JS module not bundled.
- **Form submits as a normal POST + page reload** — the parent form's submit-event preventDefault is wired by the auto-binder; if you intercept `submit` event yourself, you may break it. Test in DevTools network tab.
- **CSRF 419** — `<meta name="csrf-token">` missing in the active layout (`master.blade.php` for tablar). Add it.
- **`@invalid` shows nothing** — it renders only when the validation flow ran. Ensure your `FormRequest` calls `Lab::...->setValidationError($validator)`.
- **`setRedirect()` ignored** — happens when `setStatus(422)` (validation error). Validation flow short-circuits redirect by design.
- **Multiple alerts on resubmit** — `@alert` accumulates content if you don't clear. The JS layer replaces the alert div content per response; if you see stacking, check for multiple `@alert` directives in the same form.
- **`setData(['key' => 'value'])` but client can't read it** — read from `response.data` in your success callback (the auto-binder doesn't expose this; you need a custom `successCallback` via `window.ajaxPost`).

## Configuration

`config/lab.php` (published by `lab:install`) controls:
- Default validation error display mode.
- Fade-out timing.
- Default redirect delay.
- Alert template overrides.

## Related

- Slash command `/laravel-boost:install-laravel-ajax-builder` — guided install + smoke test.
- Skill `tablar-kit-confirm-modal-development` — pair confirm modals with Lab responses.
- Skill `tablar-kit-forms-development` — FormBuilder rendering + Lab submission.
- Source: `src/Lab.php`, `src/LabServiceProvider.php`, `src/Commands/InstallLAB.php`, `src/Enums/AlertType.php`, `resources/js/modules/ajax-request.js`.
