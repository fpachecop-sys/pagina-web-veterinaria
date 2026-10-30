<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

final class MascotaRepository
{
    public function __construct(private readonly mysqli $db)
    {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listAllActive(): array
    {
        $sql = "SELECT m.id, m.nombre, m.especie, m.raza, m.edad, m.dni_dueno, m.activo, m.fecha_registro,
                       d.nombre AS nombre_dueno
                FROM mascotas m
                INNER JOIN duenos d ON m.dni_dueno = d.dni
                WHERE m.activo = 1
                ORDER BY m.id DESC";
        $res = $this->db->query($sql);
        $items = [];
        while ($row = $res->fetch_assoc()) {
            $items[] = $row;
        }
        return $items;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listByDuenoDni(string $dni): array
    {
        if ($dni === '') {
            return [];
        }
        $sql = "SELECT id, nombre, especie, raza, edad, dni_dueno, fecha_registro 
                FROM mascotas WHERE dni_dueno = ? AND activo = 1 ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $dni);
        $stmt->execute();
        $res = $stmt->get_result();
        $items = [];
        while ($row = $res->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt->close();
        return $items;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT id, nombre, especie, raza, edad, dni_dueno, activo, fecha_registro 
                FROM mascotas WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $row ?: null;
    }

    public function create(string $nombre, string $especie, string $raza, int $edad, string $dniDueno): bool
    {
        $sql = "INSERT INTO mascotas (nombre, especie, raza, edad, dni_dueno, activo) VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssis', $nombre, $especie, $raza, $edad, $dniDueno);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function update(int $id, string $nombre, string $especie, string $raza, int $edad, string $dniDueno): bool
    {
        $sql = "UPDATE mascotas SET nombre = ?, especie = ?, raza = ?, edad = ?, dni_dueno = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssisi', $nombre, $especie, $raza, $edad, $dniDueno, $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function deactivate(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE mascotas SET activo = 0 WHERE id = ?");
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }
}
