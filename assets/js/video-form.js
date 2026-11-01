document.querySelectorAll('[data-video-form]').forEach((form) => {
    const typeSelect = form.querySelector('[name="tipo_contenido"]');
    const sourceSelect = form.querySelector('[name="fuente"]');
    const sourceField = form.querySelector('[data-source-field]');
    const urlField = form.querySelector('[data-url-field]');
    const fileField = form.querySelector('[data-file-field]');
    const categoryField = form.querySelector('[data-category-field]');
    const categorySelect = form.querySelector('[name="categoria"]');
    const urlInput = form.querySelector('[name="url_video"]');
    const fileInput = form.querySelector('[name="archivo_media"]');
    const fileHint = form.querySelector('[data-file-hint]');

    if (!typeSelect || !sourceSelect || !fileInput) return;

    const editing = form.dataset.editing === 'true';
    const existingKind = form.dataset.existingLocalKind || '';

    const updateFields = () => {
        const isPromotion = typeSelect.value === 'promocion';
        const source = sourceSelect.value;
        const wantsFile = isPromotion || source === 'archivo';

        if (sourceField) sourceField.hidden = isPromotion;
        if (urlField) urlField.hidden = isPromotion || source !== 'url';
        if (fileField) fileField.hidden = !wantsFile;
        if (categoryField) categoryField.hidden = isPromotion;
        if (categorySelect) categorySelect.disabled = isPromotion;

        if (urlInput) urlInput.required = !isPromotion && source === 'url';
        fileInput.accept = isPromotion
            ? 'image/jpeg,image/png,image/webp,image/gif'
            : 'video/mp4,video/webm,video/ogg';
        fileInput.required = wantsFile && (!editing || existingKind !== (isPromotion ? 'image' : 'video'));

        if (fileHint) {
            fileHint.textContent = isPromotion
                ? 'Imagen JPG, PNG, WEBP o GIF. Máximo 8 MB.'
                : (editing && existingKind === 'video' ? 'Si no seleccionas otro archivo, se conservará el video actual.' : 'Video MP4, WEBM u OGG. Máximo 120 MB.');
        }
    };

    typeSelect.addEventListener('change', updateFields);
    sourceSelect.addEventListener('change', updateFields);
    updateFields();
});
