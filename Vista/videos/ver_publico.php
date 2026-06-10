<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videos - Veterinaria Ralah Pets</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
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
        
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }
        
        .badge-primary {
            background: #3498db;
            color: white;
        }
    </style>
</head>
<body>
    
    <!-- Main Container -->
    <div class="main-container">
        <h2 style="color: var(--primary); margin-bottom: 30px;">🎥 Videos Informativos</h2>
        
        <?php if (empty($datos)): ?>
            <div style="text-align: center; padding: 60px 20px; background: linear-gradient(135deg, rgba(22, 160, 133, 0.05) 0%, rgba(14, 118, 100, 0.05) 100%); border-radius: 15px;">
                <div style="font-size: 5em; margin-bottom: 20px;">🎥</div>
                <h3 style="color: var(--primary); margin-bottom: 15px;">No hay videos disponibles</h3>
                <p style="color: var(--gray); font-size: 1.1em;">Pronto agregaremos contenido nuevo para ti</p>
            </div>
        <?php else: ?>
            <div class="videos-grid">
                <?php foreach ($datos as $video): ?>
                    <?php if ($video['activo']): ?>
                        <div class="video-card">
                            <div class="video-container">
                                <iframe 
                                    src="<?= htmlspecialchars($video['url_video']) ?>"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <div class="video-info">
                                <h3><?= htmlspecialchars($video['titulo']) ?></h3>
                                <p><?= htmlspecialchars($video['descripcion']) ?></p>
                                <span class="badge badge-primary"><?= htmlspecialchars($video['categoria']) ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <p><strong>© <?= date('Y') ?> Veterinaria Ralah Pets</strong></p>
            <p>Los Olivos Huandoy, San Martin de Porres - Lima, Perú</p>
            <p>📞 943 626 841 | 📧 info@veterinariaralah.com</p>
        </div>
    </footer>
</body>
</html>