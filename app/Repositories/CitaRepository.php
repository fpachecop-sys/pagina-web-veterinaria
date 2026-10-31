<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

final class CitaRepository
{
    public function __construct(private readonly mysqli $db)
    {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listAll(): array
    {
        $sql = "SELECT c.id, c.fecha, c.hora, c.id_mascota, c.id_veterinario, c.motivo, c.estado, c.fecha_registro,
                       m.nombre AS nombre_mascota, m.especie,
                       d.nombre AS nombre_dueno, d.telefono AS telefono_dueno,
                       v.nombre AS nombre_veterinario, v.especialidad
                FROM citas c
                INNER JOIN mascotas m ON c.id_mascota = m.id
                INNER JOIN duenos d ON m.dni_dueno = d.dni
                INNER JOIN veterinarios v ON c.id_veterinario = v.id
                ORDER BY c.fecha DESC, c.hora DESC";
        $res =$this->db->query($sql);$items = [];
        while ($row =$res->fetch_assoc()) {
            $items[] =$row;
        }
        return $items;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT id, fecha, hora, id_mascota, id_veterinario, motivo, estado, fecha_registro
                FROM citas WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i',$id);
        $stmt->execute();$res = $stmt->get_result();$row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $row ?: null;
    }

    public function hasScheduleConflict(int $idVeterinario, string$fecha, string $hora, ?int $excludeCitaId = null): bool
    {
        $horaNorm = strlen($hora) === 5 ? $hora . ':00' :$hora;

        if ($excludeCitaId !== null) {$sql = "SELECT id FROM citas 
                    WHERE id_veterinario = ? AND fecha = ? AND hora = ? 
                      AND estado != 'Cancelada' AND id != ? 
                    LIMIT 1 FOR UPDATE";
            $stmt =$this->db->prepare($sql);$stmt->bind_param('issi', $idVeterinario,$fecha, $horaNorm,$excludeCitaId);
        } else {
            $sql = "SELECT id FROM citas 
                    WHERE id_veterinario = ? AND fecha = ? AND hora = ? 
                      AND estado != 'Cancelada' 
                    LIMIT 1 FOR UPDATE";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('iss',$idVeterinario, $fecha,$horaNorm);
        }

        $stmt->execute();
        $res =$stmt->get_result();
        $conflict = ($res && $res->num_rows > 0);$stmt->close();

        return $conflict;
    }

    public function create(string $fecha, string $hora, int$idMascota, int $idVeterinario, string$motivo): bool
    {
        $horaNorm = strlen($hora) === 5 ? $hora . ':00' :$hora;
        $sql = "INSERT INTO citas (fecha, hora, id_mascota, id_veterinario, motivo, estado) 
                VALUES (?, ?, ?, ?, ?, 'Pendiente')";
        $stmt =$this->db->prepare($sql);$stmt->bind_param('ssiis', $fecha,$horaNorm, $idMascota,$idVeterinario, $motivo);$ok = $stmt->execute();$stmt->close();

        return $ok;
    }

    public function update(
        int $id,
        string $fecha,
        string $hora,
        int $idMascota,
        int $idVeterinario,
        string $motivo,
        string $estado
    ): bool {
        $horaNorm = strlen($hora) === 5 ? $hora . ':00' :$hora;
        $sql = "UPDATE citas 
                SET fecha = ?, hora = ?, id_mascota = ?, id_veterinario = ?, motivo = ?, estado = ? 
                WHERE id = ?";
        $stmt =$this->db->prepare($sql);$stmt->bind_param('ssiissi', $fecha,$horaNorm, $idMascota,$idVeterinario, $motivo,$estado, $id);$ok = $stmt->execute();$stmt->close();

        return $ok;
    }

    public function updateEstado(int $id, string$estado): bool
    {
        $stmt =$this->db->prepare("UPDATE citas SET estado = ? WHERE id = ?");
        $stmt->bind_param('si',$estado, $id);$ok = $stmt->execute();$stmt->close();

        return $ok;
    }

    public function cancelOrDelete(int $id): bool
    {
        $stmt =$this->db->prepare("DELETE FROM citas WHERE id = ?");
        $stmt->bind_param('i', $id);$ok = $stmt->execute();$stmt->close();

        return $ok;
    }
}