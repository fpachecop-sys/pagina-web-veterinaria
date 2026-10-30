<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador - Veterinaria Ralah Pets</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
    .admin-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin: 30px 0;
    }
    
    .stat-box {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 25px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .stat-box.secondary {
        background: linear-gradient(135deg, var(--secondary) 0%, #2980b9 100%);
    }
    
    .stat-box.warning {
        background: linear-gradient(135deg, var(--warning) 0%, #e67e22 100%);
    }
    
    .stat-box.success {
        background: linear-gradient(135deg, var(--success) 0%, #1e8449 100%);
    }
    
    .stat-number {
        font-size: 2.5em;
        font-weight: 700;
        margin: 10px 0;
    }
    
    .stat-label {
        font-size: 0.9em;
        opacity: 0.9;
    }
    
    .quick-links {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin: 30px 0;
    }
    
    .quick-link-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        text-align: center;
        text-decoration: none;
        color: var(--dark);
        transition: transform 0.3s ease;
    }
    
    .quick-link-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    
    .quick-link-icon {
        font-size: 3em;
        margin-bottom: 10px;
    }
    
    .recent-activity {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        margin-top: 30px;
    }
</style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="header-container">
            <a href="index.php?entidad=admin&accion=dashboard" class="logo">
                <span style="font-size: 2em;">🐾</span>
                <h1>Ralah Pets - Admin</h1>
            </a>
            <nav class="main-nav">
                <a href="index.php?entidad=admin&accion=dashboard" class="nav-link active">Dashboard</a>
                <a href="index.php?entidad=admin&accion=listarUsuarios" class="nav-link">Usuarios</a>
                <a href="index.php?entidad=auth&accion=logout" class="btn-login">Cerrar Sesión</a>
            </nav>
        </div>
    </header>
    <!-- Main Container -->
<div class="main-container">
    <h2 style="color: var(--primary); margin-bottom: 10px;">🎯 Panel de Administración</h2>
    <p style="color: var(--gray); margin-bottom: 30px;">Bienvenido, <?= htmlspecialchars($usuario['nombre']) ?></p>
    
    <!-- Estadísticas -->
    <div class="admin-stats">
        <div class="stat-box">
            <div class="stat-label">Total Mascotas</div>
            <div class="stat-number"><?= $stats['total_mascotas'] ?></div>
        </div>
        <div class="stat-box secondary">
            <div class="stat-label">Total Dueños</div>
            <div class="stat-number"><?= $stats['total_duenos'] ?></div>
        </div>
        <div class="stat-box warning">
            <div class="stat-label">Citas Pendientes</div>
            <div class="stat-number"><?= $stats['citas_pendientes'] ?></div>
        </div>
        <div class="stat-box success">
            <div class="stat-label">Total Citas</div>
            <div class="stat-number"><?= $stats['total_citas'] ?></div>
        </div>
    </div>

    <!-- Accesos Rápidos -->
    <h3 style="color: var(--primary); margin: 40px 0 20px;">⚡ Accesos Rápidos</h3>
    <div class="quick-links">
        <a href="index.php?entidad=cita&accion=mostrarFormularioAgregar" class="quick-link-card">
            <div class="quick-link-icon">📅</div>
            <strong>Agendar Cita</strong>
        </a>
        <a href="index.php?entidad=mascota&accion=mostrarFormularioAgregar" class="quick-link-card">
            <div class="quick-link-icon">🐾</div>
            <strong>Registrar Mascota</strong>
        </a>
        <a href="index.php?entidad=dueno&accion=mostrarFormularioAgregar" class="quick-link-card">
            <div class="quick-link-icon">👥</div>
            <strong>Registrar Dueño</strong>
        </a>
        <a href="index.php?entidad=veterinario&accion=mostrarFormularioAgregar" class="quick-link-card">
            <div class="quick-link-icon">⚕️</div>
            <strong>Agregar Veterinario</strong>
        </a>
        <a href="index.php?entidad=reporte&accion=ver" class="quick-link-card">
            <div class="quick-link-icon">📊</div>
            <strong>Ver Reportes</strong>
        </a>
        <a href="index.php?entidad=video&accion=listar" class="quick-link-card">
            <div class="quick-link-icon">🎥</div>
            <strong>Gestionar Videos</strong>
        </a>
    </div>

    <!-- Actividad Reciente -->
    <?php if (!empty($citasRecientes)): ?>
    <div class="recent-activity">
        <h3 style="color: var(--primary); margin-bottom: 20px;">📋 Citas Recientes</h3>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Mascota</th>
                        <th>Dueño</th>
                        <th>Veterinario</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($citasRecientes as $cita): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($cita['fecha'])) ?></td>
                        <td><?= htmlspecialchars($cita['nombre_mascota']) ?></td>
                        <td><?= htmlspecialchars($cita['nombre_dueno']) ?></td>
                        <td><?= htmlspecialchars($cita['nombre_veterinario']) ?></td>
                        <td>
                            <span class="badge badge-<?= strtolower($cita['estado']) == 'pendiente' ? 'warning' : (strtolower($cita['estado']) == 'atendida' ? 'success' : 'danger') ?>">
                                <?= htmlspecialchars($cita['estado']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Footer -->
<footer class="main-footer">
    <div class="footer-content">
        <p><strong>© <?= date('Y') ?> Veterinaria Ralah Pets - Panel de Administración</strong></p>
    </div>
</footer>
</body>
</html>
