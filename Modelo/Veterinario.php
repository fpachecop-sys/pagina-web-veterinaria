<?php

declare(strict_types=1);

require_once __DIR__ . '/../Servicios/conexion.php';

use App\Repositories\VeterinarioRepository;
use App\Support\Database;

class Veterinario
{
    public static function listarTodos(): array
    {
        return (new VeterinarioRepository(Database::getConnection()))->listAllActive();
    }

    public static function guardar(string $nombre, string $especialidad, string $correo, string $telefono): bool
    {
        return (new VeterinarioRepository(Database::getConnection()))->create($nombre, $especialidad, $correo, $telefono);
    }

    public static function obtenerPorId(int|string $id): ?array
    {
        return (new VeterinarioRepository(Database::getConnection()))->findById((int) $id);
    }

    public static function actualizar(int|string $id, string $nombre, string $especialidad, string $correo, string $telefono): bool
    {
        return (new VeterinarioRepository(Database::getConnection()))->update((int) $id, $nombre, $especialidad, $correo, $telefono);
    }

    public static function eliminar(int|string $id): bool
    {
        return (new VeterinarioRepository(Database::getConnection()))->deactivate((int) $id);
    }
}
