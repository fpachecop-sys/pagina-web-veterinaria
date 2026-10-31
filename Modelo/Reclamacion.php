<?php
declare(strict_types=1);

require_once __DIR__ . '/../Servicios/conexion.php';
use App\Repositories\InteraccionRepository;
use App\Support\Database;

class Reclamacion
{
    public static function listarTodas(): array
    {
        return (new InteraccionRepository(Database::getConnection()))->listReclamaciones();
    }

    public static function guardar(array $datos): bool
    {
        return (new InteraccionRepository(Database::getConnection()))->createReclamacion($datos);
    }

    public static function cambiarEstado(int|string $id, string$estado): bool
    {
        return (new InteraccionRepository(Database::getConnection()))->updateEstadoReclamacion((int) $id,$estado);
    }

    public static function eliminar(int|string $id): bool
    {
        return (new InteraccionRepository(Database::getConnection()))->deleteReclamacion((int) $id);
    }
}