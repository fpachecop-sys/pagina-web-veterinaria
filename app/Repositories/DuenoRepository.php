<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

final class DuenoRepository
{
    public function __construct(private readonly mysqli $db)
    {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listAllActive(): array
    {
        $sql = "SELECT dni, id_usuario, nombre, telefono, direccion, activo, fecha_registro 
                FROM duenos WHERE activo = 1 ORDER BY nombre ASC";
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
    public function findByDni(string $dni): ?array
    {
        $sql = "SELECT dni, id_usuario, nombre, telefono, direccion, activo, fecha_registro 
                FROM duenos WHERE dni = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $dni);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $row ?: null;
    }

    public function save(string $dni, string $nombre, string $telefono, string $direccion): bool
    {
        $existing = $this->findByDni($dni);
        if ($existing !== null) {
            // Si existía pero estaba inactivo, lo reactivamos y actualizamos
            if ((int) $existing['activo'] === 0) {
                $stmt = $this->db->prepare("UPDATE duenos SET nombre = ?, telefono = ?, direccion = ?, activo = 1 WHERE dni = ?");
                $stmt->bind_param('ssss', $nombre, $telefono, $direccion, $dni);
                $ok = $stmt->execute();
                $stmt->close();
                return $ok;
            }
            return false;
        }

        $stmt = $this->db->prepare("INSERT INTO duenos (dni, nombre, telefono, direccion, activo) VALUES (?, ?, ?, ?, 1)");
        $stmt->bind_param('ssss', $dni, $nombre, $telefono, $direccion);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function update(string $dni, string $nombre, string $telefono, string $direccion): bool
    {
        $stmt = $this->db->prepare("UPDATE duenos SET nombre = ?, telefono = ?, direccion = ? WHERE dni = ?");
        $stmt->bind_param('ssss', $nombre, $telefono, $direccion, $dni);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function deactivate(string $dni): bool
    {
        $this->db->begin_transaction();
        try {
            $stmt1 = $this->db->prepare("UPDATE mascotas SET activo = 0 WHERE dni_dueno = ?");
            $stmt1->bind_param('s', $dni);
            $stmt1->execute();
            $stmt1->close();

            $stmt2 = $this->db->prepare("UPDATE duenos SET activo = 0 WHERE dni = ?");
            $stmt2->bind_param('s', $dni);
            $ok = $stmt2->execute();
            $stmt2->close();

            $this->db->commit();
            return $ok;
        } catch (\Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
