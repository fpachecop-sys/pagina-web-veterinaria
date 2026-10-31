<?php

declare(strict_types=1);

namespace App\Support;

use ReflectionMethod;

final class Router
{
    /**
     * @var array<string, array{file: string, class: string}>
     */
    private const CONTROLLERS = [
        'home'        => ['file' => 'ControladorHome.php',        'class' => 'ControladorHome'],
        'auth'        => ['file' => 'ControladorAuth.php',        'class' => 'ControladorAuth'],
        'usuario'     => ['file' => 'ControladorUsuario.php',     'class' => 'ControladorUsuario'],
        'admin'       => ['file' => 'ControladorAdmin.php',       'class' => 'ControladorAdmin'],
        'dueno'       => ['file' => 'ControladorDueno.php',       'class' => 'ControladorDueno'],
        'mascota'     => ['file' => 'ControladorMascota.php',     'class' => 'ControladorMascota'],
        'cita'        => ['file' => 'ControladorCita.php',        'class' => 'ControladorCita'],
        'veterinario' => ['file' => 'ControladorVeterinario.php', 'class' => 'ControladorVeterinario'],
        'reporte'     => ['file' => 'ControladorReporte.php',     'class' => 'ControladorReporte'],
        'reclamacion' => ['file' => 'ControladorReclamacion.php', 'class' => 'ControladorReclamacion'],
        'sugerencia'  => ['file' => 'ControladorSugerencia.php',  'class' => 'ControladorSugerencia'],
        'video'       => ['file' => 'ControladorVideo.php',       'class' => 'ControladorVideo'],
        'ubicacion'   => ['file' => 'ControladorUbicacion.php',   'class' => 'ControladorUbicacion'],
    ];

    /**
     * @var array<string, list<string>>
     */
    private const PUBLIC_ROUTES = [
        'home'        => ['ver'],
        'auth'        => ['mostrarLogin', 'mostrarRegistro', 'procesarLogin', 'procesarRegistro', 'logout'],
        'reclamacion' => ['crear', 'mostrarFormulario', 'guardar'],
        'sugerencia'  => ['crear', 'mostrarFormulario', 'guardar'],
        'video'       => ['verPublico'],
        'ubicacion'   => ['ver'],
        'veterinario' => ['verPublico'],
    ];

    /**
     * @var array<string, list<string>>
     */
    private const ADMIN_ROUTES = [
        'admin'       => ['dashboard', 'usuarios', 'listarUsuarios', 'cambiarEstadoUsuario'],
        'dueno'       => ['listar', 'agregar', 'mostrarFormularioAgregar', 'guardarNuevo', 'editar', 'mostrarFormularioEditar', 'actualizar', 'eliminar'],
        'mascota'     => ['listar', 'agregar', 'mostrarFormularioAgregar', 'guardarNuevo', 'editar', 'mostrarFormularioEditar', 'actualizar', 'eliminar'],
        'cita'        => ['listar', 'agregar', 'mostrarFormularioAgregar', 'guardarNuevo', 'editar', 'mostrarFormularioEditar', 'actualizar', 'cambiarEstado', 'eliminar'],
        'veterinario' => ['listar', 'agregar', 'mostrarFormularioAgregar', 'guardarNuevo', 'editar', 'mostrarFormularioEditar', 'actualizar', 'eliminar'],
        'reporte'     => ['ver'],
        'reclamacion' => ['listar', 'cambiarEstado', 'eliminar'],
        'sugerencia'  => ['listar', 'eliminar'],
        'video'       => ['listar', 'agregar', 'mostrarFormularioAgregar', 'guardar', 'guardarNuevo', 'editar', 'mostrarFormularioEditar', 'actualizar', 'eliminar'],
    ];

    public static function sendSecurityHeaders(): void
    {
        if (!headers_sent()) {
            header('X-Frame-Options: SAMEORIGIN');
            header('X-Content-Type-Options: nosniff');
            header('Referrer-Policy: strict-origin-when-cross-origin');
        }
    }

    public static function isPublicRoute(string $entidad, string $accion): bool
    {
        return isset(self::PUBLIC_ROUTES[$entidad])
            && in_array($accion, self::PUBLIC_ROUTES[$entidad], true);
    }

    public static function isAdminRoute(string $entidad, string $accion): bool
    {
        return isset(self::ADMIN_ROUTES[$entidad])
            && in_array($accion, self::ADMIN_ROUTES[$entidad], true);
    }

    public static function authorize(string $entidad, string $accion, bool $isAuthenticated, bool $isAdmin): void
    {
        if (!isset(self::CONTROLLERS[$entidad])) {
            header('Location: index.php?entidad=home&accion=ver');
            exit();
        }

        if (!self::isPublicRoute($entidad, $accion) && !$isAuthenticated) {
            header('Location: index.php?entidad=auth&accion=mostrarLogin');
            exit();
        }

        if (self::isAdminRoute($entidad, $accion) && !$isAdmin) {
            header('Location: index.php?entidad=usuario&accion=dashboard');
            exit();
        }
    }

    public static function dispatch(string $baseDir, string $entidad, string $accion): void
    {
        if (!isset(self::CONTROLLERS[$entidad])) {
            echo '<p>Entidad no válida.</p>';
            return;
        }

        $meta = self::CONTROLLERS[$entidad];
        $filePath = $baseDir . '/Controlador/' . $meta['file'];

        if (!file_exists($filePath)) {
            echo '<p>Controlador no disponible.</p>';
            return;
        }

        require_once $filePath;
        $className = $meta['class'];

        if (!class_exists($className)) {
            echo '<p>Clase de controlador no encontrada.</p>';
            return;
        }

        $controller = new $className();

        if (method_exists($controller, $accion)) {
            $reflection = new ReflectionMethod($controller, $accion);
            if ($reflection->isPublic() && !$reflection->isStatic() && !$reflection->isConstructor()) {
                $controller->$accion();
                return;
            }
        }

        http_response_code(404);
        echo '<p>La acción solicitada no existe o no está permitida.</p>';
    }
}
