<?php

declare(strict_types=1);

require_once __DIR__ . '/../Servicios/conexion.php';

use App\Repositories\InteraccionRepository;
use App\Support\Database;

class Sugerencia
{
    public static function listarTodas(): array
    {
        return (new InteraccionRepository(Database::getConnection()))->listSugerencias();
    }

    public static function guardar(string $nombre, string $email, string $mensaje, string $telefono = '', string $categoria = 'General'): bool
    {
        return (new InteraccionRepository(Database::getConnection()))->createSugerencia([
            'nombre'     => $nombre,
            'email'      => $email,
            'telefono'   => $telefono,
            'categoria'  => $categoria,
            'sugerencia' => $mensaje,
        ]);
    }

    public static function eliminar(int|string $id): bool
    {
        return (new InteraccionRepository(Database::getConnection()))->deleteSugerencia((int) $id);
    }
}