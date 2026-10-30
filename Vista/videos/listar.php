<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Videos - Veterinaria Ralah Pets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        h1 {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
        }
        .btn-add {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
            color: white;
        }
        .videos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        .video-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .video-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        .video-container {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%;
            background: #000;
        }
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .video-info {
            padding: 20px;
        }
        .video-info h3 {
            color: #2c3e50;
            font-size: 1.2em;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .video-info p {
            color: #7f8c8d;
            font-size: 0.9em;
            margin-bottom: 15px;
        }
        .video-actions {
            display: flex;
            gap: 10px;
            justify-content: space-between;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }
        .btn-sm {
            padding: 8px 16px;
            font-size: 12px;
            border-radius: 6px;
        }
        .admin-section {
            background: #fff3cd;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>🎥 Videos de Servicios y Productos</h1>
    
    <div class="admin-section">
        <p><strong>Panel de Administración</strong></p>
        <a href="index.php?entidad=video&accion=mostrarFormularioAgregar" class="btn btn-add">
            ➕ Agregar Nuevo Video
        </a>
    </div>

    <div class="videos-grid">
        <?php if (empty($datos)): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
                <p style="font-size: 1.2em; color: #7f8c8d;">No hay videos disponibles</p>
            </div>
        <?php else: ?>
            <?php foreach ($datos as $video): ?>
                <?php if ($video['activo']): ?>
                    <div class="video-card">
                        <div class="video-container">
                            <iframe src="<?= htmlspecialchars($video['url_video']) ?>" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                            </iframe>
                        </div>
                        <div class="video-info">
                            <h3><?= htmlspecialchars($video['titulo']) ?></h3>
                            <p><?= htmlspecialchars($video['descripcion']) ?></p>
                            <div style="margin-bottom: 15px;">
                                <span class="badge bg-primary"><?= htmlspecialchars($video['categoria']) ?></span>
                                <span class="badge <?= $video['activo'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $video['activo'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </div>
                            <div class="video-actions">
                                <a href="index.php?entidad=video&accion=mostrarFormularioEditar&id=<?= $video['id'] ?>" 
                                   class="btn btn-sm btn-warning">Editar</a>
                                <a href="index.php?entidad=video&accion=eliminar&id=<?= $video['id'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('¿Eliminar este video?')">Eliminar</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>