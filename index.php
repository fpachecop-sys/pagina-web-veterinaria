<?php

declare(strict_types=1);

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

if (Router::isStandaloneView($entidad, $accion)) {
    Router::dispatch(__DIR__, $entidad, $accion);
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria Ralah Pets - Sistema de Gestión</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.5rem 1rem;
            background: rgba(255,255,255,0.1);
            border-radius: 50px;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
            font-weight: 500;
        }
        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: white;
            color: #667eea;
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
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <a href="index.php?entidad=home&accion=ver" class="logo">
                <i class="fas fa-paw"></i>
                <div>
                    <h1>Ralah Pets</h1>
                    <span>Clínica Veterinaria</span>
                </div>
            </a>

            <nav>
                <ul>
                    <?php if ($estaAutenticado): ?>
                        <?php if ($esAdmin): ?>
                            <li><a href="index.php?entidad=admin&accion=dashboard" class="<?= $entidad === 'admin' && $accion === 'dashboard' ? 'active' : '' ?>"><i class="fas fa-th-large"></i> Dashboard</a></li>
                            <li><a href="index.php?entidad=dueno&accion=listar" class="<?= $entidad === 'dueno' ? 'active' : '' ?>"><i class="fas fa-users"></i> Dueños</a></li>
                            <li><a href="index.php?entidad=mascota&accion=listar" class="<?= $entidad === 'mascota' ? 'active' : '' ?>"><i class="fas fa-dog"></i> Mascotas</a></li>
                            <li><a href="index.php?entidad=cita&accion=listar" class="<?= $entidad === 'cita' ? 'active' : '' ?>"><i class="fas fa-calendar-check"></i> Citas</a></li>
                            <li><a href="index.php?entidad=veterinario&accion=listar" class="<?= $entidad === 'veterinario' ? 'active' : '' ?>"><i class="fas fa-user-md"></i> Veterinarios</a></li>
                            <li><a href="index.php?entidad=reporte&accion=ver" class="<?= $entidad === 'reporte' ? 'active' : '' ?>"><i class="fas fa-chart-bar"></i> Reportes</a></li>
                        <?php else: ?>
                            <li><a href="index.php?entidad=usuario&accion=dashboard" class="<?= $entidad === 'usuario' && $accion === 'dashboard' ? 'active' : '' ?>"><i class="fas fa-home"></i> Inicio</a></li>
                            <li><a href="index.php?entidad=usuario&accion=misMascotas" class="<?= $accion === 'misMascotas' ? 'active' : '' ?>"><i class="fas fa-paw"></i> Mis Mascotas</a></li>
                            <li><a href="index.php?entidad=usuario&accion=misCitas" class="<?= $accion === 'misCitas' ? 'active' : '' ?>"><i class="fas fa-calendar"></i> Mis Citas</a></li>
                            <li><a href="index.php?entidad=video&accion=verPublico" class="<?= $entidad === 'video' ? 'active' : '' ?>"><i class="fas fa-video"></i> Videos</a></li>
                            <li><a href="index.php?entidad=ubicacion&accion=ver" class="<?= $entidad === 'ubicacion' ? 'active' : '' ?>"><i class="fas fa-map-marker-alt"></i> Ubicación</a></li>
                        <?php endif; ?>

                        <li>
                            <div class="user-menu">
                                <a href="index.php?entidad=usuario&accion=perfil" class="user-info" style="text-decoration: none;">
                                    <div class="user-avatar">
                                        <?= Helper::e(strtoupper(substr((string) ($usuarioActual['nombre'] ?? 'U'), 0, 1))) ?>
                                    </div>
                                    <span><?= Helper::e((string) ($usuarioActual['nombre'] ?? '')) ?></span>
                                </a>
                                <a href="index.php?entidad=auth&accion=logout" class="btn-logout" title="Cerrar Sesión">
                                    <i class="fas fa-sign-out-alt"></i> Salir
                                </a>
                            </div>
                        </li>
                    <?php else: ?>
                        <li><a href="index.php?entidad=home&accion=ver"><i class="fas fa-home"></i> Inicio</a></li>
                        <li><a href="index.php?entidad=video&accion=verPublico"><i class="fas fa-video"></i> Videos</a></li>
                        <li><a href="index.php?entidad=ubicacion&accion=ver"><i class="fas fa-map-marker-alt"></i> Ubicación</a></li>
                        <li><a href="index.php?entidad=auth&accion=mostrarLogin"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <?php if ($msgExito = Session::getFlash('exito') ?? Session::getFlash('success')): ?>
        <div class="flash-banner flash-success"><?= Helper::e($msgExito) ?></div>
    <?php endif; ?>
    <?php if ($msgError = Session::getFlash('error')): ?>
        <div class="flash-banner flash-error"><?= Helper::e($msgError) ?></div>
    <?php endif; ?>

    <main>
        <?php Router::dispatch(__DIR__, $entidad, $accion); ?>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-links">
                <a href="index.php?entidad=home&accion=ver">Inicio</a>
                <a href="index.php?entidad=ubicacion&accion=ver">Ubicación</a>
                <a href="index.php?entidad=video&accion=verPublico">Videos</a>
                <a href="index.php?entidad=reclamacion&accion=crear">Libro de Reclamaciones</a>
                <a href="index.php?entidad=sugerencia&accion=crear">Sugerencias</a>
            </div>
            <p>&copy; <?= date('Y') ?> Veterinaria Ralah Pets - Todos los derechos reservados</p>
            <p style="font-size: 0.875rem; opacity: 0.8;">Cuidando a tus mascotas con amor y profesionalismo</p>
        </div>
    </footer>
</body>
</html>
