<?php

declare(strict_types=1);

require_once __DIR__ . '/../Servicios/conexion.php';

use App\Repositories\DuenoRepository;
use App\Support\Database;

class Dueno
{
    public static function listarTodos(): array
    {
        return (new DuenoRepository(Database::getConnection()))->listAllActive();
    }

    public static function guardar(string $dni, string $nombre, string $telefono, string $direccion): bool
    {
        return (new DuenoRepository(Database::getConnection()))->save($dni, $nombre, $telefono, $direccion);
    }

    public static function obtenerPorDni(string $dni): ?array
    {
        return (new DuenoRepository(Database::getConnection()))->findByDni($dni);
    }

    public static function actualizar(string $dni, string $nombre, string $telefono, string $direccion): bool
    {
        return (new DuenoRepository(Database::getConnection()))->update($dni, $nombre, $telefono, $direccion);
    }

    public static function eliminar(string $dni): bool
    {
        return (new DuenoRepository(Database::getConnection()))->deactivate($dni);
    }
}
