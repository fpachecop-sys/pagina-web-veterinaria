<?php
declare(strict_types=1);

require_once __DIR__ . '/../Modelo/Video.php';
require_once __DIR__ . '/../Modelo/Usuario.php';

use App\Support\Csrf;
use App\Support\Logger;
use App\Support\Session;
use App\Validators\InteraccionValidator;

class ControladorVideo
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
        $videos = Video::listarTodos();
        include __DIR__ . '/../Vista/videos/ver_publico.php';
    }

    public function listar(): void
    {
        $this->verificarAdmin();$videos = Video::listarTodos();
        include __DIR__ . '/../Vista/videos/listar.php';
    }

    public function agregar(): void
    {
        $this->verificarAdmin();
        include __DIR__ . '/../Vista/videos/agregar.php';
    }

    public function guardar(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=video&accion=agregar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=video&accion=agregar');
            exit();
        }

        $error = InteraccionValidator::validateVideo($_POST);
        if ($error !== null) {
            Session::setFlash('error', $error);
            header('Location: index.php?entidad=video&accion=agregar');
            exit();
        }

        try {
            Video::guardar(
                trim((string) $_POST['titulo']),
                trim((string) ($_POST['descripcion'] ?? '')),
                trim((string) $_POST['url'])
            );
            Session::setFlash('exito', 'Video publicado exitosamente.');
        } catch (\Throwable $e) {
            Logger::error('Error guardando video', $e);
            Session::setFlash('error', 'No se pudo guardar el video.');
        }

        header('Location: index.php?entidad=video&accion=listar');
        exit();
    }

    public function editar(): void
    {
        $this->verificarAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        $video = Video::obtenerPorId($id);
        if ($video === null) {
            Session::setFlash('error', 'Video no encontrado.');
            header('Location: index.php?entidad=video&accion=listar');
            exit();
        }
        include __DIR__ . '/../Vista/videos/editar.php';
    }

    public function actualizar(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=video&accion=listar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=video&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? 0);
        $error = InteraccionValidator::validateVideo($_POST);
        if ($error !== null) {
            Session::setFlash('error', $error);
            header('Location: index.php?entidad=video&accion=editar&id=' . $id);
            exit();
        }

        try {
            Video::actualizar(
                $id,
                trim((string) $_POST['titulo']),
                trim((string) ($_POST['descripcion'] ?? '')),
                trim((string) $_POST['url'])
            );
            Session::setFlash('exito', 'Video actualizado correctamente.');
        } catch (\Throwable $e) {
            Logger::error('Error actualizando video', $e);
            Session::setFlash('error', 'No se pudo actualizar el video.');
        }

        header('Location: index.php?entidad=video&accion=listar');
        exit();
    }

    public function eliminar(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=video&accion=listar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=video&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? 0);
        if (Video::eliminar($id)) {
            Session::setFlash('exito', 'Video eliminado correctamente.');
        } else {
            Session::setFlash('error', 'No se pudo eliminar el video.');
        }

        header('Location: index.php?entidad=video&accion=listar');
        exit();
    }
}