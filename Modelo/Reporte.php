<?php

declare(strict_types=1);

require_once __DIR__ . '/../Servicios/conexion.php';

use App\Support\Database;

class Reporte
{
    public static function obtenerEstadisticas(): array
    {
        $conn = Database::getConnection();
        $stats = [];

        $r = $conn->query("SELECT COUNT(*) AS total FROM usuarios WHERE rol = 'usuario' AND activo = 1");
        $stats['total_usuarios'] = (int) ($r->fetch_assoc()['total'] ?? 0);

        $r = $conn->query("SELECT COUNT(*) AS total FROM duenos WHERE activo = 1");
        $stats['total_duenos'] = (int) ($r->fetch_assoc()['total'] ?? 0);

        $r = $conn->query("SELECT COUNT(*) AS total FROM mascotas WHERE activo = 1");
        $stats['total_mascotas'] = (int) ($r->fetch_assoc()['total'] ?? 0);

        $r = $conn->query("SELECT COUNT(*) AS total FROM veterinarios WHERE activo = 1");
        $stats['total_veterinarios'] = (int) ($r->fetch_assoc()['total'] ?? 0);

        $r = $conn->query("
            SELECT 
                COUNT(*) AS total_citas,
                SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) AS pendientes,
                SUM(CASE WHEN estado = 'Atendida' THEN 1 ELSE 0 END) AS atendidas,
                SUM(CASE WHEN estado = 'Cancelada' THEN 1 ELSE 0 END) AS canceladas
            FROM citas
        ");
        $rowCitas = $r->fetch_assoc();
        $stats['total_citas']      = (int) ($rowCitas['total_citas'] ?? 0);
        $stats['citas_pendientes'] = (int) ($rowCitas['pendientes'] ?? 0);
        $stats['citas_atendidas']  = (int) ($rowCitas['atendidas'] ?? 0);
        $stats['citas_canceladas'] = (int) ($rowCitas['canceladas'] ?? 0);

        return $stats;
    }

    public static function obtenerCitasPorEstado(): array
    {
        $conn = Database::getConnection();
        $res = $conn->query("SELECT estado, COUNT(*) AS cantidad FROM citas GROUP BY estado");
        $citas = [];
        while ($fila = $res->fetch_assoc()) {
            $citas[$fila['estado']] = (int) $fila['cantidad'];
        }
        return $citas;
    }

    public static function obtenerMascotasPorEspecie(): array
    {
        $conn = Database::getConnection();
        $res = $conn->query("SELECT especie, COUNT(*) AS cantidad FROM mascotas WHERE activo = 1 GROUP BY especie");
        $mascotas = [];
        while ($fila = $res->fetch_assoc()) {
            $mascotas[] = $fila;
        }
        return $mascotas;
    }

    public static function obtenerVeterinariosPopulares(): array
    {
        $conn = Database::getConnection();
        $sql = "SELECT v.id, v.nombre, v.especialidad, COUNT(c.id) AS total_citas
                FROM veterinarios v
                LEFT JOIN citas c ON v.id = c.id_veterinario
                WHERE v.activo = 1
                GROUP BY v.id, v.nombre, v.especialidad
                ORDER BY total_citas DESC, v.nombre ASC";
        $res = $conn->query($sql);
        $veterinarios = [];
        while ($fila = $res->fetch_assoc()) {
            $veterinarios[] = $fila;
        }
        return $veterinarios;
    }

    public static function obtenerCitasPorMes(): array
    {
        $conn = Database::getConnection();
        $res = $conn->query("SELECT DATE_FORMAT(fecha, '%Y-%m') AS mes, COUNT(*) AS cantidad FROM citas GROUP BY mes ORDER BY mes DESC LIMIT 6");
        $citas = [];
        while ($fila = $res->fetch_assoc()) {
            $citas[] = $fila;
        }
        return array_reverse($citas);
    }
}