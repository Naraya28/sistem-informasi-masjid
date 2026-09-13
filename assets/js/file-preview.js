document.querySelectorAll('[data-file-upload]').forEach(function (upload) {
    const input = upload.querySelector('input[type="file"]');
    const preview = upload.querySelector('[data-file-preview]');
    const name = upload.querySelector('[data-file-name]');
    const size = upload.querySelector('[data-file-size]');

    if (!input || !preview) return;

    let objectUrl = null;

    input.addEventListener('change', function () {
        const file = input.files && input.files[0];
        if (!file) return;

        if (name) name.textContent = file.name;
        if (size) size.textContent = formatFileSize(file.size);

        if (!file.type.startsWith('image/')) return;

        if (objectUrl) URL.revokeObjectURL(objectUrl);
        objectUrl = URL.createObjectURL(file);
        preview.src = objectUrl;
        preview.hidden = false;
        upload.classList.add('has-file');
    });

    window.addEventListener('beforeunload', function () {
        if (objectUrl) URL.revokeObjectURL(objectUrl);
    });
});

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}
