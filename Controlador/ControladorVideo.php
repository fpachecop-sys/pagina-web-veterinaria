<?php

declare(strict_types=1);

require_once __DIR__ . '/../Modelo/Video.php';
require_once __DIR__ . '/../Modelo/Usuario.php';

use App\Support\Csrf;
use App\Support\Logger;
use App\Support\Session;
use App\Services\VideoMediaStorage;
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
        $datos = array_values(array_filter($videos, static fn (array $video): bool => !empty($video['activo'])));
        $promociones = [
            [
                'titulo' => 'Promoción de baños',
                'descripcion' => 'Baño con agua temperada y servicios de cuidado para tu mascota.',
                'imagen' => 'assets/img/Promocion_baños.jpg',
            ],
            [
                'titulo' => 'Antipulgas por baño o corte',
                'descripcion' => 'Spray antipulgas de cortesía los jueves por baño o corte.',
                'imagen' => 'assets/img/anuncio2.jpg',
            ],
        ];
        $videosPublicos = [];
        foreach ($datos as $video) {
            if (Video::esImagenMedia($video)) {
                $promociones[] = [
                    'titulo' => (string) $video['titulo'],
                    'descripcion' => (string) ($video['descripcion'] ?? ''),
                    'imagen' => (string) $video['url_video'],
                ];
            } else {
                $videosPublicos[] = $video;
            }
        }
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

        $tipo = trim((string) ($_POST['tipo_contenido'] ?? 'video'));
        $fuente = trim((string) ($_POST['fuente'] ?? 'url'));
        $requiereArchivo = $tipo === 'promocion' || $fuente === 'archivo';
        $error = InteraccionValidator::validateVideo($_POST, $requiereArchivo);
        if ($error !== null) {
            Session::setFlash('error', $error);
            header('Location: index.php?entidad=video&accion=agregar');
            exit();
        }

        $recurso = '';
        try {
            [$recurso, $categoria] = $this->obtenerRecurso($_POST, $_FILES);
            $guardado = Video::guardar(
                trim((string) $_POST['titulo']),
                trim((string) ($_POST['descripcion'] ?? '')),
                $recurso,
                $categoria
            );
            if (!$guardado) {
                throw new \RuntimeException('No se pudo guardar el contenido.');
            }
            Session::setFlash('exito', 'Contenido publicado exitosamente.');
            Session::setFlash('success', 'Contenido publicado exitosamente.');
        } catch (\InvalidArgumentException $e) {
            VideoMediaStorage::delete($recurso);
            Session::setFlash('error', $e->getMessage());
            header('Location: index.php?entidad=video&accion=agregar');
            exit();
        } catch (\Throwable $e) {
            VideoMediaStorage::delete($recurso);
            Logger::error('Error guardando contenido multimedia', $e);
            Session::setFlash('error', 'No se pudo guardar el contenido.');
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
        $video = Video::obtenerPorId($id);
        if ($video === null) {
            Session::setFlash('error', 'Contenido no encontrado.');
            header('Location: index.php?entidad=video&accion=listar');
            exit();
        }
        $tipo = trim((string) ($_POST['tipo_contenido'] ?? (Video::esImagenMedia($video) ? 'promocion' : 'video')));
        $fuente = trim((string) ($_POST['fuente'] ?? (Video::esRecursoLocal($video) ? 'archivo' : 'url')));
        $requiereArchivo = $tipo === 'promocion' || $fuente === 'archivo';
        $error = InteraccionValidator::validateVideo($_POST, $requiereArchivo);
        if ($error !== null) {
            Session::setFlash('error', $error);
            header('Location: index.php?entidad=video&accion=editar&id=' . $id);
            exit();
        }

        $nuevoRecurso = '';
        try {
            [$nuevoRecurso, $categoria] = $this->obtenerRecurso($_POST, $_FILES, $video);
            $activo = isset($_POST['activo']) ? 1 : 0;
            $actualizado = Video::actualizar(
                $id,
                trim((string) $_POST['titulo']),
                trim((string) ($_POST['descripcion'] ?? '')),
                $nuevoRecurso,
                $categoria,
                $activo
            );
            if (!$actualizado) {
                throw new \RuntimeException('No se pudo actualizar el contenido.');
            }
            if ($video['url_video'] !== $nuevoRecurso) {
                VideoMediaStorage::delete((string) $video['url_video']);
            }
            Session::setFlash('exito', 'Contenido actualizado correctamente.');
            Session::setFlash('success', 'Contenido actualizado correctamente.');
        } catch (\InvalidArgumentException $e) {
            if ($nuevoRecurso !== (string) $video['url_video']) {
                VideoMediaStorage::delete($nuevoRecurso);
            }
            Session::setFlash('error', $e->getMessage());
            header('Location: index.php?entidad=video&accion=editar&id=' . $id);
            exit();
        } catch (\Throwable $e) {
            if ($nuevoRecurso !== (string) $video['url_video']) {
                VideoMediaStorage::delete($nuevoRecurso);
            }
            Logger::error('Error actualizando contenido multimedia', $e);
            Session::setFlash('error', 'No se pudo actualizar el contenido.');
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
        $video = Video::obtenerPorId($id);
        if (Video::eliminar($id)) {
            VideoMediaStorage::delete((string) ($video['url_video'] ?? ''));
            Session::setFlash('exito', 'Video eliminado correctamente.');
            Session::setFlash('success', 'Video eliminado correctamente.');
        } else {
            Session::setFlash('error', 'No se pudo eliminar el video.');
        }

        header('Location: index.php?entidad=video&accion=listar');
        exit();
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $files
     * @param array<string, mixed>|null $existing
     * @return array{0: string, 1: string}
     */
    private function obtenerRecurso(array $data, array $files, ?array $existing = null): array
    {
        $tipo = trim((string) ($data['tipo_contenido'] ?? 'video'));
        if (!in_array($tipo, ['video', 'promocion'], true)) {
            throw new \InvalidArgumentException('Selecciona si vas a publicar un video o una promoción.');
        }

        $fuente = trim((string) ($data['fuente'] ?? 'url'));
        $tipoArchivo = $tipo === 'promocion' ? 'image' : 'video';
        $archivo = $files['archivo_media'] ?? null;
        $errorCarga = is_array($archivo) ? (int) ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) : UPLOAD_ERR_NO_FILE;

        if ($errorCarga !== UPLOAD_ERR_NO_FILE) {
            $recurso = VideoMediaStorage::store(is_array($archivo) ? $archivo : [], $tipoArchivo);
        } elseif ($tipo === 'promocion') {
            if ($existing !== null && Video::esImagenMedia($existing)) {
                $recurso = (string) $existing['url_video'];
            } else {
                throw new \InvalidArgumentException('Selecciona la imagen que se publicará como promoción.');
            }
        } elseif ($fuente === 'archivo') {
            if ($existing !== null && Video::esVideoLocal($existing)) {
                $recurso = (string) $existing['url_video'];
            } else {
                throw new \InvalidArgumentException('Selecciona un video local MP4, WEBM u OGG.');
            }
        } elseif ($fuente === 'url') {
            $recurso = trim((string) ($data['url_video'] ?? ''));
            $scheme = strtolower((string) parse_url($recurso, PHP_URL_SCHEME));
            if (!filter_var($recurso, FILTER_VALIDATE_URL) || !in_array($scheme, ['http', 'https'], true)) {
                throw new \InvalidArgumentException('Ingresa una URL http o https válida para el video.');
            }
        } else {
            throw new \InvalidArgumentException('Selecciona una URL o un archivo local para el video.');
        }

        $categoria = $tipo === 'promocion'
            ? 'Promociones'
            : trim((string) ($data['categoria'] ?? 'Servicios'));

        return [$recurso, $categoria];
    }
}
