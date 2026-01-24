// admin-page-richtext.js

(function () {
    if (!document.getElementById('quill-editor')) return;

const quill = new Quill('#quill-editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline'],
            [{ header: [2, 3, false] }],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['blockquote'],
            ['image'],
            ['clean']
        ]
    }
});

/* Media picker integration */
const toolbar = quill.getModule('toolbar');
toolbar.addHandler('image', function () {
    window.open(
        '/admin/media?picker=1',
        'mediaPicker',
        'width=900,height=600'
    );
});

/* sync quill → textarea before AJAX submit */
$('form[id*=form]').on('submit', function () {
    $('textarea[name=content_html]').val(
        quill.root.innerHTML
    );
});

/* exposed for media picker */
window.insertImage = function (url) {
    const range = quill.getSelection(true);
    quill.insertEmbed(range.index, 'image', url);
    quill.formatText(range.index, 1, 'alt', '');
    quill.setSelection(range.index + 1);
};

const autosaveRichtext = debounce(() => {
    setAutosaveStatus('saving');

// ---------- CSRF ----------
const csrfInput = document.querySelector('input[name="_csrf_token"]');

const payload = {
    content_mode: 'richtext',
    title: document.querySelector('input[name="title"]').value,
    slug: document.querySelector('input[name="slug"]').value,
    template: document.querySelector('select[name="template"]').value,
    _csrf_token: csrfInput.value,
    content_html: quill.root.innerHTML
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
                document.getElementsByName('_csrf_token').value = data.csrfToken;
            }

            setAutosaveStatus('saved');
        })
        .catch(() => {
            setAutosaveStatus('error');
        });


    }, 2000);

    quill.on('text-change', () => {
        autosaveRichtext();
    });

})();
