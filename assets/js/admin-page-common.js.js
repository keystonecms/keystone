// admin-page-common.js

// ---------- debounce ----------
function debounce(fn, delay = 500) {
    let timer = null;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}

function getCsrfInput() {
    return document.querySelector('input[name="_csrf_token"]');
}


// ---------- CSRF ----------
function csrfHeaders() {
    
    const csrfInput = getCsrfInput();

    return {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfInput ? csrfInput.value : ''
    };
}


// ---------- autosave status ----------
function setAutosaveStatus(state) {
    const el = document.getElementById('autosave-status');
    if (!el) return;

    switch (state) {
        case 'saving':
            el.textContent = 'Saving…';
            el.className = 'text-muted small';
            break;
        case 'saved':
            el.textContent = 'Saved';
            el.className = 'text-success small';
            break;
        case 'error':
            el.textContent = 'Error saving';
            el.className = 'text-danger small';
            break;
    }
}
