<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

final class InteraccionRepository
{
    public function __construct(private readonly mysqli $db)
    {
    }

    // --- RECLAMACIONES ---

    public function generateCodigoReclamoAtomic(): string
    {
        $anio = date('Y');
        $res =$this->db->query("SELECT COUNT(*) AS total FROM reclamaciones WHERE codigo LIKE 'REC-{$anio}-%' FOR UPDATE");
        $count = $res ? (int) ($res->fetch_assoc()['total'] ?? 0) : 0;
        return sprintf('REC-%s-%04d', $anio,$count + 1);
    }

    public function createReclamacion(array $d): bool
    {
        $this->db->begin_transaction();
        try {
            $codigo = $this->generateCodigoReclamoAtomic();$sql = "INSERT INTO reclamaciones (codigo, nombre_cliente, dni_cliente, email_cliente, telefono_cliente, tipo, detalle, pedido_cliente, estado)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pendiente')";
            $stmt =$this->db->prepare($sql);$stmt->bind_param(
                'ssssssss',
                $codigo,$d['nombre_cliente'],
                $d['dni_cliente'],$d['email_cliente'],
                $d['telefono_cliente'],$d['tipo'],
                $d['detalle'],$d['pedido_cliente']
            );
            $ok =$stmt->execute();
            $stmt->close();$this->db->commit();
            return $ok;
        } catch (\Throwable $e) {$this->db->rollback();
            throw $e;
        }
    }

    public function listReclamaciones(): array
    {
        $res =$this->db->query("SELECT * FROM reclamaciones ORDER BY fecha_registro DESC");
        $rows = [];
        while ($r =$res->fetch_assoc()) {
            $rows[] =$r;
        }
        return $rows;
    }

    public function updateEstadoReclamacion(int $id, string$estado): bool
    {
        $stmt =$this->db->prepare("UPDATE reclamaciones SET estado = ? WHERE id = ?");
        $stmt->bind_param('si',$estado, $id);$ok = $stmt->execute();$stmt->close();
        return $ok;
    }

    public function deleteReclamacion(int $id): bool
    {
        $stmt =$this->db->prepare("DELETE FROM reclamaciones WHERE id = ?");
        $stmt->bind_param('i', $id);$ok = $stmt->execute();$stmt->close();
        return $ok;
    }

    // --- SUGERENCIAS ---

    public function createSugerencia(array $d): bool
    {
        $stmt =$this->db->prepare("INSERT INTO sugerencias (nombre_cliente, email_cliente, mensaje) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $d['nombre_cliente'],$d['email_cliente'], $d['mensaje']);$ok = $stmt->execute();$stmt->close();
        return $ok;
    }

    public function listSugerencias(): array
    {
        $res =$this->db->query("SELECT * FROM sugerencias ORDER BY fecha_registro DESC");
        $rows = [];
        while ($r =$res->fetch_assoc()) {
            $rows[] =$r;
        }
        return $rows;
    }

    public function deleteSugerencia(int $id): bool
    {
        $stmt =$this->db->prepare("DELETE FROM sugerencias WHERE id = ?");
        $stmt->bind_param('i', $id);$ok = $stmt->execute();$stmt->close();
        return $ok;
    }

    // --- VIDEOS ---

    public function listVideos(): array
    {
        $res =$this->db->query("SELECT * FROM videos ORDER BY id DESC");
        $rows = [];
        while ($r =$res->fetch_assoc()) {
            $rows[] =$r;
        }
        return $rows;
    }

    public function findVideoById(int $id): ?array
    {
        $stmt =$this->db->prepare("SELECT * FROM videos WHERE id = ? LIMIT 1");
        $stmt->bind_param('i',$id);
        $stmt->execute();$res = $stmt->get_result();$row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row ?: null;
    }

    public function createVideo(string $titulo, string $descripcion, string$url): bool
    {
        $stmt =$this->db->prepare("INSERT INTO videos (titulo, descripcion, url) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $titulo,$descripcion, $url);$ok = $stmt->execute();$stmt->close();
        return $ok;
    }

    public function updateVideo(int $id, string$titulo, string $descripcion, string$url): bool
    {
        $stmt =$this->db->prepare("UPDATE videos SET titulo = ?, descripcion = ?, url = ? WHERE id = ?");
        $stmt->bind_param('sssi',$titulo, $descripcion,$url, $id);$ok = $stmt->execute();$stmt->close();
        return $ok;
    }

    public function deleteVideo(int $id): bool
    {
        $stmt =$this->db->prepare("DELETE FROM videos WHERE id = ?");
        $stmt->bind_param('i', $id);$ok = $stmt->execute();$stmt->close();
        return $ok;
    }
}