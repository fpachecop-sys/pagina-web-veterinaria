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
        $datos = $videos;
        include __DIR__ . '/../Vista/videos/ver_publico.php';
    }

    public function listar(): void
    {
        $this->verificarAdmin();
        $videos = Video::listarTodos();
        $datos = $videos;
        include __DIR__ . '/../Vista/videos/listar.php';
    }

    public function agregar(): void
    {
        $this->verificarAdmin();
        include __DIR__ . '/../Vista/videos/agregar.php';
    }

    public function mostrarFormularioAgregar(): void
    {
        $this->agregar();
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
            $urlVideo = trim((string) ($_POST['url_video'] ?? $_POST['url'] ?? ''));
            $categoria = trim((string) ($_POST['categoria'] ?? 'Servicios'));
            Video::guardar(
                trim((string) $_POST['titulo']),
                trim((string) ($_POST['descripcion'] ?? '')),
                $urlVideo,
                $categoria
            );
            Session::setFlash('exito', 'Video publicado exitosamente.');
            Session::setFlash('success', 'Video publicado exitosamente.');
        } catch (\Throwable $e) {
            Logger::error('Error guardando video', $e);
            Session::setFlash('error', 'No se pudo guardar el video.');
        }

        header('Location: index.php?entidad=video&accion=listar');
        exit();
    }

    public function guardarNuevo(): void
    {
        $this->guardar();
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
        $v = $video;
        include __DIR__ . '/../Vista/videos/editar.php';
    }

    public function mostrarFormularioEditar(): void
    {
        $this->editar();
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
            $urlVideo = trim((string) ($_POST['url_video'] ?? $_POST['url'] ?? ''));
            $categoria = trim((string) ($_POST['categoria'] ?? 'Servicios'));
            $activo = isset($_POST['activo']) ? (int) $_POST['activo'] : 1;
            Video::actualizar(
                $id,
                trim((string) $_POST['titulo']),
                trim((string) ($_POST['descripcion'] ?? '')),
                $urlVideo,
                $categoria,
                $activo
            );
            Session::setFlash('exito', 'Video actualizado correctamente.');
            Session::setFlash('success', 'Video actualizado correctamente.');
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
            Session::setFlash('error', 'Método no permitido.');
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
        if ($id <= 0) {
            Session::setFlash('error', 'El identificador del video no es válido.');
            header('Location: index.php?entidad=video&accion=listar');
            exit();
        }
        if (Video::eliminar($id)) {
            Session::setFlash('exito', 'Video eliminado correctamente.');
            Session::setFlash('success', 'Video eliminado correctamente.');
        } else {
            Session::setFlash('error', 'No se pudo eliminar el video.');
        }

        header('Location: index.php?entidad=video&accion=listar');
        exit();
    }
}
