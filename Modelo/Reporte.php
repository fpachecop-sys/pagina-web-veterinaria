<?php
declare(strict_types=1);

require_once __DIR__ . '/../Servicios/conexion.php';
use App\Support\Database;

class Reporte
{
    public static function obtenerEstadisticas(): array
    {
        $conn = Database::getConnection();$stats = [];

        $r =$conn->query("SELECT COUNT(*) AS total FROM usuarios WHERE rol = 'usuario' AND activo = 1");
        $stats['total_usuarios'] = (int) ($r->fetch_assoc()['total'] ?? 0);

        $r =$conn->query("SELECT COUNT(*) AS total FROM mascotas WHERE activo = 1");
        $stats['total_mascotas'] = (int) ($r->fetch_assoc()['total'] ?? 0);

        $r =$conn->query("SELECT COUNT(*) AS total FROM citas");
        $stats['total_citas'] = (int) ($r->fetch_assoc()['total'] ?? 0);

        $r =$conn->query("SELECT COUNT(*) AS total FROM veterinarios WHERE activo = 1");
        $stats['total_veterinarios'] = (int) ($r->fetch_assoc()['total'] ?? 0);

        return $stats;
    }

    public static function obtenerCitasPorEstado(): array
    {
        $conn = Database::getConnection();
        $res =$conn->query("SELECT estado, COUNT(*) as cantidad FROM citas GROUP BY estado");
        $citas = [];
        while ($fila = $res->fetch_assoc()) {$citas[$fila['estado']] = (int)$fila['cantidad'];
        }
        return $citas;
    }

    public static function obtenerMascotasPorEspecie(): array
    {
        $conn = Database::getConnection();
        $res =$conn->query("SELECT especie, COUNT(*) as cantidad FROM mascotas WHERE activo = 1 GROUP BY especie");
        $mascotas = [];
        while ($fila =$res->fetch_assoc()) {
            $mascotas[] =$fila;
        }
        return $mascotas;
    }

    public static function obtenerCitasPorMes(): array
    {
        $conn = Database::getConnection();
        $res =$conn->query("SELECT DATE_FORMAT(fecha, '%Y-%m') as mes, COUNT(*) as cantidad FROM citas GROUP BY mes ORDER BY mes DESC LIMIT 6");
        $citas = [];
        while ($fila =$res->fetch_assoc()) {
            $citas[] =$fila;
        }
        return array_reverse($citas);
    }
}