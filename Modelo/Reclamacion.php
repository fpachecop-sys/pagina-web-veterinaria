<?php
require_once 'Servicios/conexion.php';

class Reclamacion {
    public static function obtenerTodos() {
        $conn = conexion::conectar();
        $sql = "SELECT * FROM reclamaciones ORDER BY fecha_registro DESC";
        $resultado = $conn->query($sql);
        $reclamaciones = [];
        while ($fila = $resultado->fetch_assoc()) {
            $reclamaciones[] = $fila;
        }
        $conn->close();
        return $reclamaciones;
    }

    public static function agregar($tipo_documento, $numero_documento, $nombres, $apellidos, 
                                   $telefono, $email, $direccion, $tipo_reclamo, $detalle, $pedido) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("INSERT INTO reclamaciones (tipo_documento, numero_documento, nombres, 
                                apellidos, telefono, email, direccion, tipo_reclamo, detalle, pedido) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $tipo_documento, $numero_documento, $nombres, $apellidos, 
                         $telefono, $email, $direccion, $tipo_reclamo, $detalle, $pedido);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }

    public static function obtenerUltimoId() {
        $conn = conexion::conectar();
        $sql = "SELECT MAX(id) as ultimo_id FROM reclamaciones";
        $resultado = $conn->query($sql);
        $id = $resultado->fetch_assoc()['ultimo_id'];
        $conn->close();
        return $id;
    }

    public static function cambiarEstado($id, $estado) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("UPDATE reclamaciones SET estado=? WHERE id=?");
        $stmt->bind_param("si", $estado, $id);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
}
?>