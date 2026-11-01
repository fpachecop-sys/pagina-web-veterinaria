<?php

declare(strict_types=1);

// Los controladores procesan POST y emiten redirecciones durante el despacho,
// que sucede después de construir la plantilla común. El buffer mantiene los
// encabezados HTTP editables hasta que el controlador termine.
ob_start();

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Modelo/Usuario.php';

use App\Middleware\SessionGuardMiddleware;
use App\Support\Env;
use App\Support\ErrorHandler;
use App\Support\Helper;
use App\Support\Router;
use App\Support\Session;

Env::load(__DIR__);
ErrorHandler::register();
Session::start();
Router::sendSecurityHeaders();
SessionGuardMiddleware::verifyActiveUser();

$entidad = isset($_GET['entidad']) ? trim((string) $_GET['entidad']) : 'home';
$accion  = isset($_GET['accion']) ? trim((string) $_GET['accion']) : 'ver';

$estaAutenticado = Usuario::estaAutenticado();
$esAdmin         = Usuario::esAdministrador();
$usuarioActual   = Usuario::obtenerUsuarioActual();

Router::authorize($entidad, $accion, $estaAutenticado, $esAdmin);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria Ralah Pets - Sistema de Gestión</title>
    <link rel="stylesheet" href="assets/css/styles.css?v=5">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.5rem 1rem;
            background: rgba(22,160,133,0.08);
            border: 1px solid rgba(22,160,133,0.15);
            border-radius: 50px;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--dark);
            font-weight: 500;
        }
        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .btn-logout {
            padding: 0.5rem 1rem;
            background: rgba(239, 68, 68, 0.9);
            color: white;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-logout:hover {
            background: #dc2626;
            transform: scale(1.05);
        }
        .flash-banner {
            max-width: 1200px;
            margin: 1rem auto 0;
            padding: 1rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
        }
        .flash-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }
        .flash-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        header nav ul {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 0.25rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        header nav .nav-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.55rem 0.7rem;
            white-space: nowrap;
        }
        @media (max-width: 1100px) {
            .header-container {
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.75rem 1rem;
            }
            header nav ul {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <a href="index.php?entidad=home&accion=ver" class="logo">
                <img class="brand-logo" src="assets/img/RALAH_LOGO.png" alt="">
                <div>
                    <h1>Ralah Pets</h1>
                    <span>Clínica Veterinaria</span>
                </div>
            </a>

            <nav class="main-nav" aria-label="Navegación principal">
                <ul>
                    <?php if ($estaAutenticado && $esAdmin): ?>
                        <li><a class="nav-link <?= $entidad === 'admin' ? 'active' : '' ?>" href="index.php?entidad=admin&accion=dashboard"><i class="fas fa-th-large"></i> Panel</a></li>
                        <li><a class="nav-link <?= $entidad === 'dueno' ? 'active' : '' ?>" href="index.php?entidad=dueno&accion=listar"><i class="fas fa-users"></i> Dueños</a></li>
                        <li><a class="nav-link <?= $entidad === 'mascota' ? 'active' : '' ?>" href="index.php?entidad=mascota&accion=listar"><i class="fas fa-dog"></i> Mascotas</a></li>
                        <li><a class="nav-link <?= $entidad === 'cita' ? 'active' : '' ?>" href="index.php?entidad=cita&accion=listar"><i class="fas fa-calendar-check"></i> Citas</a></li>
                        <li><a class="nav-link <?= $entidad === 'veterinario' ? 'active' : '' ?>" href="index.php?entidad=veterinario&accion=listar"><i class="fas fa-user-md"></i> Veterinarios</a></li>
                        <li><a class="nav-link <?= $entidad === 'reporte' ? 'active' : '' ?>" href="index.php?entidad=reporte&accion=ver"><i class="fas fa-chart-bar"></i> Reportes</a></li>
                        <li><a class="nav-link <?= $entidad === 'admin' && in_array($accion, ['usuarios', 'listarUsuarios'], true) ? 'active' : '' ?>" href="index.php?entidad=admin&accion=usuarios"><i class="fas fa-user-cog"></i> Usuarios</a></li>
                        <li><a class="nav-link <?= $entidad === 'reclamacion' ? 'active' : '' ?>" href="index.php?entidad=reclamacion&accion=listar"><i class="fas fa-book"></i> Reclamaciones</a></li>
                        <li><a class="nav-link <?= $entidad === 'sugerencia' ? 'active' : '' ?>" href="index.php?entidad=sugerencia&accion=listar"><i class="fas fa-lightbulb"></i> Sugerencias</a></li>
                        <li><a class="nav-link <?= $entidad === 'video' ? 'active' : '' ?>" href="index.php?entidad=video&accion=listar"><i class="fas fa-photo-film"></i> Videos y Promociones</a></li>
                        <li><a class="nav-link <?= $entidad === 'ubicacion' ? 'active' : '' ?>" href="index.php?entidad=ubicacion&accion=ver"><i class="fas fa-map-marker-alt"></i> Ubicación</a></li>
                    <?php elseif ($estaAutenticado): ?>
                        <li><a class="nav-link <?= $entidad === 'usuario' && $accion === 'dashboard' ? 'active' : '' ?>" href="index.php?entidad=usuario&accion=dashboard"><i class="fas fa-home"></i> Inicio</a></li>
                        <li><a class="nav-link <?= $accion === 'misMascotas' ? 'active' : '' ?>" href="index.php?entidad=usuario&accion=misMascotas"><i class="fas fa-paw"></i> Mis Mascotas</a></li>
                        <li><a class="nav-link <?= $accion === 'misCitas' ? 'active' : '' ?>" href="index.php?entidad=usuario&accion=misCitas"><i class="fas fa-calendar"></i> Mis Citas</a></li>
                        <li><a class="nav-link <?= $entidad === 'ubicacion' ? 'active' : '' ?>" href="index.php?entidad=ubicacion&accion=ver"><i class="fas fa-map-marker-alt"></i> Ubicación</a></li>
                        <li><a class="nav-link <?= $entidad === 'video' ? 'active' : '' ?>" href="index.php?entidad=video&accion=verPublico"><i class="fas fa-photo-film"></i> Videos y Promociones</a></li>
                        <li><a class="nav-link <?= $entidad === 'reclamacion' ? 'active' : '' ?>" href="index.php?entidad=reclamacion&accion=crear"><i class="fas fa-book"></i> Reclamaciones</a></li>
                        <li><a class="nav-link <?= $entidad === 'sugerencia' ? 'active' : '' ?>" href="index.php?entidad=sugerencia&accion=crear"><i class="fas fa-lightbulb"></i> Sugerencias</a></li>
                        <li><a class="nav-link <?= $entidad === 'usuario' && in_array($accion, ['perfil', 'verPerfil'], true) ? 'active' : '' ?>" href="index.php?entidad=usuario&accion=perfil"><i class="fas fa-user"></i> Mi Perfil</a></li>
                    <?php else: ?>
                        <li><a class="nav-link <?= $entidad === 'home' ? 'active' : '' ?>" href="index.php?entidad=home&accion=ver"><i class="fas fa-home"></i> Inicio</a></li>
                        <li><a class="nav-link <?= $entidad === 'veterinario' ? 'active' : '' ?>" href="index.php?entidad=veterinario&accion=verPublico"><i class="fas fa-user-md"></i> Veterinarios</a></li>
                        <li><a class="nav-link <?= $entidad === 'video' ? 'active' : '' ?>" href="index.php?entidad=video&accion=verPublico"><i class="fas fa-photo-film"></i> Videos y Promociones</a></li>
                        <li><a class="nav-link <?= $entidad === 'ubicacion' ? 'active' : '' ?>" href="index.php?entidad=ubicacion&accion=ver"><i class="fas fa-map-marker-alt"></i> Ubicación</a></li>
                        <li><a class="nav-link <?= $entidad === 'reclamacion' ? 'active' : '' ?>" href="index.php?entidad=reclamacion&accion=crear"><i class="fas fa-book"></i> Reclamaciones</a></li>
                        <li><a class="nav-link <?= $entidad === 'sugerencia' ? 'active' : '' ?>" href="index.php?entidad=sugerencia&accion=crear"><i class="fas fa-lightbulb"></i> Sugerencias</a></li>
                        <li><a class="nav-link" href="index.php?entidad=auth&accion=mostrarLogin"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a></li>
                    <?php endif; ?>
                    <?php if ($estaAutenticado): ?>
                        <li class="user-menu-item">
                            <div class="user-menu">
                                <span class="user-info">
                                    <span class="user-avatar"><?= Helper::e(strtoupper(substr((string) ($usuarioActual['nombre'] ?? 'U'), 0, 1))) ?></span>
                                    <span><?= Helper::e((string) ($usuarioActual['nombre'] ?? '')) ?></span>
                                </span>
                                <a href="index.php?entidad=auth&accion=logout" class="btn-logout" title="Cerrar Sesión"><i class="fas fa-sign-out-alt"></i> Salir</a>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <?php
    $msgExito = Session::getFlash('exito');
    $msgSuccess = Session::getFlash('success');
    $msgExito = $msgExito ?? $msgSuccess;
    ?>
    <?php if ($msgExito): ?>
        <div class="flash-banner flash-success"><?= Helper::e($msgExito) ?></div>
    <?php endif; ?>
    <?php if ($msgError = Session::getFlash('error')): ?>
        <div class="flash-banner flash-error"><?= Helper::e($msgError) ?></div>
    <?php endif; ?>

    <main>
        <?php Router::dispatch(__DIR__, $entidad, $accion); ?>
    </main>

    <footer class="main-footer">
        <div class="footer-content">
            <p>&copy; <?= date('Y') ?> Veterinaria Ralah Pets - Todos los derechos reservados</p>
            <p style="font-size: 0.875rem; opacity: 0.8;">Cuidando a tus mascotas con amor y profesionalismo</p>
        </div>
    </footer>
</body>
</html>
