<?php
require_once 'Modelo/Usuario.php';
require_once 'Modelo/Mascota.php';
require_once 'Modelo/Cita.php';
require_once 'Modelo/Dueno.php';
require_once 'Modelo/Veterinario.php';
require_once 'Modelo/Reporte.php';

class ControladorAdmin {
    
    public function dashboard() {
        // Verificar que es administrador
        if (!Usuario::esAdministrador()) {
            $_SESSION['error'] = 'Acceso denegado';
            header("Location: index.php");
            exit;
        }
        
        $usuario = Usuario::obtenerUsuarioActual();
        
        // Obtener estadísticas generales del sistema
        $stats = Reporte::obtenerEstadisticas();
        $citasRecientes = $this->obtenerCitasRecientes(10);
        $usuariosRecientes = $this->obtenerUsuariosRecientes(5);
        $mascotasRecientes = Mascota::obtenerTodos(); // Últimas 5
        
        require("Vista/admin/dashboard.php");
    }
    
    // Gestión de usuarios
    public function listarUsuarios() {
        if (!Usuario::esAdministrador()) {
            $_SESSION['error'] = 'Acceso denegado';
            header("Location: index.php");
            exit;
        }
        
        $usuarios = Usuario::obtenerTodos();
        require("Vista/admin/usuarios_listar.php");
    }
    
    public function cambiarEstadoUsuario() {
        if (!Usuario::esAdministrador()) {
            $_SESSION['error'] = 'Acceso denegado';
            header("Location: index.php");
            exit;
        }
        
        $id = $_GET['id'];
        $activo = $_GET['activo'];
        
        if (Usuario::cambiarEstado($id, $activo)) {
            $_SESSION['success'] = 'Estado de usuario actualizado';
        } else {
            $_SESSION['error'] = 'Error al actualizar estado';
        }
        
        header("Location: index.php?entidad=admin&accion=listarUsuarios");
        exit;
    }
    
    // Métodos auxiliares privados
    private function obtenerCitasRecientes($limite = 10) {
        $conn = conexion::conectar();
        $sql = "SELECT c.*, 
                       m.nombre as nombre_mascota, 
                       v.nombre as nombre_veterinario,
                       d.nombre as nombre_dueno
                FROM citas c
                INNER JOIN mascotas m ON c.id_mascota = m.id
                INNER JOIN veterinarios v ON c.id_veterinario = v.id
                INNER JOIN duenos d ON m.dni_dueno = d.dni
                ORDER BY c.fecha_registro DESC
                LIMIT ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $limite);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $citas = [];
        while ($fila = $resultado->fetch_assoc()) {
            $citas[] = $fila;
        }
        
        $stmt->close();
        $conn->close();
        
        return $citas;
    }
    
    private function obtenerUsuariosRecientes($limite = 5) {
        $conn = conexion::conectar();
        $sql = "SELECT id, nombre, apellido, email, rol, fecha_registro, activo
                FROM usuarios
                ORDER BY fecha_registro DESC
                LIMIT ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $limite);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $usuarios = [];
        while ($fila = $resultado->fetch_assoc()) {
            $usuarios[] = $fila;
        }
        
        $stmt->close();
        $conn->close();
        
        return $usuarios;
    }
}
?>