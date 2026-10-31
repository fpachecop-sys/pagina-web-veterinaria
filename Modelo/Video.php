<?php

declare(strict_types=1);

require_once __DIR__ . '/../Servicios/conexion.php';

use App\Repositories\InteraccionRepository;
use App\Support\Database;

class Video
{
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