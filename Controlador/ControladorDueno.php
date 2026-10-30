<?php

declare(strict_types=1);

require_once __DIR__ . '/../Modelo/Dueno.php';
require_once __DIR__ . '/../Modelo/Usuario.php';

use App\Support\Csrf;
use App\Support\Logger;
use App\Support\Session;
use App\Validators\CatalogoValidator;

class ControladorDueno
{
    private function verificarAdmin(): void
    {
        if (!Usuario::esAdministrador()) {
            header('Location: index.php?entidad=home&accion=ver');
            exit();
        }
    }

    public function listar(): void
    {
        $this->verificarAdmin();
        $duenos = Dueno::listarTodos();
        include __DIR__ . '/../Vista/duenos/listar.php';
    }

    public function agregar(): void
    {
        $this->verificarAdmin();
        include __DIR__ . '/../Vista/duenos/agregar.php';
    }

    public function guardarNuevo(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=dueno&accion=agregar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=dueno&accion=agregar');
            exit();
        }

        $error = CatalogoValidator::validateDueno($_POST);
        if ($error !== null) {
            Session::setFlash('error', $error);
            header('Location: index.php?entidad=dueno&accion=agregar');
            exit();
        }

        $dni = trim((string) $_POST['dni']);
        $nombre = trim((string) $_POST['nombre']);
        $telefono = trim((string) $_POST['telefono']);
        $direccion = trim((string) $_POST['direccion']);

        try {
            $ok = Dueno::guardar($dni, $nombre, $telefono, $direccion);
            if ($ok) {
                Session::setFlash('exito', 'Dueño registrado exitosamente.');
            } else {
                Session::setFlash('error', 'El DNI ingresado ya se encuentra registrado activo.');
                header('Location: index.php?entidad=dueno&accion=agregar');
                exit();
            }
        } catch (\Throwable $e) {
            Logger::error('Error guardando dueno', $e);
            Session::setFlash('error', 'Error al guardar el dueño.');
        }

        header('Location: index.php?entidad=dueno&accion=listar');
        exit();
    }

    public function editar(): void
    {
        $this->verificarAdmin();
        $dni = trim((string) ($_GET['dni'] ?? ''));
        $dueno = Dueno::obtenerPorDni($dni);
        if ($dueno === null) {
            Session::setFlash('error', 'Dueño no encontrado.');
            header('Location: index.php?entidad=dueno&accion=listar');
            exit();
        }
        include __DIR__ . '/../Vista/duenos/editar.php';
    }

    public function actualizar(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=dueno&accion=listar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=dueno&accion=listar');
            exit();
        }

        $error = CatalogoValidator::validateDueno($_POST);
        $dni = trim((string) ($_POST['dni'] ?? ''));
        if ($error !== null) {
            Session::setFlash('error', $error);
            header('Location: index.php?entidad=dueno&accion=editar&dni=' . urlencode($dni));
            exit();
        }

        $nombre = trim((string) $_POST['nombre']);
        $telefono = trim((string) $_POST['telefono']);
        $direccion = trim((string) $_POST['direccion']);

        try {
            Dueno::actualizar($dni, $nombre, $telefono, $direccion);
            Session::setFlash('exito', 'Datos del dueño actualizados correctamente.');
        } catch (\Throwable $e) {
            Logger::error('Error actualizando dueno', $e);
            Session::setFlash('error', 'No se pudo actualizar el dueño.');
        }

        header('Location: index.php?entidad=dueno&accion=listar');
        exit();
    }

    public function eliminar(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Session::setFlash('error', 'Método no permitido para esta operación.');
            header('Location: index.php?entidad=dueno&accion=listar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=dueno&accion=listar');
            exit();
        }

        $dni = trim((string) ($_POST['dni'] ?? ''));
        try {
            Dueno::eliminar($dni);
            Session::setFlash('exito', 'Dueño y sus mascotas asociados han sido desactivados preservando el historial.');
        } catch (\Throwable $e) {
            Logger::error('Error desactivando dueno', $e);
            Session::setFlash('error', 'No se pudo desactivar el registro.');
        }

        header('Location: index.php?entidad=dueno&accion=listar');
        exit();
    }
}
