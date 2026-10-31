<?php
declare(strict_types=1);

require_once __DIR__ . '/../Modelo/Reclamacion.php';
require_once __DIR__ . '/../Modelo/Usuario.php';

use App\Support\Csrf;
use App\Support\Logger;
use App\Support\Session;
use App\Validators\InteraccionValidator;

class ControladorReclamacion
{
    public function crear(): void
    {
        include __DIR__ . '/../Vista/reclamaciones/formulario.php';
    }

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=reclamacion&accion=crear');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=reclamacion&accion=crear');
            exit();
        }

        $error = InteraccionValidator::validateReclamacion($_POST);
        if ($error !== null) {
            Session::setFlash('error', $error);
            header('Location: index.php?entidad=reclamacion&accion=crear');
            exit();
        }

        try {
            Reclamacion::guardar([
                'nombre_cliente'   => trim((string) $_POST['nombre_cliente']),
                'dni_cliente'      => trim((string) $_POST['dni_cliente']),
                'email_cliente'    => trim((string) $_POST['email_cliente']),
                'telefono_cliente' => trim((string) ($_POST['telefono_cliente'] ?? '')),
                'tipo'             => trim((string) $_POST['tipo']),
                'detalle'          => trim((string) $_POST['detalle']),
                'pedido_cliente'   => trim((string) ($_POST['pedido_cliente'] ?? '')),
            ]);
            Session::setFlash('exito', 'Su reclamación o queja ha sido registrada con éxito en el Libro de Reclamaciones.');
        } catch (\Throwable $e) {
            Logger::error('Error registrando reclamacion', $e);
            Session::setFlash('error', 'Ocurrió un error al registrar su solicitud.');
        }

        header('Location: index.php?entidad=reclamacion&accion=crear');
        exit();
    }

    public function listar(): void
    {
        if (!Usuario::esAdministrador()) {
            header('Location: index.php?entidad=home&accion=ver');
            exit();
        }
        $reclamaciones = Reclamacion::listarTodas();
        include __DIR__ . '/../Vista/reclamaciones/listar.php';
    }

    public function cambiarEstado(): void
    {
        if (!Usuario::esAdministrador() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=reclamacion&accion=listar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=reclamacion&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? 0);
        $estado = trim((string) ($_POST['estado'] ?? 'Pendiente'));

        if (Reclamacion::cambiarEstado($id,$estado)) {
            Session::setFlash('exito', 'Estado actualizado a ' . $estado);
        } else {
            Session::setFlash('error', 'No se pudo actualizar el estado.');
        }

        header('Location: index.php?entidad=reclamacion&accion=listar');
        exit();
    }

    public function eliminar(): void
    {
        if (!Usuario::esAdministrador() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=reclamacion&accion=listar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=reclamacion&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? 0);
        if (Reclamacion::eliminar($id)) {
            Session::setFlash('exito', 'Registro eliminado del Libro de Reclamaciones.');
        } else {
            Session::setFlash('error', 'No se pudo eliminar el registro.');
        }

        header('Location: index.php?entidad=reclamacion&accion=listar');
        exit();
    }
}