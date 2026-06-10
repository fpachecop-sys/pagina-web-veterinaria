<?php
require_once 'Servicios/conexion.php';

class Sugerencia {
    public static function obtenerTodos() {
        $conn = conexion::conectar();
        $sql = "SELECT * FROM sugerencias ORDER BY fecha_registro DESC";
        $resultado = $conn->query($sql);
        $sugerencias = [];
        while ($fila = $resultado->fetch_assoc()) {
            $sugerencias[] = $fila;
        }
        $conn->close();
        return $sugerencias;
    }

    public static function agregar($nombre, $email, $telefono, $categoria, $sugerencia) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("INSERT INTO sugerencias (nombre, email, telefono, categoria, sugerencia) 
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nombre, $email, $telefono, $categoria, $sugerencia);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }

    public static function eliminar($id) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("DELETE FROM sugerencias WHERE id=?");
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
}
?>