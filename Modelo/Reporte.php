<?php
require_once 'Servicios/conexion.php';

class Reporte {
    
    public static function obtenerEstadisticas() {
        $conn = conexion::conectar();
        
        $stats = [];
        
        // Total de mascotas
        $sql = "SELECT COUNT(*) as total FROM mascotas";
        $resultado = $conn->query($sql);
        $stats['total_mascotas'] = $resultado->fetch_assoc()['total'];
        
        // Total de dueños
        $sql = "SELECT COUNT(*) as total FROM duenos";
        $resultado = $conn->query($sql);
        $stats['total_duenos'] = $resultado->fetch_assoc()['total'];
        
        // Total de veterinarios
        $sql = "SELECT COUNT(*) as total FROM veterinarios";
        $resultado = $conn->query($sql);
        $stats['total_veterinarios'] = $resultado->fetch_assoc()['total'];
        
        // Total de citas
        $sql = "SELECT COUNT(*) as total FROM citas";
        $resultado = $conn->query($sql);
        $stats['total_citas'] = $resultado->fetch_assoc()['total'];
        
        // Citas pendientes
        $sql = "SELECT COUNT(*) as total FROM citas WHERE estado = 'Pendiente'";
        $resultado = $conn->query($sql);
        $stats['citas_pendientes'] = $resultado->fetch_assoc()['total'];
        
        // Citas atendidas
        $sql = "SELECT COUNT(*) as total FROM citas WHERE estado = 'Atendida'";
        $resultado = $conn->query($sql);
        $stats['citas_atendidas'] = $resultado->fetch_assoc()['total'];
        
        // Citas canceladas
        $sql = "SELECT COUNT(*) as total FROM citas WHERE estado = 'Cancelada'";
        $resultado = $conn->query($sql);
        $stats['citas_canceladas'] = $resultado->fetch_assoc()['total'];
        
        $conn->close();
        return $stats;
    }
    
    public static function obtenerMascotasPorEspecie() {
        $conn = conexion::conectar();
        $sql = "SELECT especie, COUNT(*) as cantidad 
                FROM mascotas 
                GROUP BY especie 
                ORDER BY cantidad DESC";
        $resultado = $conn->query($sql);
        
        $datos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }
        
        $conn->close();
        return $datos;
    }
    
    public static function obtenerVeterinariosPopulares() {
        $conn = conexion::conectar();
        $sql = "SELECT v.nombre, v.especialidad, COUNT(c.id) as total_citas 
                FROM veterinarios v 
                LEFT JOIN citas c ON v.id = c.id_veterinario 
                GROUP BY v.id, v.nombre, v.especialidad 
                ORDER BY total_citas DESC 
                LIMIT 5";
        $resultado = $conn->query($sql);
        
        $datos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }
        
        $conn->close();
        return $datos;
    }
    
    public static function obtenerCitasPorMes() {
        $conn = conexion::conectar();
        $sql = "SELECT DATE_FORMAT(fecha, '%Y-%m') as mes, COUNT(*) as cantidad 
                FROM citas 
                GROUP BY mes 
                ORDER BY mes DESC 
                LIMIT 12";
        $resultado = $conn->query($sql);
        
        $datos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }
        
        $conn->close();
        return $datos;
    }
}
?>