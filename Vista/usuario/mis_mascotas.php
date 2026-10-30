<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Mascotas - Veterinaria Ralah Pets</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .mascotas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .mascota-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .mascota-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        
        .mascota-icon {
            font-size: 4em;
            text-align: center;
            margin-bottom: 15px;
        }
        
        .mascota-nombre {
            font-size: 1.5em;
            font-weight: 700;
            color: var(--primary);
            text-align: center;
            margin-bottom: 15px;
        }
        
        .mascota-info {
            margin: 10px 0;
        }
        
        .mascota-label {
            font-weight: 600;
            color: var(--dark);
            display: inline-block;
            width: 80px;
        }
        
        .mascota-value {
            color: var(--gray);
        }
        
        .no-mascotas {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, rgba(22, 160, 133, 0.05) 0%, rgba(14, 118, 100, 0.05) 100%);
            border-radius: 15px;
        }
        
        .no-mascotas-icon {
            font-size: 5em;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="header-container">
            <a href="index.php?entidad=usuario&accion=dashboard" class="logo">
                <span style="font-size: 2em;">🐾</span>
                <h1>Ralah Pets</h1>
            </a>
            <nav class="main-nav">
                <a href="index.php?entidad=usuario&accion=dashboard" class="nav-link">Mi Panel</a>
                <a href="index.php?entidad=usuario&accion=verPerfil" class="nav-link">Mi Perfil</a>
                <a href="index.php?entidad=usuario&accion=misMascotas" class="nav-link active">Mis Mascotas</a>
                <a href="index.php?entidad=usuario&accion=misCitas" class="nav-link">Mis Citas</a>
                <a href="index.php?entidad=ubicacion&accion=ver" class="nav-link">Ubicación</a>
                <a href="index.php?entidad=video&accion=verPublico" class="nav-link">Videos</a>
                <a href="index.php?entidad=auth&accion=logout" class="btn-login">Cerrar Sesión</a>
            </nav>
        </div>
    </header>

    <!-- Main Container -->
    <div class="main-container">
        <h2 style="color: var(--primary); margin-bottom: 30px;">🐾 Mis Mascotas</h2>
        
        <?php if (empty($mascotas)): ?>
            <div class="no-mascotas">
                <div class="no-mascotas-icon">🐕</div>
                <h3 style="color: var(--primary); margin-bottom: 15px;">No tienes mascotas registradas</h3>
                <p style="color: var(--gray); font-size: 1.1em; margin-bottom: 20px;">
                    Contacta con nuestro personal para registrar a tu mascota
                </p>
                <a href="index.php?entidad=ubicacion&accion=ver" class="btn btn-primary">
                    Ver Ubicación y Contacto
                </a>
            </div>
        <?php else: ?>
            <div class="mascotas-grid">
                <?php foreach ($mascotas as $mascota): ?>
                    <div class="mascota-card">
                        <div class="mascota-icon">
                            <?php
                            $icono = '🐾';
                            if ($mascota['especie'] == 'Perro') $icono = '🐕';
                            elseif ($mascota['especie'] == 'Gato') $icono = '🐈';
                            elseif ($mascota['especie'] == 'Ave') $icono = '🦜';
                            echo $icono;
                            ?>
                        </div>
                        <div class="mascota-nombre"><?= htmlspecialchars($mascota['nombre']) ?></div>
                        
                        <div class="mascota-info">
                            <span class="mascota-label">Especie:</span>
                            <span class="mascota-value"><?= htmlspecialchars($mascota['especie']) ?></span>
                        </div>
                        
                        <div class="mascota-info">
                            <span class="mascota-label">Raza:</span>
                            <span class="mascota-value"><?= htmlspecialchars($mascota['raza']) ?></span>
                        </div>
                        
                        <div class="mascota-info">
                            <span class="mascota-label">Edad:</span>
                            <span class="mascota-value"><?= htmlspecialchars($mascota['edad']) ?> años</span>
                        </div>
                        
                        <div class="mascota-info">
                            <span class="mascota-label">Registrado:</span>
                            <span class="mascota-value"><?= date('d/m/Y', strtotime($mascota['fecha_registro'])) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="card" style="margin-top: 40px; text-align: center; background: linear-gradient(135deg, rgba(22, 160, 133, 0.05) 0%, rgba(14, 118, 100, 0.05) 100%);">
                <p style="font-size: 1.1em; color: var(--dark); margin-bottom: 15px;">
                    ¿Tienes una nueva mascota? Contáctanos para registrarla
                </p>
                <a href="index.php?entidad=ubicacion&accion=ver" class="btn btn-primary">
                    📞 Ver Contacto
                </a>
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