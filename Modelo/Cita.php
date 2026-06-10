<?php
require_once 'Servicios/conexion.php';

class Cita {
    
    public static function obtenerTodos() {
        $conn = conexion::conectar();
        $sql = "SELECT c.*, 
                       m.nombre as nombre_mascota, 
                       v.nombre as nombre_veterinario,
                       d.nombre as nombre_dueno
                FROM citas c 
                INNER JOIN mascotas m ON c.id_mascota = m.id
                INNER JOIN veterinarios v ON c.id_veterinario = v.id
                INNER JOIN duenos d ON m.dni_dueno = d.dni
                ORDER BY c.fecha DESC, c.hora DESC";
        $resultado = $conn->query($sql);
        
        $citas = [];
        while ($fila = $resultado->fetch_assoc()) {
            $citas[] = $fila;
        }
        
        $conn->close();
        return $citas;
    }
    
    public static function obtenerPorId($id) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("SELECT c.*, 
                                       m.nombre as nombre_mascota,
                                       v.nombre as nombre_veterinario
                                FROM citas c 
                                INNER JOIN mascotas m ON c.id_mascota = m.id
                                INNER JOIN veterinarios v ON c.id_veterinario = v.id
                                WHERE c.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
    
    public static function obtenerMascotas() {
        $conn = conexion::conectar();
        $sql = "SELECT m.id, m.nombre, d.nombre as nombre_dueno 
                FROM mascotas m 
                INNER JOIN duenos d ON m.dni_dueno = d.dni 
                ORDER BY m.nombre";
        $resultado = $conn->query($sql);
        
        $mascotas = [];
        while ($fila = $resultado->fetch_assoc()) {
            $mascotas[] = $fila;
        }
        
        $conn->close();
        return $mascotas;
    }
    
    public static function obtenerVeterinarios() {
        $conn = conexion::conectar();
        $sql = "SELECT id, nombre, especialidad FROM veterinarios ORDER BY nombre";
        $resultado = $conn->query($sql);
        
        $veterinarios = [];
        while ($fila = $resultado->fetch_assoc()) {
            $veterinarios[] = $fila;
        }
        
        $conn->close();
        return $veterinarios;
    }
    
    public static function agregar($fecha, $hora, $id_mascota, $id_veterinario, $motivo) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("INSERT INTO citas (fecha, hora, id_mascota, id_veterinario, motivo, estado) 
                                VALUES (?, ?, ?, ?, ?, 'Pendiente')");
        $stmt->bind_param("ssiis", $fecha, $hora, $id_mascota, $id_veterinario, $motivo);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
    
    public static function actualizar($id, $fecha, $hora, $id_mascota, $id_veterinario, $motivo, $estado) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("UPDATE citas 
                                SET fecha=?, hora=?, id_mascota=?, id_veterinario=?, motivo=?, estado=? 
                                WHERE id=?");
        $stmt->bind_param("ssiissi", $fecha, $hora, $id_mascota, $id_veterinario, $motivo, $estado, $id);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
    
    public static function cambiarEstado($id, $estado) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("UPDATE citas SET estado=? WHERE id=?");
        $stmt->bind_param("si", $estado, $id);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
    
    public static function eliminar($id) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("DELETE FROM citas WHERE id=?");
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
}
?>