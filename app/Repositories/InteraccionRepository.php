<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

final class InteraccionRepository
{
    public function __construct(private readonly mysqli $db)
    {
    }

    public function createReclamacion(array $d): bool
    {
        $tipoDoc   = trim((string) ($d['tipo_documento'] ?? 'DNI'));
        $numDoc    = trim((string) ($d['numero_documento'] ?? $d['dni_cliente'] ?? ''));
        $nombres   = trim((string) ($d['nombres'] ?? $d['nombre_cliente'] ?? ''));
        $apellidos = trim((string) ($d['apellidos'] ?? ''));
        $telefono  = trim((string) ($d['telefono'] ?? $d['telefono_cliente'] ?? ''));
        $email     = trim((string) ($d['email'] ?? $d['email_cliente'] ?? ''));
        $direccion = trim((string) ($d['direccion'] ?? 'No especificada'));
        $tipoRec   = trim((string) ($d['tipo_reclamo'] ?? $d['tipo'] ?? 'Reclamo'));
        $detalle   = trim((string) ($d['detalle'] ?? ''));
        $pedido    = trim((string) ($d['pedido'] ?? $d['pedido_cliente'] ?? ''));

        $sql = "INSERT INTO reclamaciones 
                (tipo_documento, numero_documento, nombres, apellidos, telefono, email, direccion, tipo_reclamo, detalle, pedido, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pendiente')";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(
            'ssssssssss',
            $tipoDoc,
            $numDoc,
            $nombres,
            $apellidos,
            $telefono,
            $email,
            $direccion,
            $tipoRec,
            $detalle,
            $pedido
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listReclamaciones(): array
    {
        $res = $this->db->query("SELECT * FROM reclamaciones ORDER BY fecha_registro DESC");
        $rows = [];
        while ($r = $res->fetch_assoc()) {
            $rows[] = $r;
        }
        return $rows;
    }

    public function updateEstadoReclamacion(int $id, string $estado): bool
    {
        $stmt = $this->db->prepare("UPDATE reclamaciones SET estado = ? WHERE id = ?");
        $stmt->bind_param('si', $estado, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function deleteReclamacion(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM reclamaciones WHERE id = ?");
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function createSugerencia(array $d): bool
    {
        $nombre     = trim((string) ($d['nombre'] ?? $d['nombre_cliente'] ?? ''));
        $email      = trim((string) ($d['email'] ?? $d['email_cliente'] ?? ''));
        $telefono   = trim((string) ($d['telefono'] ?? ''));
        $categoria  = trim((string) ($d['categoria'] ?? 'General'));
        $sugerencia = trim((string) ($d['sugerencia'] ?? $d['mensaje'] ?? ''));

        $sql = "INSERT INTO sugerencias (nombre, email, telefono, categoria, sugerencia, estado) 
                VALUES (?, ?, ?, ?, ?, 'Pendiente')";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssss', $nombre, $email, $telefono, $categoria, $sugerencia);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listSugerencias(): array
    {
        $res = $this->db->query("SELECT * FROM sugerencias ORDER BY fecha_registro DESC");
        $rows = [];
        while ($r = $res->fetch_assoc()) {
            $rows[] = $r;
        }
        return $rows;
    }

    public function deleteSugerencia(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM sugerencias WHERE id = ?");
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listVideos(): array
    {
        $res = $this->db->query("SELECT * FROM videos ORDER BY id DESC");
        $rows = [];
        while ($r = $res->fetch_assoc()) {
            $rows[] = $r;
        }
        return $rows;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findVideoById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM videos WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row ?: null;
    }

    public function createVideo(string $titulo, string $descripcion, string $urlVideo, string $categoria = 'Servicios'): bool
    {
        $sql = "INSERT INTO videos (titulo, descripcion, url_video, categoria, activo) VALUES (?, ?, ?, ?, 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssss', $titulo, $descripcion, $urlVideo, $categoria);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function updateVideo(int $id, string $titulo, string $descripcion, string $urlVideo, string $categoria = 'Servicios', int $activo = 1): bool
    {
        $sql = "UPDATE videos SET titulo = ?, descripcion = ?, url_video = ?, categoria = ?, activo = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssssii', $titulo, $descripcion, $urlVideo, $categoria, $activo, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function deleteVideo(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM videos WHERE id = ?");
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}