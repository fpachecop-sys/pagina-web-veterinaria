<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

final class UsuarioRepository
{
    public function __construct(private readonly mysqli $db)
    {
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT id, nombre, apellido, email, password, telefono, direccion, dni, rol, activo 
                FROM usuarios WHERE email = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $emailNorm = strtolower(trim($email));
        $stmt->bind_param('s',$emailNorm);
        $stmt->execute();$res = $stmt->get_result();$row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $row ?: null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT id, nombre, apellido, email, password, telefono, direccion, dni, rol, activo, fecha_registro 
                FROM usuarios WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i',$id);
        $stmt->execute();$res = $stmt->get_result();$row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $row ?: null;
    }

    public function existsEmail(string $email, ?int $excludeId = null): bool
    {
        $emailNorm = strtolower(trim($email));
        if ($excludeId !== null) {
            $stmt =$this->db->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ? LIMIT 1");
            $stmt->bind_param('si', $emailNorm,$excludeId);
        } else {
            $stmt =$this->db->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
            $stmt->bind_param('s',$emailNorm);
        }
        $stmt->execute();
        $res =$stmt->get_result();
        $exists = ($res && $res->num_rows > 0);$stmt->close();

        return $exists;
    }

    public function existsDniInUsuarios(string $dni, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt =$this->db->prepare("SELECT id FROM usuarios WHERE dni = ? AND id != ? LIMIT 1");
            $stmt->bind_param('si', $dni,$excludeId);
        } else {
            $stmt =$this->db->prepare("SELECT id FROM usuarios WHERE dni = ? LIMIT 1");
            $stmt->bind_param('s',$dni);
        }
        $stmt->execute();
        $res =$stmt->get_result();
        $exists = ($res && $res->num_rows > 0);$stmt->close();

        return $exists;
    }

    public function existsDniInDuenos(string $dni, ?int $excludeUserId = null): bool
    {
        if ($excludeUserId !== null) {
            $stmt =$this->db->prepare("SELECT dni FROM duenos WHERE dni = ? AND (id_usuario IS NULL OR id_usuario != ?) LIMIT 1");
            $stmt->bind_param('si', $dni,$excludeUserId);
        } else {
            $stmt =$this->db->prepare("SELECT dni FROM duenos WHERE dni = ? LIMIT 1");
            $stmt->bind_param('s',$dni);
        }
        $stmt->execute();
        $res =$stmt->get_result();
        $exists = ($res && $res->num_rows > 0);$stmt->close();

        return $exists;
    }

    public function createUsuario(
        string $nombre,
        string $apellido,
        string $email,
        string $passwordHash,
        string $telefono,
        string $direccion,
        string $dni,
        string $rol = 'usuario'
    ): int {
        $sql = "INSERT INTO usuarios (nombre, apellido, email, password, telefono, direccion, dni, rol) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $emailNorm = strtolower(trim($email));
        $stmt->bind_param('ssssssss',$nombre, $apellido,$emailNorm, $passwordHash,$telefono, $direccion,$dni, $rol);$stmt->execute();
        $id = (int)$this->db->insert_id;
        $stmt->close();

        return $id;
    }

    public function createDuenoVinculado(
        string $dni,
        int $idUsuario,
        string $nombreCompleto,
        string $telefono,
        string $direccion
    ): void {
        $sql = "INSERT INTO duenos (dni, id_usuario, nombre, telefono, direccion) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sisss',$dni, $idUsuario,$nombreCompleto, $telefono,$direccion);
        $stmt->execute();$stmt->close();
    }

    public function updatePerfilUsuario(
        int $id,
        string $nombre,
        string $apellido,
        string $telefono,
        string $direccion,
        string $dni
    ): void {
        $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, telefono = ?, direccion = ?, dni = ? WHERE id = ?";
        $stmt =$this->db->prepare($sql);$stmt->bind_param('sssssi', $nombre,$apellido, $telefono,$direccion, $dni,$id);
        $stmt->execute();$stmt->close();
    }

    public function updateDuenoVinculado(
        int $idUsuario,
        string $dni,
        string $nombreCompleto,
        string $telefono,
        string $direccion
    ): void {
        $sql = "UPDATE duenos SET dni = ?, nombre = ?, telefono = ?, direccion = ? WHERE id_usuario = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssssi',$dni, $nombreCompleto,$telefono, $direccion,$idUsuario);
        $stmt->execute();$stmt->close();
    }

    public function updatePassword(int $id, string$newHash): void
    {
        $stmt =$this->db->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt->bind_param('si', $newHash,$id);
        $stmt->execute();$stmt->close();
    }
}
