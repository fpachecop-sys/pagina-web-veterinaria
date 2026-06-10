<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Video</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<h2>✏️ Editar Video</h2>

<form action="index.php?entidad=video&accion=actualizar" method="POST">

    <input type="hidden" name="id" value="<?= $video['id'] ?>">

    <div class="mb-3">
        <label class="form-label">Título</label>
        <input type="text" name="titulo" class="form-control" value="<?= $video['titulo'] ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-control" required><?= $video['descripcion'] ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">URL del Video (iframe src)</label>
        <input type="text" name="url_video" class="form-control" value="<?= $video['url_video'] ?>" required>
        <small class="text-muted">Ejemplo: https://www.youtube.com/embed/XXXXX</small>
    </div>

    <div class="mb-3">
        <label class="form-label">Categoría</label>
        <input type="text" name="categoria" class="form-control" value="<?= $video['categoria'] ?>" required>
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" name="activo" class="form-check-input" <?= $video['activo'] ? 'checked' : '' ?>>
        <label class="form-check-label">Activo</label>
    </div>

    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    <a href="index.php?entidad=video&accion=listar" class="btn btn-secondary">Cancelar</a>
</form>

</body>
</html>
