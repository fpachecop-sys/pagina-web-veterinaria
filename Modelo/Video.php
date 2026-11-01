<?php

declare(strict_types=1);

require_once __DIR__ . '/../Servicios/conexion.php';

use App\Repositories\InteraccionRepository;
use App\Support\Database;

class Video
{
    public static function esImagenMedia(array $video): bool
    {
        $extension = strtolower(pathinfo((string) parse_url((string) ($video['url_video'] ?? ''), PHP_URL_PATH), PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
    }

    public static function esVideoLocal(array $video): bool
    {
        $url = (string) ($video['url_video'] ?? '');
        $extension = strtolower(pathinfo((string) parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        return str_starts_with($url, 'assets/uploads/media/')
            && in_array($extension, ['mp4', 'webm', 'ogv', 'ogg'], true);
    }

    public static function esVideoDirecto(array $video): bool
    {
        $url = (string) ($video['url_video'] ?? '');
        $extension = strtolower(pathinfo((string) parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        return in_array($extension, ['mp4', 'webm', 'ogv', 'ogg'], true)
            && (self::esVideoLocal($video) || in_array($scheme, ['http', 'https'], true));
    }

    public static function esRecursoLocal(array $video): bool
    {
        return self::esImagenMedia($video) || self::esVideoLocal($video);
    }

    public static function urlParaEmbed(string $url): string
    {
        $parts = parse_url($url);
        if (!is_array($parts)) {
            return $url;
        }

        $host = strtolower((string) ($parts['host'] ?? ''));
        $path = trim((string) ($parts['path'] ?? ''), '/');
        $segments = $path === '' ? [] : explode('/', $path);
        $videoId = '';

        if (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'music.youtube.com'], true)) {
            parse_str((string) ($parts['query'] ?? ''), $query);
            $videoId = (string) ($query['v'] ?? '');
            if ($videoId === '' && count($segments) >= 2 && in_array($segments[0], ['embed', 'shorts', 'live'], true)) {
                $videoId = (string) $segments[1];
            }
        } elseif (in_array($host, ['youtu.be', 'www.youtu.be'], true)) {
            $videoId = (string) ($segments[0] ?? '');
        }

        if ($videoId !== '' && preg_match('/^[A-Za-z0-9_-]{6,20}$/', $videoId) === 1) {
            return 'https://www.youtube-nocookie.com/embed/' . $videoId;
        }

        if (in_array($host, ['vimeo.com', 'www.vimeo.com'], true)) {
            $vimeoId = end($segments);
            if (is_string($vimeoId) && preg_match('/^[0-9]{5,15}$/', $vimeoId) === 1) {
                return 'https://player.vimeo.com/video/' . $vimeoId;
            }
        }

        return $url;
    }

    public static function listarTodos(): array
    {
        return (new InteraccionRepository(Database::getConnection()))->listVideos();
    }

    public static function obtenerPorId(int|string $id): ?array
    {
        return (new InteraccionRepository(Database::getConnection()))->findVideoById((int) $id);
    }

    public static function guardar(string $titulo, string $descripcion, string $urlVideo, string $categoria = 'Servicios'): bool
    {
        return (new InteraccionRepository(Database::getConnection()))->createVideo($titulo, $descripcion, $urlVideo, $categoria);
    }

    public static function actualizar(int|string $id, string $titulo, string $descripcion, string $urlVideo, string $categoria = 'Servicios', int $activo = 1): bool
    {
        return (new InteraccionRepository(Database::getConnection()))->updateVideo((int) $id, $titulo, $descripcion, $urlVideo, $categoria, $activo);
    }

    public static function eliminar(int|string $id): bool
    {
        return (new InteraccionRepository(Database::getConnection()))->deleteVideo((int) $id);
    }
}
