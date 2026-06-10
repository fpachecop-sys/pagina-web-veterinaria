<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Panel - Veterinaria Ralah Pets</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin: 30px 0;
        }
        
        .dashboard-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            transition: transform 0.3s ease;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        
        .dashboard-card.secondary {
            background: linear-gradient(135deg, var(--secondary) 0%, #2980b9 100%);
        }
        
        .dashboard-card.warning {
            background: linear-gradient(135deg, var(--warning) 0%, #e67e22 100%);
        }
        
        .dashboard-card.success {
            background: linear-gradient(135deg, var(--success) 0%, #1e8449 100%);
        }
        
        .dashboard-card-icon {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .dashboard-card-value {
            font-size: 2.5em;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .dashboard-card-label {
            font-size: 1em;
            opacity: 0.9;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
            background: white;
            border: 2px solid var(--light);
            border-radius: 15px;
            text-decoration: none;
            color: var(--dark);
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(22, 160, 133, 0.2);
        }
        
        .action-btn-icon {
            font-size: 3em;
            margin-bottom: 10px;
        }
        
        .proxima-cita-card {
            background: white;
            border-left: 5px solid var(--primary);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin: 30px 0;
        }
        
        .proxima-cita-card h3 {
            color: var(--primary);
            margin-bottom: 15px;
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
            <a href="index.php?entidad=usuario&accion=misMascotas" class="nav-link">Mis Mascotas</a>
            <a href="index.php?entidad=usuario&accion=misCitas" class="nav-link">Mis Citas</a>
            
            <!-- NUEVOS ENLACES -->
            <a href="index.php?entidad=video&accion=verPublico" class="nav-link">Videos</a>
            <a href="index.php?entidad=reclamacion&accion=mostrarFormulario" class="nav-link">Reclamaciones</a>
            <a href="index.php?entidad=sugerencia&accion=mostrarFormulario" class="nav-link">Sugerencias</a>
            <a href="index.php?entidad=ubicacion&accion=ver" class="nav-link">Ubicación</a>
            
            <a href="index.php?entidad=auth&accion=logout" class="btn-login">Cerrar Sesión</a>
        </nav>
    </div>
</header>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Bienvenida -->
        <div class="card">
            <h2 style="color: var(--primary); margin-bottom: 10px;">
                ¡Bienvenido, <?= htmlspecialchars($usuario['nombre']) ?>! 👋
            </h2>
            <p style="color: var(--gray); font-size: 1.1em;">
                Este es tu panel de usuario. Desde aquí puedes gestionar tus mascotas, citas y más.
            </p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="dashboard-card-icon">🐾</div>
                <div class="dashboard-card-value"><?= $stats['total_mascotas'] ?></div>
                <div class="dashboard-card-label">Mis Mascotas</div>
            </div>
            
            <div class="dashboard-card secondary">
                <div class="dashboard-card-icon">📅</div>
                <div class="dashboard-card-value"><?= $stats['total_citas'] ?></div>
                <div class="dashboard-card-label">Total de Citas</div>
            </div>
            
            <div class="dashboard-card warning">
                <div class="dashboard-card-icon">⏰</div>
                <div class="dashboard-card-value"><?= $stats['citas_pendientes'] ?></div>
                <div class="dashboard-card-label">Citas Pendientes</div>
            </div>
        </div>

        <!-- Próxima Cita -->
        <?php if ($stats['proxima_cita']): ?>
        <div class="proxima-cita-card">
            <h3>📅 Próxima Cita Programada</h3>
            <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($stats['proxima_cita']['fecha'])) ?></p>
            <p><strong>Hora:</strong> <?= date('H:i', strtotime($stats['proxima_cita']['hora'])) ?></p>
            <p><strong>Mascota:</strong> <?= htmlspecialchars($stats['proxima_cita']['nombre_mascota']) ?></p>
            <p><strong>Veterinario:</strong> <?= htmlspecialchars($stats['proxima_cita']['nombre_veterinario']) ?></p>
            <p><strong>Motivo:</strong> <?= htmlspecialchars($stats['proxima_cita']['motivo']) ?></p>
        </div>
        <?php else: ?>
        <div class="card" style="text-align: center; padding: 40px;">
            <p style="color: var(--gray); font-size: 1.1em;">No tienes citas próximas programadas</p>
            <a href="index.php?entidad=usuario&accion=misCitas" class="btn btn-primary" style="margin-top: 15px;">
                Agendar Nueva Cita
            </a>
        </div>
        <?php endif; ?>

        <!-- Acciones Rápidas -->
       

        <div class="card">
            <h3 style="color: var(--primary); margin-bottom: 25px;">⚡ Acciones Rápidas</h3>
            <div class="quick-actions">
                <a href="index.php?entidad=usuario&accion=misMascotas" class="action-btn">
                    <div class="action-btn-icon">🐕</div>
                    <span>Ver Mis Mascotas</span>
                </a>
                <a href="index.php?entidad=usuario&accion=misCitas" class="action-btn">
                    <div class="action-btn-icon">📅</div>
                    <span>Mis Citas</span>
                </a>
                <a href="index.php?entidad=usuario&accion=verPerfil" class="action-btn">
                    <div class="action-btn-icon">👤</div>
                    <span>Mi Perfil</span>
                </a>

                <!-- NUEVOS BOTONES -->
                <a href="index.php?entidad=video&accion=verPublico" class="action-btn">
                    <div class="action-btn-icon">🎥</div>
                    <span>Ver Videos</span>
                </a>
                <a href="index.php?entidad=reclamacion&accion=mostrarFormulario" class="action-btn">
                    <div class="action-btn-icon">📝</div>
                    <span>Reclamaciones</span>
                </a>
                <a href="index.php?entidad=sugerencia&accion=mostrarFormulario" class="action-btn">
                    <div class="action-btn-icon">💡</div>
                    <span>Sugerencias</span>
                </a>
                <a href="index.php?entidad=ubicacion&accion=ver" class="action-btn">
                    <div class="action-btn-icon">📍</div>
                    <span>Nuestra Ubicación</span>
                </a>
            </div>
        </div>
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