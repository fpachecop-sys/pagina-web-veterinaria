<?php

declare(strict_types=1);

require_once __DIR__ . '/../Modelo/Usuario.php';
require_once __DIR__ . '/../Modelo/Reporte.php';
require_once __DIR__ . '/../Modelo/Cita.php';
require_once __DIR__ . '/../Modelo/Mascota.php';
require_once __DIR__ . '/../Modelo/Dueno.php';
require_once __DIR__ . '/../Modelo/Veterinario.php';

use App\Repositories\DashboardRepository;
use App\Support\Csrf;
use App\Support\Database;
use App\Support\Session;

class ControladorAdmin
{
    public function __construct()
    {
        if (!Usuario::estaAutenticado() || !Usuario::esAdministrador()) {
            header('Location: index.php?entidad=auth&accion=mostrarLogin');
            exit();
        }
    }

    public function dashboard(): void
    {
        $stats = Reporte::obtenerEstadisticas();
        $citasPorEstado = Reporte::obtenerCitasPorEstado();
        $mascotasPorEspecie = Reporte::obtenerMascotasPorEspecie();
        $citasRecientes = (new DashboardRepository(Database::getConnection()))->getCitasRecientesAdmin(10);

        include __DIR__ . '/../Vista/admin/dashboard.php';
    }

    public function usuarios(): void
    {
        $usuarios = Usuario::listarTodos();
        include __DIR__ . '/../Vista/admin/usuarios_listar.php';
    }

    /** Alias para compatibilidad con enlaces antiguos. */
    public function listarUsuarios(): void
    {
        $this->usuarios();
    }

    public function cambiarEstadoUsuario(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=admin&accion=usuarios');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=admin&accion=usuarios');
            exit();
        }

        $id = (int) ($_POST['id'] ?? 0);
        $activo = ((int) ($_POST['activo'] ?? 0) === 1) ? 1 : 0;
        $adminActual = Usuario::obtenerUsuarioActual();

        if ($id === (int) ($adminActual['id'] ?? 0)) {
            Session::setFlash('error', 'Por seguridad, no puedes desactivar tu propia cuenta de administrador.');
            header('Location: index.php?entidad=admin&accion=usuarios');
            exit();
        }

        if (Usuario::cambiarEstado($id, $activo)) {
            Session::setFlash('exito', 'Estado del usuario actualizado correctamente.');
        } else {
            Session::setFlash('error', 'No se pudo actualizar el estado del usuario.');
        }

        header('Location: index.php?entidad=admin&accion=usuarios');
        exit();
    }
}
