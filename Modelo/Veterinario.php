<?php
require_once 'Servicios/conexion.php';

class Veterinario {
    
    public static function obtenerTodos() {
        $conn = conexion::conectar();
        $sql = "SELECT id, nombre, especialidad, correo, telefono, fecha_registro 
                FROM veterinarios 
                ORDER BY fecha_registro DESC";
        $resultado = $conn->query($sql);
        
        $veterinarios = [];
        while ($fila = $resultado->fetch_assoc()) {
            $veterinarios[] = $fila;
        }
        
        $conn->close();
        return $veterinarios;
    }
    
    public static function obtenerPorId($id) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("SELECT * FROM veterinarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
    
    public static function agregar($nombre, $especialidad, $correo, $telefono) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("INSERT INTO veterinarios (nombre, especialidad, correo, telefono) 
                                VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $especialidad, $correo, $telefono);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
    
    public static function actualizar($id, $nombre, $especialidad, $correo, $telefono) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("UPDATE veterinarios 
                                SET nombre=?, especialidad=?, correo=?, telefono=? 
                                WHERE id=?");
        $stmt->bind_param("ssssi", $nombre, $especialidad, $correo, $telefono, $id);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
    
    public static function eliminar($id) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("DELETE FROM veterinarios WHERE id=?");
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
}
?>