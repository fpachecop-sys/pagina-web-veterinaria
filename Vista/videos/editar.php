<?php
$esPromocion = Video::esImagenMedia($v);
$recursoLocal = Video::esRecursoLocal($v);
?>
<div class="card video-form-card">
    <header class="video-form-heading">
        <span class="video-form-icon"><i class="fas fa-pen-to-square" aria-hidden="true"></i></span>
        <div>
            <p class="eyebrow">Biblioteca de contenido</p>
            <h2>Editar publicación</h2>
            <p>Actualiza los datos o reemplaza el archivo multimedia.</p>
        </div>
    </header>

    <form action="index.php?entidad=video&accion=actualizar" method="POST" enctype="multipart/form-data"
          data-video-form data-editing="true" data-existing-local-kind="<?= $esPromocion ? 'image' : ($recursoLocal ? 'video' : '') ?>">
        <?= \App\Support\Csrf::field() ?>
        <input type="hidden" name="id" value="<?= (int) $v['id'] ?>">

        <?php if ($recursoLocal): ?>
            <div class="current-media-preview">
                <?php if ($esPromocion): ?>
                    <img src="<?= htmlspecialchars((string) $v['url_video'], ENT_QUOTES, 'UTF-8') ?>" alt="Promoción actual">
                <?php else: ?>
                    <video controls preload="metadata"><source src="<?= htmlspecialchars((string) $v['url_video'], ENT_QUOTES, 'UTF-8') ?>"></video>
                <?php endif; ?>
                <p><i class="fas fa-paperclip" aria-hidden="true"></i> Archivo actual: <?= htmlspecialchars(basename((string) $v['url_video']), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label class="form-label" for="tipo_contenido">Tipo de publicación</label>
            <select name="tipo_contenido" id="tipo_contenido" class="form-control" required>
                <option value="video" <?= !$esPromocion ? 'selected' : '' ?>>Video</option>
                <option value="promocion" <?= $esPromocion ? 'selected' : '' ?>>Imagen de promoción</option>
            </select>
        </div>

        <div class="form-group" data-source-field>
            <label class="form-label" for="fuente">Origen del video</label>
            <select name="fuente" id="fuente" class="form-control">
                <option value="url" <?= !$recursoLocal ? 'selected' : '' ?>>Enlace de YouTube o video web</option>
                <option value="archivo" <?= $recursoLocal ? 'selected' : '' ?>>Archivo local</option>
            </select>
        </div>

        <div class="form-group" data-url-field>
            <label class="form-label" for="url_video">Enlace del video</label>
            <input type="url" name="url_video" id="url_video" class="form-control"
                   value="<?= !$recursoLocal && !$esPromocion ? htmlspecialchars((string) $v['url_video'], ENT_QUOTES, 'UTF-8') : '' ?>"
                   placeholder="https://www.youtube.com/embed/...">
        </div>

        <div class="form-group" data-file-field hidden>
            <label class="form-label" for="archivo_media">Reemplazar archivo (opcional)</label>
            <input type="file" name="archivo_media" id="archivo_media" class="form-control" accept="video/mp4,video/webm,video/ogg">
            <small class="form-hint" data-file-hint>Si no seleccionas otro, se conservará el archivo actual.</small>
        </div>

        <div class="form-group" data-category-field>
            <label class="form-label" for="categoria">Categoría</label>
            <select name="categoria" id="categoria" class="form-control" required>
                <?php foreach (['Servicios', 'Productos', 'Cuidados', 'Testimonios'] as $categoria): ?>
                    <option value="<?= $categoria ?>" <?= (string) ($v['categoria'] ?? '') === $categoria ? 'selected' : '' ?>><?= $categoria ?></option>
                <?php endforeach; ?>
                <?php if (!in_array((string) ($v['categoria'] ?? ''), ['Servicios', 'Productos', 'Cuidados', 'Testimonios', 'Promociones'], true)): ?>
                    <option value="<?= htmlspecialchars((string) $v['categoria'], ENT_QUOTES, 'UTF-8') ?>" selected><?= htmlspecialchars((string) $v['categoria'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endif; ?>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="titulo">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" maxlength="150"
                   value="<?= htmlspecialchars((string) $v['titulo'], ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="4"><?= htmlspecialchars((string) ($v['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="activo" id="activo" class="form-check-input" value="1" <?= !empty($v['activo']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="activo">Visible en la página pública</label>
        </div>

        <div class="video-form-actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-floppy-disk" aria-hidden="true"></i> Guardar cambios</button>
            <a href="index.php?entidad=video&accion=listar" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
<script src="assets/js/video-form.js" defer></script>
