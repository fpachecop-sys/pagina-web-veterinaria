<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

final class DashboardRepository
{
    public function __construct(private readonly mysqli $db)
    {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getCitasPorDniDueno(string $dni): array
    {
        if ($dni === '') { return []; }$sql = "SELECT c.id, c.fecha, c.hora, c.motivo, c.estado, 
                       m.nombre AS nombre_mascota, m.especie, 
                       v.nombre AS nombre_veterinario, v.especialidad 
                FROM citas c 
                INNER JOIN mascotas m ON c.id_mascota = m.id 
                INNER JOIN veterinarios v ON c.id_veterinario = v.id 
                WHERE m.dni_dueno = ? 
                ORDER BY c.fecha DESC, c.hora DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s',$dni);
        $stmt->execute();$res = $stmt->get_result();$citas = [];
        while ($fila =$res->fetch_assoc()) {
            $citas[] =$fila;
        }
        $stmt->close();
        return $citas;
    }

    /**
     * @return array<string, mixed>
     */
    public function getEstadisticasUsuario(string $dni): array
    {
        if ($dni === '') {
            return ['total_mascotas' => 0, 'citas_pendientes' => 0, 'citas_atendidas' => 0, 'proxima_cita' => null];
        }

        $hoyLima = date('Y-m-d');$stats = [];

        $stmt =$this->db->prepare("SELECT COUNT(*) AS total FROM mascotas WHERE dni_dueno = ? AND activo = 1");
        $stmt->bind_param('s',$dni);
        $stmt->execute();$stats['total_mascotas'] = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);$stmt->close();

        $stmt =$this->db->prepare("
            SELECT 
                SUM(CASE WHEN c.estado = 'Pendiente' THEN 1 ELSE 0 END) AS pendientes,
                SUM(CASE WHEN c.estado = 'Atendida' THEN 1 ELSE 0 END) AS atendidas
            FROM citas c
            INNER JOIN mascotas m ON c.id_mascota = m.id
            WHERE m.dni_dueno = ?
        ");
        $stmt->bind_param('s',$dni);
        $stmt->execute();$rowCitas = $stmt->get_result()->fetch_assoc();$stats['citas_pendientes'] = (int) ($rowCitas['pendientes'] ?? 0);$stats['citas_atendidas']  = (int) ($rowCitas['atendidas'] ?? 0);$stmt->close();

        $stmt =$this->db->prepare("
            SELECT c.fecha, c.hora, m.nombre AS nombre_mascota, v.nombre AS nombre_veterinario 
            FROM citas c 
            INNER JOIN mascotas m ON c.id_mascota = m.id 
            INNER JOIN veterinarios v ON c.id_veterinario = v.id 
            WHERE m.dni_dueno = ? AND c.estado = 'Pendiente' AND c.fecha >= ? 
            ORDER BY c.fecha ASC, c.hora ASC LIMIT 1
        ");
        $stmt->bind_param('ss',$dni, $hoyLima);$stmt->execute();
        $stats['proxima_cita'] =$stmt->get_result()->fetch_assoc() ?: null;
        $stmt->close();

        return $stats;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getCitasRecientesAdmin(int $limit = 10): array
    {
        $sql = "SELECT c.id, c.fecha, c.hora, c.motivo, c.estado,
                       m.nombre AS nombre_mascota, m.especie,
                       d.nombre AS nombre_dueno, d.telefono AS telefono_dueno,
                       v.nombre AS nombre_veterinario, v.especialidad
                FROM citas c
                INNER JOIN mascotas m ON c.id_mascota = m.id
                INNER JOIN duenos d ON m.dni_dueno = d.dni
                INNER JOIN veterinarios v ON c.id_veterinario = v.id
                ORDER BY c.fecha DESC, c.hora DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i',$limit);
        $stmt->execute();$res = $stmt->get_result();$citas = [];
        while ($fila =$res->fetch_assoc()) {
            $citas[] =$fila;
        }
        $stmt->close();
        return $citas;
    }
}