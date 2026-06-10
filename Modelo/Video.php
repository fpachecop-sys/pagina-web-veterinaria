<?php
require_once 'Servicios/conexion.php';

class Video {
    public static function obtenerTodos() {
        $conn = conexion::conectar();
        $sql = "SELECT * FROM videos ORDER BY fecha_registro DESC";
        $resultado = $conn->query($sql);
        $videos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $videos[] = $fila;
        }
        $conn->close();
        return $videos;
    }

    public static function obtenerPorId($id) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("SELECT * FROM videos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $resultado;
    }

    public static function agregar($titulo, $descripcion, $url_video, $categoria) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("INSERT INTO videos (titulo, descripcion, url_video, categoria) 
                                VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $titulo, $descripcion, $url_video, $categoria);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }

    public static function actualizar($id, $titulo, $descripcion, $url_video, $categoria, $activo) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("UPDATE videos SET titulo=?, descripcion=?, url_video=?, 
                                categoria=?, activo=? WHERE id=?");
        $stmt->bind_param("ssssii", $titulo, $descripcion, $url_video, $categoria, $activo, $id);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }

    public static function eliminar($id) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("DELETE FROM videos WHERE id=?");
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
}
?>
