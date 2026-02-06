var tinymceConfig = {
    selector: '.tinymce-editor',
    language: 'fr_FR',
    height: 500,
    menubar: false,
    plugins: [
        'lists',
        'link',
        'table',
        'wordcount',
        'autosave'
    ],
    toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link table | removeformat',
    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; font-size: 14px; }',
    autosave_interval: '30s',
    autosave_prefix: 'miage-rapport-{path}{query}-',
    autosave_restore_when_empty: true,
    autosave_retention: '1440m',
    branding: false,
    promotion: false,
    setup: function(editor) {
        editor.on('change', function() {
            editor.save();
            var textarea = document.querySelector('.tinymce-editor');
            if (textarea) {
                textarea.dispatchEvent(new Event('input'));
            }
        });
    }
};

function initTinyMCE() {
    if (typeof tinymce !== 'undefined') {
        tinymce.init(tinymceConfig);
    }
}

document.addEventListener('DOMContentLoaded', initTinyMCE);
