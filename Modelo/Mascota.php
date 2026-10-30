<?php
require_once 'Servicios/conexion.php';

class Mascota {
    
    public static function obtenerTodos() {
        $conn = conexion::conectar();
        $sql = "SELECT m.*, d.nombre as nombre_dueno 
                FROM mascotas m 
                INNER JOIN duenos d ON m.dni_dueno = d.dni 
                ORDER BY m.fecha_registro DESC";
        $resultado = $conn->query($sql);
        
        $mascotas = [];
        while ($fila = $resultado->fetch_assoc()) {
            $mascotas[] = $fila;
        }
        
        $conn->close();
        return $mascotas;
    }
    
    public static function obtenerPorId($id) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("SELECT m.*, d.nombre as nombre_dueno 
                                FROM mascotas m 
                                INNER JOIN duenos d ON m.dni_dueno = d.dni 
                                WHERE m.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
    
    public static function obtenerDuenos() {
        $conn = conexion::conectar();
        $sql = "SELECT dni, nombre FROM duenos ORDER BY nombre";
        $resultado = $conn->query($sql);
        
        $duenos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $duenos[] = $fila;
        }
        
        $conn->close();
        return $duenos;
    }
    
    public static function agregar($nombre, $especie, $raza, $edad, $dni_dueno) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("INSERT INTO mascotas (nombre, especie, raza, edad, dni_dueno) 
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $nombre, $especie, $raza, $edad, $dni_dueno);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
    
    public static function actualizar($id, $nombre, $especie, $raza, $edad, $dni_dueno) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("UPDATE mascotas SET nombre=?, especie=?, raza=?, edad=?, dni_dueno=? 
                                WHERE id=?");
        $stmt->bind_param("sssisi", $nombre, $especie, $raza, $edad, $dni_dueno, $id);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
    
    public static function eliminar($id) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("DELETE FROM mascotas WHERE id=?");
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
}
?>