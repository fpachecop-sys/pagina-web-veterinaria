<?php

declare(strict_types=1);

require_once __DIR__ . '/../Servicios/conexion.php';

use App\Repositories\CitaRepository;
use App\Services\CitaService;
use App\Support\Database;

class Cita
{
    public static function listarTodas(): array
    {
        return (new CitaRepository(Database::getConnection()))->listAll();
    }

    public static function guardar(string $fecha, string $hora, int|string $id_mascota, int|string $id_veterinario, string $motivo): bool
    {
        $res = (new CitaService())->agendarCita([
            'fecha'          => $fecha,
            'hora'           => $hora,
            'id_mascota'     => (int) $id_mascota,
            'id_veterinario' => (int) $id_veterinario,
            'motivo'         => $motivo,
        ]);
        return $res['exito'];
    }

    public static function obtenerPorId(int|string $id): ?array
    {
        return (new CitaRepository(Database::getConnection()))->findById((int) $id);
    }

    public static function actualizar(
        int|string $id,
        string $fecha,
        string $hora,
        int|string $id_mascota,
        int|string $id_veterinario,
        string $motivo,
        string $estado
    ): bool {
        $res = (new CitaService())->actualizarCita((int) $id, [
            'fecha'          => $fecha,
            'hora'           => $hora,
            'id_mascota'     => (int) $id_mascota,
            'id_veterinario' => (int) $id_veterinario,
            'motivo'         => $motivo,
            'estado'         => $estado,
        ]);
        return $res['exito'];
    }

    public static function cambiarEstado(int|string $id, string $estado): bool
    {
        $res = (new CitaService())->cambiarEstado((int) $id, $estado);
        return $res['exito'];
    }

    public static function eliminar(int|string $id): bool
    {
        return (new CitaRepository(Database::getConnection()))->cancelOrDelete((int) $id);
    }
}