// Native ajax helpers using fetch + FormData. Compatible with the
// existing window.ajax* API surface so user code keeps working
// unchanged. See README for usage and the phase-7 audit for the
// rewrite history.

const csrfToken = () => {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
};

window.fadeOutAndClear = function (elementId, timeout = 2000) {
    setTimeout(() => {
        const el = document.getElementById(elementId);
        if (!el) return;

        el.style.transition = 'opacity 600ms ease';
        el.style.opacity = '0';

        setTimeout(() => {
            el.innerHTML = '';
            el.style.opacity = '';
            el.style.transition = '';
        }, 600);
    }, timeout);
};

window.loadModal = function (url) {
    const target = document.getElementById('body-content');
    if (!target) return;

    fetch(url, { credentials: 'same-origin' })
        .then((response) => response.text())
        .then((html) => { target.innerHTML = html; })
        .catch((err) => console.error('loadModal failed:', err));
};

const clearFormErrors = () => {
    document
        .querySelectorAll('form .is-invalid')
        .forEach((el) => el.classList.remove('is-invalid'));
    document
        .querySelectorAll('form .invalid-feedback')
        .forEach((el) => { el.innerHTML = ''; });
};

const handleSuccess = (response, successCallback) => {
    clearFormErrors();

    if (typeof successCallback === 'function') {
        successCallback(response);
    }

    if (response.alert && !response.as_ajax) {
        const alertEl = document.getElementById('alert');
        if (alertEl) alertEl.innerHTML = response.alert;
        if (response.fade_out) {
            const timeOut = parseInt(response.fade_out_time ?? 3000, 10);
            window.fadeOutAndClear('alert', timeOut);
        }
    }

    if (response.redirect) {
        const delay = response.redirect_delay ?? 1500;
        setTimeout(() => { window.location.href = response.redirect; }, delay);
    }
};

const handleError = (response, errorCallback, originalError) => {
    clearFormErrors();

    if (response && response.data && response.data.errors && response.individual_validation_error) {
        Object.keys(response.data.errors).forEach((field) => {
            const input = document.querySelector(`[name="${field}"]`);
            if (!input) return;
            input.classList.add('is-invalid');
            const wrap = input.closest('div');
            const feedback = wrap ? wrap.querySelector('.invalid-feedback') : null;
            if (feedback) feedback.innerHTML = response.data.errors[field][0];
        });

        if (response.message && !response.top_validation_error) {
            const alertEl = document.getElementById('alert');
            if (alertEl) {
                alertEl.innerHTML = `<div class="alert alert-danger alert-dismissible" role="alert">${response.message}</div>`;
            }
        }
    }

    if (response && response.top_validation_error && response.alert) {
        const alertEl = document.getElementById('alert');
        if (alertEl) alertEl.innerHTML = response.alert;
    }

    if (typeof errorCallback === 'function') {
        errorCallback(originalError);
    }

    if (response && response.fade_out) {
        const timeOut = parseInt(response.fade_out_time ?? 3000, 10);
        window.fadeOutAndClear('alert', timeOut);
    }

    if (response && response.scroll_to_top) {
        window.scrollTo(0, 0);
    }
};

window.ajaxRequest = function (url, data = {}, successCallback, errorCallback, completeCallback, method = 'POST') {
    const upperMethod = (method || 'POST').toUpperCase();

    const init = {
        method: upperMethod,
        credentials: 'same-origin',
        headers: {
            'X-CSRF-TOKEN': csrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
            Accept: 'application/json',
        },
    };

    if (upperMethod !== 'GET') {
        if (data instanceof FormData) {
            init.body = data;
        } else if (typeof data === 'string') {
            init.body = data;
            init.headers['Content-Type'] = 'application/x-www-form-urlencoded';
        } else {
            init.body = JSON.stringify(data ?? {});
            init.headers['Content-Type'] = 'application/json';
        }
    }

    fetch(url, init)
        .then(async (response) => {
            const text = await response.text();
            let body = null;
            try { body = text ? JSON.parse(text) : null; } catch { body = text; }

            if (!response.ok) {
                handleError(body, errorCallback, { status: response.status, body });
                return;
            }

            handleSuccess(body, successCallback);
        })
        .catch((err) => {
            console.error('ajax request failed:', err);
            handleError(null, errorCallback, err);
        })
        .finally(() => {
            if (typeof completeCallback === 'function') {
                completeCallback();
            }
        });
};

window.ajaxPost = function (url, data, successCallback, errorCallback, completeCallback) {
    window.ajaxRequest(url, data, successCallback, errorCallback, completeCallback, 'POST');
};

window.ajaxGet = function (url, data, successCallback, errorCallback, completeCallback) {
    let queryString = '';
    if (data && typeof data === 'object' && Object.keys(data).length > 0) {
        queryString = '?' + new URLSearchParams(data).toString();
    }
    window.ajaxRequest(url + queryString, {}, successCallback, errorCallback, completeCallback, 'GET');
};

window.ajaxPut = function (url, data, successCallback, errorCallback, completeCallback) {
    if (data instanceof FormData) {
        data.append('_method', 'put');
    }
    window.ajaxRequest(url, data, successCallback, errorCallback, completeCallback, 'POST');
};

window.ajaxPatch = function (url, data, successCallback, errorCallback, completeCallback) {
    if (data instanceof FormData) {
        data.append('_method', 'patch');
    }
    window.ajaxRequest(url, data, successCallback, errorCallback, completeCallback, 'POST');
};

window.executeAjaxCall = (method, url, data, successCallback, errorCallback, completeCallback) => {
    let fn;
    switch ((method || 'post').toLowerCase()) {
        case 'get':   fn = window.ajaxGet;   break;
        case 'put':   fn = window.ajaxPut;   break;
        case 'patch': fn = window.ajaxPatch; break;
        default:      fn = window.ajaxPost;
    }
    fn(url, data, successCallback, errorCallback, completeCallback);
};

window.resetForm = function () {
    const form = document.getElementById('ajax-form');
    if (!form) return;

    form
        .querySelectorAll('input[type="text"], input[type="email"], input[type="password"], textarea')
        .forEach((el) => { el.value = ''; });

    form
        .querySelectorAll('input[type="radio"], input[type="checkbox"]')
        .forEach((el) => { el.checked = false; });

    form
        .querySelectorAll('select')
        .forEach((el) => { el.selectedIndex = 0; });
};

document.addEventListener('click', (event) => {
    const submitTrigger = event.target.closest('.btn-submit-action');
    if (submitTrigger) {
        const form = document.getElementById('myForm');
        if (form) form.requestSubmit();
        return;
    }

    const ajaxBtn = event.target.closest('.ajax-submit-button');
    if (!ajaxBtn) return;

    event.preventDefault();

    const confirmAttr = ajaxBtn.getAttribute('data-confirm');
    let confirmMessage = confirmAttr;
    if (confirmAttr === '1') {
        confirmMessage = 'Are you sure you want to proceed?';
    }
    if (confirmMessage && !window.confirm(confirmMessage)) {
        return;
    }

    const alertEl = document.getElementById('alert');
    if (alertEl) alertEl.innerHTML = '';

    document.querySelectorAll('[tinymce-id]').forEach((el) => {
        const id = el.getAttribute('tinymce-id');
        if (window.tinymceEditorsMap && window.tinymceEditorsMap[id]) {
            window.tinymceEditorsMap[id].triggerSave();
        }
    });

    const form = ajaxBtn.closest('form');
    if (!form) return;

    const formData = new FormData(form);
    const requestMethod = form.getAttribute('method') || 'POST';
    const action = form.getAttribute('action') || window.location.href;

    window.executeAjaxCall(
        requestMethod,
        action,
        formData,
        (data) => {
            if (data && data.submit_button_label !== null && data.submit_button_label !== undefined) {
                ajaxBtn.textContent = data.submit_button_label;
            }
        },
        (err) => { console.error('An unexpected error occurred:', err); },
        () => { /* request completed */ }
    );
});
