<?php

declare(strict_types=1);

require_once __DIR__ . '/../Modelo/Usuario.php';
require_once __DIR__ . '/../Modelo/Mascota.php';
require_once __DIR__ . '/../Modelo/Cita.php';
require_once __DIR__ . '/../Modelo/Dueno.php';

use App\Repositories\DashboardRepository;
use App\Repositories\MascotaRepository;
use App\Services\AuthService;
use App\Support\Csrf;
use App\Support\Database;
use App\Support\Session;

class ControladorUsuario
{
    public function __construct()
    {
        if (!Usuario::estaAutenticado()) {
            header('Location: index.php?entidad=auth&accion=mostrarLogin');
            exit();
        }
    }

    public function dashboard(): void
    {
        $usuario = Usuario::obtenerUsuarioActual();
        $dni = (string) ($usuario['dni'] ?? '');
        $db = Database::getConnection();

        $misMascotas = (new MascotaRepository($db))->listByDuenoDni($dni);
        $dashRepo = new DashboardRepository($db);
        $misCitas = $dashRepo->getCitasPorDniDueno($dni);
        $estadisticas = $dashRepo->getEstadisticasUsuario($dni);
        $stats = $estadisticas;

        include __DIR__ . '/../Vista/usuario/dashboard.php';
    }

    public function perfil(): void
    {
        $usuarioActual = Usuario::obtenerUsuarioActual();
        $usuario = Usuario::obtenerPorId((int) $usuarioActual['id']);
        include __DIR__ . '/../Vista/usuario/perfil.php';
    }

    /** Alias para enlaces antiguos que apuntan a usuario&accion=verPerfil. */
    public function verPerfil(): void
    {
        $this->perfil();
    }

    public function editarPerfil(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=usuario&accion=perfil');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=usuario&accion=perfil');
            exit();
        }

        $usuarioActual = Usuario::obtenerUsuarioActual();
        $idSesion = (int) $usuarioActual['id'];

        $datos = [
            'nombre'    => $_POST['nombre'] ?? '',
            'apellido'  => $_POST['apellido'] ?? '',
            'telefono'  => $_POST['telefono'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'dni'       => $_POST['dni'] ?? '',
        ];

        $resultado = (new AuthService())->actualizarPerfil($idSesion, $datos);

        if ($resultado['exito']) {
            Session::setFlash('exito', $resultado['mensaje']);
        } else {
            Session::setFlash('error', $resultado['mensaje']);
        }

        header('Location: index.php?entidad=usuario&accion=perfil');
        exit();
    }

    public function cambiarPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=usuario&accion=perfil');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=usuario&accion=perfil');
            exit();
        }

        $usuarioActual = Usuario::obtenerUsuarioActual();
        $idSesion = (int) $usuarioActual['id'];

        $passwordActual = (string) ($_POST['password_actual'] ?? '');
        $passwordNueva = (string) ($_POST['password_nueva'] ?? '');
        $confirmarPassword = (string) ($_POST['confirmar_password'] ?? $_POST['password_confirmar'] ?? '');

        if ($passwordNueva !== $confirmarPassword) {
            Session::setFlash('error', 'Las contraseñas nuevas no coinciden.');
            header('Location: index.php?entidad=usuario&accion=perfil');
            exit();
        }

        $resultado = Usuario::cambiarPassword($idSesion, $passwordActual, $passwordNueva);

        if ($resultado['exito']) {
            Session::setFlash('exito', $resultado['mensaje']);
        } else {
            Session::setFlash('error', $resultado['mensaje']);
        }

        header('Location: index.php?entidad=usuario&accion=perfil');
        exit();
    }

    public function misMascotas(): void
    {
        $usuario = Usuario::obtenerUsuarioActual();
        $dni = (string) ($usuario['dni'] ?? '');
        $mascotas = (new MascotaRepository(Database::getConnection()))->listByDuenoDni($dni);
        include __DIR__ . '/../Vista/usuario/mis_mascotas.php';
    }

    public function misCitas(): void
    {
        $usuario = Usuario::obtenerUsuarioActual();
        $dni = (string) ($usuario['dni'] ?? '');
        $citas = (new DashboardRepository(Database::getConnection()))->getCitasPorDniDueno($dni);
        include __DIR__ . '/../Vista/usuario/mis_citas.php';
    }
}
