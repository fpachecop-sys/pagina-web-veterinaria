<?php

declare(strict_types=1);

require_once __DIR__ . '/../Modelo/Veterinario.php';
require_once __DIR__ . '/../Modelo/Usuario.php';

use App\Repositories\VeterinarioRepository;
use App\Support\Csrf;
use App\Support\Database;
use App\Support\Logger;
use App\Support\Session;
use App\Validators\CatalogoValidator;

class ControladorVeterinario
{
    private function verificarAdmin(): void
    {
        if (!Usuario::esAdministrador()) {
            header('Location: index.php?entidad=home&accion=ver');
            exit();
        }
    }

    public function verPublico(): void
    {
        $veterinarios = Veterinario::listarTodos();
        include __DIR__ . '/../Vista/veterinarios/ver_publico.php';
    }

    public function listar(): void
    {
        $this->verificarAdmin();
        $veterinarios = Veterinario::listarTodos();
        include __DIR__ . '/../Vista/veterinarios/listar.php';
    }

    public function agregar(): void
    {
        $this->verificarAdmin();
        include __DIR__ . '/../Vista/veterinarios/agregar.php';
    }

    public function guardarNuevo(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=veterinario&accion=agregar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=veterinario&accion=agregar');
            exit();
        }

        $error = CatalogoValidator::validateVeterinario($_POST);
        if ($error !== null) {
            Session::setFlash('error', $error);
            header('Location: index.php?entidad=veterinario&accion=agregar');
            exit();
        }

        $correo = strtolower(trim((string) $_POST['correo']));
        $repo = new VeterinarioRepository(Database::getConnection());
        if ($repo->existsEmail($correo)) {
            Session::setFlash('error', 'El correo electrónico ya está registrado en otro veterinario.');
            header('Location: index.php?entidad=veterinario&accion=agregar');
            exit();
        }

        try {
            Veterinario::guardar(
                trim((string) $_POST['nombre']),
                trim((string) $_POST['especialidad']),
                $correo,
                trim((string) $_POST['telefono'])
            );
            Session::setFlash('exito', 'Veterinario registrado exitosamente.');
        } catch (\Throwable $e) {
            Logger::error('Error guardando veterinario', $e);
            Session::setFlash('error', 'No se pudo registrar el veterinario.');
        }

        header('Location: index.php?entidad=veterinario&accion=listar');
        exit();
    }

    public function editar(): void
    {
        $this->verificarAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        $veterinario = Veterinario::obtenerPorId($id);
        if ($veterinario === null) {
            Session::setFlash('error', 'Veterinario no encontrado.');
            header('Location: index.php?entidad=veterinario&accion=listar');
            exit();
        }
        include __DIR__ . '/../Vista/veterinarios/editar.php';
    }

    public function actualizar(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=veterinario&accion=listar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=veterinario&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? 0);
        $error = CatalogoValidator::validateVeterinario($_POST);
        if ($error !== null) {
            Session::setFlash('error', $error);
            header('Location: index.php?entidad=veterinario&accion=editar&id=' . $id);
            exit();
        }

        $correo = strtolower(trim((string) $_POST['correo']));
        $repo = new VeterinarioRepository(Database::getConnection());
        if ($repo->existsEmail($correo, $id)) {
            Session::setFlash('error', 'El correo electrónico ya pertenece a otro veterinario.');
            header('Location: index.php?entidad=veterinario&accion=editar&id=' . $id);
            exit();
        }

        try {
            Veterinario::actualizar(
                $id,
                trim((string) $_POST['nombre']),
                trim((string) $_POST['especialidad']),
                $correo,
                trim((string) $_POST['telefono'])
            );
            Session::setFlash('exito', 'Datos del veterinario actualizados correctamente.');
        } catch (\Throwable $e) {
            Logger::error('Error actualizando veterinario', $e);
            Session::setFlash('error', 'No se pudo actualizar el veterinario.');
        }

        header('Location: index.php?entidad=veterinario&accion=listar');
        exit();
    }

    public function eliminar(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Session::setFlash('error', 'Método no permitido.');
            header('Location: index.php?entidad=veterinario&accion=listar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=veterinario&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? 0);
        try {
            Veterinario::eliminar($id);
            Session::setFlash('exito', 'Veterinario desactivado conservando su historial de citas.');
        } catch (\Throwable $e) {
            Logger::error('Error desactivando veterinario', $e);
            Session::setFlash('error', 'No se pudo desactivar el veterinario.');
        }

        header('Location: index.php?entidad=veterinario&accion=listar');
        exit();
    }
}
