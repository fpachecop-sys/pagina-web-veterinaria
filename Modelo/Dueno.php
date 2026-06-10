<?php
require_once 'Servicios/conexion.php';

class Dueno {
    
    public static function obtenerTodos() {
        $conn = conexion::conectar();
        $sql = "SELECT dni, nombre, telefono, direccion, fecha_registro FROM duenos ORDER BY fecha_registro DESC";
        $resultado = $conn->query($sql);
        
        $duenos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $duenos[] = $fila;
        }
        
        $conn->close();
        return $duenos;
    }
    
    public static function obtenerPorDni($dni) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("SELECT * FROM duenos WHERE dni = ?");
        $stmt->bind_param("s", $dni);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
    
    public static function agregar($dni, $nombre, $telefono, $direccion) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("INSERT INTO duenos (dni, nombre, telefono, direccion) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $dni, $nombre, $telefono, $direccion);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
    
    public static function actualizar($dni, $nombre, $telefono, $direccion) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("UPDATE duenos SET nombre=?, telefono=?, direccion=? WHERE dni=?");
        $stmt->bind_param("ssss", $nombre, $telefono, $direccion, $dni);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
    
    public static function eliminar($dni) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("DELETE FROM duenos WHERE dni=?");
        $stmt->bind_param("s", $dni);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
}
?>