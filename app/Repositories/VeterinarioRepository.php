<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

final class VeterinarioRepository
{
    public function __construct(private readonly mysqli $db)
    {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listAllActive(): array
    {
        $sql = "SELECT id, nombre, especialidad, correo, telefono, activo, fecha_registro 
                FROM veterinarios WHERE activo = 1 ORDER BY nombre ASC";
        $res = $this->db->query($sql);
        $items = [];
        while ($row = $res->fetch_assoc()) {
            $items[] = $row;
        }
        return $items;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT id, nombre, especialidad, correo, telefono, activo, fecha_registro 
                FROM veterinarios WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $row ?: null;
    }

    public function existsEmail(string $correo, ?int $excludeId = null): bool
    {
        $correoNorm = strtolower(trim($correo));
        if ($excludeId !== null) {
            $stmt = $this->db->prepare("SELECT id FROM veterinarios WHERE correo = ? AND id != ? LIMIT 1");
            $stmt->bind_param('si', $correoNorm, $excludeId);
        } else {
            $stmt = $this->db->prepare("SELECT id FROM veterinarios WHERE correo = ? LIMIT 1");
            $stmt->bind_param('s', $correoNorm);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        $exists = ($res && $res->num_rows > 0);
        $stmt->close();

        return $exists;
    }

    public function create(string $nombre, string $especialidad, string $correo, string $telefono): bool
    {
        $correoNorm = strtolower(trim($correo));
        $sql = "INSERT INTO veterinarios (nombre, especialidad, correo, telefono, activo) VALUES (?, ?, ?, ?, 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssss', $nombre, $especialidad, $correoNorm, $telefono);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function update(int $id, string $nombre, string $especialidad, string $correo, string $telefono): bool
    {
        $correoNorm = strtolower(trim($correo));
        $sql = "UPDATE veterinarios SET nombre = ?, especialidad = ?, correo = ?, telefono = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssssi', $nombre, $especialidad, $correoNorm, $telefono, $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function deactivate(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE veterinarios SET activo = 0 WHERE id = ?");
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }
}
