// Native button-loader using DOM APIs only.
//
// Usage: any button matching .has-spinner is automatically wired so a
// click puts the button into a loading state. Restore manually with
// window.buttonLoader(button, 'stop') once the request completes.
//
// HTML hooks supported:
//   data-btn-text   — set by the loader, holds the original label
//   data-load-text  — optional override for the loading label
//   .has-spinner    — opt-in marker class
//   .active         — applied while the button is in the loading state

const ALL_SPINNERS = '.has-spinner';

function start(button) {
    if (!button || button.disabled) return false;

    document.querySelectorAll(ALL_SPINNERS).forEach((b) => { b.disabled = true; });

    if (!button.getAttribute('data-btn-text')) {
        button.setAttribute('data-btn-text', button.textContent.trim());
    }

    const loadingLabel = button.getAttribute('data-load-text') || 'Loading';
    button.innerHTML = `<span class="spinner"><i class="ti ti-loader" title="button-loader"></i></span> ${loadingLabel}`;
    button.classList.add('active');

    return true;
}

function stop(button) {
    if (!button) return;

    const original = button.getAttribute('data-btn-text');
    if (original !== null) button.textContent = original;

    button.classList.remove('active');
    document.querySelectorAll(ALL_SPINNERS).forEach((b) => { b.disabled = false; });
}

window.buttonLoader = function (button, action) {
    if (action === 'start') return start(button);
    if (action === 'stop')  return stop(button);
};

document.querySelectorAll(ALL_SPINNERS).forEach((b) => { b.disabled = false; });
