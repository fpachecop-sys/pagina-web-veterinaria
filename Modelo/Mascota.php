<?php

declare(strict_types=1);

require_once __DIR__ . '/../Servicios/conexion.php';

use App\Repositories\MascotaRepository;
use App\Support\Database;

class Mascota
{
    public static function listarTodas(): array
    {
        return (new MascotaRepository(Database::getConnection()))->listAllActive();
    }

    public static function guardar(string $nombre, string $especie, string $raza, int|string $edad, string $dni_dueno): bool
    {
        return (new MascotaRepository(Database::getConnection()))->create(
            $nombre,
            $especie,
            $raza,
            (int) $edad,
            $dni_dueno
        );
    }

    public static function obtenerPorId(int|string $id): ?array
    {
        return (new MascotaRepository(Database::getConnection()))->findById((int) $id);
    }

    public static function actualizar(int|string $id, string $nombre, string $especie, string $raza, int|string $edad, string $dni_dueno): bool
    {
        return (new MascotaRepository(Database::getConnection()))->update(
            (int) $id,
            $nombre,
            $especie,
            $raza,
            (int) $edad,
            $dni_dueno
        );
    }

    public static function eliminar(int|string $id): bool
    {
        return (new MascotaRepository(Database::getConnection()))->deactivate((int) $id);
    }
}
