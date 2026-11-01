<section class="content-library">
    <header class="content-library-heading">
        <div>
            <p class="eyebrow">Administración de contenido</p>
            <h1><i class="fas fa-photo-film" aria-hidden="true"></i> Videos y Promociones</h1>
            <p>Publica videos de la clínica y promociones para tus clientes.</p>
        </div>
        <a href="index.php?entidad=video&accion=mostrarFormularioAgregar" class="btn btn-add">
            <i class="fas fa-plus" aria-hidden="true"></i> Agregar contenido
        </a>
    </header>

    <div class="table-wrapper content-table-wrap">
        <table class="table content-table">
            <thead>
                <tr>
                    <th>Vista</th>
                    <th>Título</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($datos)): ?>
                    <tr><td colspan="5" class="text-center">Todavía no hay videos ni promociones.</td></tr>
                <?php else: ?>
                    <?php foreach ($datos as $fila): ?>
                        <?php
                        $esImagen = Video::esImagenMedia($fila);
                        $esVideoDirecto = Video::esVideoDirecto($fila);
                        $urlRecurso = (string) ($fila['url_video'] ?? '');
                        $esUrlWeb = in_array(strtolower((string) parse_url($urlRecurso, PHP_URL_SCHEME)), ['http', 'https'], true);
                        ?>
                        <tr>
                            <td class="content-preview-cell">
                                <?php if ($esImagen): ?>
                                    <img class="content-preview-image" src="<?= htmlspecialchars($urlRecurso, ENT_QUOTES, 'UTF-8') ?>" alt="Promoción: <?= htmlspecialchars((string) $fila['titulo'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                                <?php elseif ($esVideoDirecto): ?>
                                    <video class="content-preview-image" controls preload="metadata"><source src="<?= htmlspecialchars($urlRecurso, ENT_QUOTES, 'UTF-8') ?>"></video>
                                <?php elseif ($esUrlWeb): ?>
                                    <div class="content-preview-placeholder"><i class="fas fa-circle-play" aria-hidden="true"></i><span>Video web</span></div>
                                <?php else: ?>
                                    <div class="content-preview-placeholder"><i class="fas fa-triangle-exclamation" aria-hidden="true"></i><span>Sin vista previa</span></div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars((string) $fila['titulo'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                            <td><span class="content-category"><?= $esImagen ? 'Promoción' : htmlspecialchars((string) $fila['categoria'], ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td><span class="content-status <?= !empty($fila['activo']) ? 'is-active' : 'is-inactive' ?>"><?= !empty($fila['activo']) ? 'Visible' : 'Oculto' ?></span></td>
                            <td class="content-actions">
                                <a href="index.php?entidad=video&accion=mostrarFormularioEditar&id=<?= (int) $fila['id'] ?>" class="btn btn-sm btn-warning" aria-label="Editar <?= htmlspecialchars((string) $fila['titulo'], ENT_QUOTES, 'UTF-8') ?>"><i class="fas fa-pen" aria-hidden="true"></i> Editar</a>
                                <form action="index.php?entidad=video&accion=eliminar" method="POST" onsubmit="return confirm('¿Eliminar esta publicación?')">
                                    <?= \App\Support\Csrf::field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $fila['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash" aria-hidden="true"></i> Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
