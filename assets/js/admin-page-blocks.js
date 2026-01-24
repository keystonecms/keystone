// admin-page-blocks.js

(function () {

    if (!document.getElementById('blocks-editor')) return;

    window.blocks = Array.isArray(window.INITIAL_BLOCKS)
    ? window.INITIAL_BLOCKS
    : [];


    let activeBlockIndex = null;
    const blocks = window.blocks;

    // ---------- preview ----------
    const renderPreviewDebounced = debounce(renderPreview, 400);

    function renderPreview() {
        fetch('/admin/blocks/preview', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ blocks })
        })
        .then(r => r.json())
        .then(({ html }) => {
            const el = document.getElementById('preview');
            if (el) el.innerHTML = html;
        });
    }

// ---------- sync blocks changes ---------
function syncBlocksToForm() {
    const input = document.getElementById('blocks-input');
    if (!input) return;

    input.value = JSON.stringify(blocks);
}


// ---------- CSRF ----------
const csrfInput = document.querySelector('input[name="_csrf_token"]');

    // ---------- autosave ----------
    const autosaveBlocks = debounce(() => {
        setAutosaveStatus('saving');


    const payload = {
        content_mode: 'blocks',
        title: document.querySelector('input[name="title"]').value,
        slug: document.querySelector('input[name="slug"]').value,
        template: document.querySelector('select[name="template"]').value,
        _csrf_token: csrfInput.value,
        blocks: blocks
    };

    fetch(`/admin/pages/${PAGE_ID}/autosave`, {
        method: 'POST',
        headers: {
                'Content-Type': 'application/json'
            },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {

        if (data.csrfToken) {
            document.querySelector('input[name="_csrf_token"]').value = data.csrfToken;
        }

        setAutosaveStatus('saved ' + data.savedAt);
    })
    .catch(() => {
        setAutosaveStatus('error');
    });


    }, 2000);

function onStructureChanged() {
    syncBlocksToForm();
    renderBlockList();
    renderBlockEditor();     // mag hier
    renderPreviewDebounced();
    autosaveBlocks();
}

function onContentChanged() {
    syncBlocksToForm();
    renderPreviewDebounced();
    autosaveBlocks();
}


function onBlocksChanged() {
    renderBlockList();
    // renderBlockEditor();
    renderPreviewDebounced();
    autosaveBlocks();
}


    // ---------- block list ----------
function renderBlockList() {
    const ul = document.getElementById('block-list');
    if (!ul) return;

    ul.innerHTML = '';

    if (blocks.length === 0) {
        const li = document.createElement('li');
        li.className = 'list-group-item text-muted';
        li.innerHTML = '<em>No blocks yet. Add one using the buttons above.</em>';
        ul.appendChild(li);
        return;
    }

    blocks.forEach((block, i) => {
        const li = document.createElement('li');
        li.className = 'list-group-item list-group-item-action';
        li.textContent = block.type;
        li.onclick = () => {
            activeBlockIndex = i;
            renderBlockEditor();
        };
        ul.appendChild(li);
    });
}



    // ---------- block editor ----------
function renderBlockEditor() {
    const editor = document.getElementById('block-editor');
    if (!editor) return;

    if (
        activeBlockIndex === null ||
        !blocks[activeBlockIndex]
    ) {
        editor.innerHTML = '<em>Select a block</em>';
        return;
    }

    const block = blocks[activeBlockIndex];

    // 1️⃣ Render header + body placeholder
    editor.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <strong>${block.type} block</strong>

            <button
                type="button"
                class="btn btn-sm btn-outline-danger"
                id="delete-block">
                Delete block
            </button>
        </div>

        <div id="block-editor-body"></div>
    `;

    // 2️⃣ Bind delete AFTER render
    document
        .getElementById('delete-block')
        .addEventListener('click', () => {
            if (!confirm('Delete this block?')) return;

            blocks.splice(activeBlockIndex, 1);
            activeBlockIndex = null;
            onStructureChanged();
        });

    // 3️⃣ Render block-specific editor
    const body = document.getElementById('block-editor-body');

    switch (block.type) {
        case 'text':
            renderTextBlockEditor(block, body);
            break;
        case 'hero':
            renderHeroBlockEditor(block, body);
            break;
        case 'columns':
            renderColumnsBlockEditor(block, body);
            break;
        case 'javascript':
            renderJavaScriptBlockEditor(block, body);
            break;
        case 'css':
            renderCssBlockEditor(block, body);
            break;
        case 'meta':
            renderMetaBlockEditor(block, body);
            break;
        case 'heading':
            renderHeadingBlockEditor(block, body);
            break;
        case 'spacer':
            renderSpacerBlockEditor(block, body);
        break;
        case 'divider':
            renderDividerBlockEditor(block, body);
        break;
        case 'button':
            renderButtonBlockEditor(block, body);
        break;
        case 'image':
            renderImageBlockEditor(block, body);
        break;
    }
}


// ---------- Text block ----------
function renderTextBlockEditor(block, container) {
    block.data = block.data || {};

    container.innerHTML = `
        <label class="form-label">Text</label>
        <div contenteditable="true"
             class="form-control"
             style="min-height:120px;"></div>
    `;

    const el = container.querySelector('[contenteditable]');
    el.innerHTML = block.data.html || '';

    el.addEventListener('input', () => {
        block.data.html = el.innerHTML;
        onContentChanged();
    });
}


    // ---------- Hero block ----------
function renderHeroBlockEditor(block, container) {
    block.data = block.data || {};

    container.innerHTML = `
        <label class="form-label">Title</label>
        <input class="form-control mb-2" />

        <label class="form-label">Subtitle</label>
        <textarea class="form-control"></textarea>
    `;

    const [title, subtitle] = container.querySelectorAll('input, textarea');

    title.value = block.data.title || '';
    subtitle.value = block.data.subtitle || '';

    title.oninput = () => {
        block.data.title = title.value;
        onContentChanged();   // ✅ juiste hook
    };

    subtitle.oninput = () => {
        block.data.subtitle = subtitle.value;
        onContentChanged();   // ✅ juiste hook
    };
}

// --------------- Heading elements ----------
function renderHeadingBlockEditor(block, container) {
    block.data = block.data || {};

    container.innerHTML = `
        <label class="form-label">Text</label>
        <input class="form-control mb-2">

        <label class="form-label">Level</label>
        <select class="form-select mb-2">
            <option value="1">H1</option>
            <option value="2">H2</option>
            <option value="3">H3</option>
            <option value="4">H4</option>
            <option value="5">H5</option>
            <option value="6">H6</option>
        </select>

        <label class="form-label">Alignment</label>
        <select class="form-select">
            <option value="left">Left</option>
            <option value="center">Center</option>
            <option value="right">Right</option>
        </select>
    `;

    const [textInput, levelSelect, alignSelect] =
        container.querySelectorAll('input, select');

    textInput.value = block.data.text || '';
    levelSelect.value = block.data.level || 2;
    alignSelect.value = block.data.align || 'left';

    textInput.oninput = () => {
        block.data.text = textInput.value;
        onContentChanged();
    };

    levelSelect.onchange = () => {
        block.data.level = parseInt(levelSelect.value, 10);
        onContentChanged();
    };

    alignSelect.onchange = () => {
        block.data.align = alignSelect.value;
        onContentChanged();
    };
}

    // ---------- Columns block ----------
function renderColumnsBlockEditor(block, container) {
    block.data.columns = block.data.columns || [];

    container.innerHTML = '';

    block.data.columns.forEach((col, colIndex) => {
        col.blocks = col.blocks || [];

        const wrapper = document.createElement('div');
        wrapper.className = 'border p-2 mb-2';

        wrapper.innerHTML = `<strong>Column ${colIndex + 1}</strong>`;

        col.blocks.forEach((childBlock, childIndex) => {
            if (childBlock.type === 'text') {
                const textarea = document.createElement('textarea');
                textarea.className = 'form-control mt-2';
                textarea.value = childBlock.data.html || '';

                textarea.oninput = () => {
                    childBlock.data.html = textarea.value;
                    onContentChanged();
                };

                wrapper.appendChild(textarea);
            }
        });

        const addBtn = document.createElement('button');
        addBtn.type = 'button';
        addBtn.className = 'btn btn-sm btn-outline-secondary mt-2';
        addBtn.textContent = '+ Add text';

        addBtn.onclick = () => {
            col.blocks.push({
                type: 'text',
                data: { html: '' }
            });
            onStructureChanged();
        };

        wrapper.appendChild(addBtn);
        container.appendChild(wrapper);
    });
}



    // ---------- javascript block -------------
function renderJavaScriptBlockEditor(block) {
    const editor = document.getElementById('block-editor');

    editor.innerHTML = `
        <div class="alert alert-warning small">
            ⚠ JavaScript runs on the frontend of this page.
        </div>

        <label class="form-label">External script URL</label>
        <input class="form-control mb-2" placeholder="https://…" />

        <label class="form-label">Inline JavaScript</label>
        <textarea class="form-control mb-2" rows="6"
                  placeholder="console.log('hello');"></textarea>

        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="js-async">
            <label class="form-check-label" for="js-async">Async</label>
        </div>

        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="js-defer">
            <label class="form-check-label" for="js-defer">Defer</label>
        </div>
    `;

    const [src, inline] = editor.querySelectorAll('input, textarea');
    const async = editor.querySelector('#js-async');
    const defer = editor.querySelector('#js-defer');

    src.value = block.data.src || '';
    inline.value = block.data.inline || '';
    async.checked = !!block.data.async;
    defer.checked = !!block.data.defer;

    src.oninput = () => { block.data.src = src.value; onContentChanged(); };
    inline.oninput = () => { block.data.inline = inline.value; onContentChanged(); };
    async.onchange = () => { block.data.async = async.checked; onContentChanged(); };
    defer.onchange = () => { block.data.defer = defer.checked; onContentChanged(); };
}

// ------------- Meta Info -------------
function renderMetaBlockEditor(block, container) {
    block.data = block.data || {};
    block.data.og = block.data.og || {};

    container.innerHTML = `
        <label class="form-label">Title</label>
        <input class="form-control mb-2">

        <label class="form-label">Description</label>
        <textarea class="form-control mb-2"></textarea>

        <label class="form-label">Robots</label>
        <input class="form-control mb-2" placeholder="index,follow">

        <label class="form-label">OG Title</label>
        <input class="form-control mb-2">

        <label class="form-label">OG Description</label>
        <textarea class="form-control mb-2"></textarea>

        <label class="form-label">OG Image</label>
        <input class="form-control mb-2">
    `;

    const inputs = container.querySelectorAll('input, textarea');

    inputs[0].value = block.data.title || '';
    inputs[1].value = block.data.description || '';
    inputs[2].value = block.data.robots || '';

    inputs[3].value = block.data.og.title || '';
    inputs[4].value = block.data.og.description || '';
    inputs[5].value = block.data.og.image || '';

    inputs.forEach(() => {
        block.data.title = inputs[0].value;
        block.data.description = inputs[1].value;
        block.data.robots = inputs[2].value;
        block.data.og = {
            title: inputs[3].value,
            description: inputs[4].value,
            image: inputs[5].value
        };
        onContentChanged();
    });
}
// ------------- Space Editor ------------
function renderSpacerBlockEditor(block, container) {
    block.data = block.data || {};

    container.innerHTML = `
        <label class="form-label">Size</label>
        <select class="form-select">
            <option value="sm">Small</option>
            <option value="md">Medium</option>
            <option value="lg">Large</option>
        </select>
    `;

    const select = container.querySelector('select');
    select.value = block.data.size || 'md';

    select.onchange = () => {
        block.data.size = select.value;
        onContentChanged();
    };
}

// ------------- Divider Block ---------
function renderDividerBlockEditor(block, container) {
    block.data = block.data || {};

    container.innerHTML = `
        <label class="form-label">Style</label>
        <select class="form-select">
            <option value="line">Line</option>
            <option value="dashed">Dashed</option>
            <option value="space">Space only</option>
        </select>
    `;

    const select = container.querySelector('select');
    select.value = block.data.style || 'line';

    select.onchange = () => {
        block.data.style = select.value;
        onContentChanged();
    };
}

// ------------- Button Editor --------
function renderButtonBlockEditor(block, container) {
    block.data = block.data || {};

    container.innerHTML = `
        <label class="form-label">Label</label>
        <input class="form-control mb-2">

        <label class="form-label">URL</label>
        <input class="form-control mb-2">

        <label class="form-label">Style</label>
        <select class="form-select mb-2">
            <option value="primary">Primary</option>
            <option value="secondary">Secondary</option>
            <option value="outline-primary">Outline</option>
        </select>

        <label class="form-label">Alignment</label>
        <select class="form-select">
            <option value="left">Left</option>
            <option value="center">Center</option>
            <option value="right">Right</option>
        </select>
    `;

    const [label, url, style, align] =
        container.querySelectorAll('input, select');

    label.value = block.data.label || '';
    url.value = block.data.url || '';
    style.value = block.data.style || 'primary';
    align.value = block.data.align || 'left';

    label.oninput = () => {
        block.data.label = label.value;
        onContentChanged();
    };

    url.oninput = () => {
        block.data.url = url.value;
        onContentChanged();
    };

    style.onchange = () => {
        block.data.style = style.value;
        onContentChanged();
    };

    align.onchange = () => {
        block.data.align = align.value;
        onContentChanged();
    };
}

// ------------ Image Block -----------
function renderImageBlockEditor(block, container) {
    block.data = block.data || {};

    container.innerHTML = `
        <div class="mb-2">
            <button
                type="button"
                class="btn btn-sm btn-outline-primary"
                id="select-image">
                Select image
            </button>
        </div>

        <label class="form-label">Image URL</label>
        <input class="form-control mb-2">

        <label class="form-label">Alt text</label>
        <input class="form-control mb-2">

        <label class="form-label">Caption</label>
        <input class="form-control mb-2">

        <label class="form-label">Alignment</label>
        <select class="form-select mb-2">
            <option value="left">Left</option>
            <option value="center">Center</option>
            <option value="right">Right</option>
        </select>
    `;

    const inputs = container.querySelectorAll('input, select');
    const selectBtn = container.querySelector('#select-image');

    inputs[0].value = block.data.src || '';
    inputs[1].value = block.data.alt || '';
    inputs[2].value = block.data.caption || '';
    inputs[3].value = block.data.align || 'center';


    // 🔹 Open bestaande media popup / window
    selectBtn.onclick = () => {
        window.open(
            '/admin/media',   // jouw bestaande media URL
            'media',
            'width=1000,height=700'
        );
    };
    // 🔹 Ontvang selectie van media window
    function onMediaMessage(event) {
        if (!event.data || event.data.type !== 'media:selected') return;

        const image = event.data.payload;

        block.data.src = image.url;
        block.data.alt = image.alt || '';

        srcInput.value = image.url;
        altInput.value = image.alt || '';

        onContentChanged();

        window.removeEventListener('message', onMediaMessage);
    }

    window.addEventListener('message', onMediaMessage);

    // 🔹 Handmatige edits
    inputs[0].oninput = () => { block.data.src = inputs[0].value; onContentChanged(); };
    inputs[1].oninput = () => { block.data.alt = inputs[1].value; onContentChanged(); };
    inputs[2].oninput = () => { block.data.caption = inputs[2].value; onContentChanged(); };
    inputs[3].onchange = () => { block.data.align = inputs[3].value; onContentChanged(); };
}

// ------------- CSS block -------------
function renderCssBlockEditor(block) {
    const editor = document.getElementById('block-editor');

    editor.innerHTML = `
        <div class="alert alert-warning small">
            ⚠ CSS affects the styling of this page.
        </div>

        <label class="form-label">Stylesheet URL</label>
        <input class="form-control mb-2" placeholder="https://…" />

        <label class="form-label">Inline CSS</label>
        <textarea class="form-control" rows="6"
                  placeholder=".my-class { color: red; }"></textarea>
    `;

    const [href, inline] = editor.querySelectorAll('input, textarea');

    href.value = block.data.href || '';
    inline.value = block.data.inline || '';

    href.oninput = () => { block.data.href = href.value; onContentChanged(); };
    inline.oninput = () => { block.data.inline = inline.value; onContentChanged(); };
}



// ---------- Handle add item buttons ----------
    document.getElementById('add-text')?.addEventListener('click', () => {
        blocks.push({ type: 'text', data: { html: '' } });
        activeBlockIndex = blocks.length - 1;
        onStructureChanged();
    });

    document.getElementById('add-hero')?.addEventListener('click', () => {
        blocks.push({ type: 'hero', data: {} });
        activeBlockIndex = blocks.length - 1;
        onStructureChanged();
    });

    document.getElementById('add-columns')?.addEventListener('click', () => {
        blocks.push({
            type: 'columns',
            data: {
                columns: [
                    { width: 6, blocks: [] },
                    { width: 6, blocks: [] }
                ]
            }
        });
        activeBlockIndex = blocks.length - 1;
        onStructureChanged();
    });


document.getElementById('add-js')?.addEventListener('click', () => {
    blocks.push({
        type: 'javascript',
        data: {
            src: '',
            inline: '',
            async: false,
            defer: false
        }
    });

    activeBlockIndex = blocks.length - 1;
    onStructureChanged();
});

document.getElementById('add-css')?.addEventListener('click', () => {
    blocks.push({
        type: 'css',
        data: {
            href: '',
            inline: ''
        }
    });

    activeBlockIndex = blocks.length - 1;
    onStructureChanged();
});


document.getElementById('add-meta')?.addEventListener('click', () => {
    blocks.push({
        type: 'meta',
        data: {
            title: '',
            description: '',
            robots: '',
            og: {}
        }
    });

    activeBlockIndex = blocks.length - 1;
    onStructureChanged();
    });

document.getElementById('add-spacer')?.addEventListener('click', () => {
    blocks.push({
        type: 'spacer',
        data: { size: 'md' }
    });

    activeBlockIndex = blocks.length - 1;
    onStructureChanged();
});



document.getElementById('add-heading')?.addEventListener('click', () => {
    blocks.push({
        type: 'heading',
        data: {
            text: '',
            level: 2,
            align: 'left'
        }
    });

    activeBlockIndex = blocks.length - 1;
    onStructureChanged();
});


document.getElementById('add-divider')?.addEventListener('click', () => {
    blocks.push({
        type: 'divider',
        data: { style: 'line' }
    });

    activeBlockIndex = blocks.length - 1;
    onStructureChanged();
});


document.getElementById('add-button')?.addEventListener('click', () => {
    blocks.push({
        type: 'button',
        data: {
            label: 'Click me',
            url: '/',
            style: 'primary',
            align: 'left'
        }
    });

    activeBlockIndex = blocks.length - 1;
    onStructureChanged();
});


document.getElementById('add-image')?.addEventListener('click', () => {
    blocks.push({
        type: 'image',
        data: {
            src: '',
            alt: '',
            caption: '',
            align: 'center',
            width: 'auto'
        }
    });

    activeBlockIndex = blocks.length - 1;
    onStructureChanged();
});



    // ---------- init ----------
    renderBlockList();
    renderPreview();

})();
