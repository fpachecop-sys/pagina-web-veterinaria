<div class="card video-form-card">
    <header class="video-form-heading">
        <span class="video-form-icon"><i class="fas fa-photo-film" aria-hidden="true"></i></span>
        <div>
            <p class="eyebrow">Biblioteca de contenido</p>
            <h2>Publicar video o promoción</h2>
            <p>Comparte consejos en video o una imagen con tus ofertas.</p>
        </div>
    </header>

    <form action="index.php?entidad=video&accion=guardarNuevo" method="POST" enctype="multipart/form-data" data-video-form>
        <?= \App\Support\Csrf::field() ?>
        <div class="form-group">
            <label class="form-label" for="tipo_contenido">¿Qué deseas publicar?</label>
            <select name="tipo_contenido" id="tipo_contenido" class="form-control" required>
                <option value="video">Video</option>
                <option value="promocion">Imagen de promoción</option>
            </select>
        </div>

        <div class="form-group" data-source-field>
            <label class="form-label" for="fuente">Origen del video</label>
            <select name="fuente" id="fuente" class="form-control">
                <option value="url">Enlace de YouTube o video web</option>
                <option value="archivo">Archivo de video local</option>
            </select>
        </div>

        <div class="form-group" data-url-field>
            <label class="form-label" for="url_video">Enlace del video</label>
            <input type="url" name="url_video" id="url_video" class="form-control" placeholder="https://www.youtube.com/embed/...">
            <small class="form-hint">Puedes pegar un enlace de inserción de YouTube o una URL directa http/https.</small>
        </div>

        <div class="form-group" data-file-field hidden>
            <label class="form-label" for="archivo_media">Archivo multimedia</label>
            <input type="file" name="archivo_media" id="archivo_media" class="form-control" accept="video/mp4,video/webm,video/ogg">
            <small class="form-hint" data-file-hint>Formatos de video MP4, WEBM u OGG. Máximo 120 MB.</small>
        </div>

        <div class="form-group" data-category-field>
            <label class="form-label" for="categoria">Categoría del video</label>
            <select name="categoria" id="categoria" class="form-control" required>
                <option value="Servicios">Servicios</option>
                <option value="Productos">Productos</option>
                <option value="Cuidados">Cuidados y consejos</option>
                <option value="Testimonios">Testimonios</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="titulo">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" maxlength="150" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="4" placeholder="Cuéntales a tus clientes qué encontrarán aquí."></textarea>
        </div>

        <div class="video-form-actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-cloud-arrow-up" aria-hidden="true"></i> Publicar</button>
            <a href="index.php?entidad=video&accion=listar" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
<script src="assets/js/video-form.js" defer></script>
