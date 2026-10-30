<?php
require_once 'Modelo/Usuario.php';
require_once 'Modelo/Mascota.php';
require_once 'Modelo/Cita.php';

class ControladorUsuario {
    
    // Dashboard del usuario
    public function dashboard() {
        // Verificar que está logueado
        if (!Usuario::estaLogueado()) {
            header("Location: index.php?entidad=auth&accion=mostrarLogin");
            exit;
        }
        
        $usuario = Usuario::obtenerUsuarioActual();
        $id_usuario = $usuario['id'];
        
        // Obtener estadísticas del usuario
        $stats = $this->obtenerEstadisticasUsuario($id_usuario);
        
        require("Vista/usuario/dashboard.php");
    }
    
    // Ver perfil
    public function verPerfil() {
        if (!Usuario::estaLogueado()) {
            header("Location: index.php?entidad=auth&accion=mostrarLogin");
            exit;
        }
        
        $usuario_actual = Usuario::obtenerUsuarioActual();
        $usuario = Usuario::obtenerPorId($usuario_actual['id']);
        
        require("Vista/usuario/perfil.php");
    }
    
    // Editar perfil
    public function editarPerfil() {
        if (!Usuario::estaLogueado()) {
            header("Location: index.php?entidad=auth&accion=mostrarLogin");
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $telefono = $_POST['telefono'];
            $direccion = $_POST['direccion'];
            $dni = $_POST['dni'];
            
            if (Usuario::actualizarPerfil($id, $nombre, $apellido, $telefono, $direccion, $dni)) {
                // Actualizar datos en sesión
                $_SESSION['usuario_nombre'] = $nombre;
                $_SESSION['usuario_apellido'] = $apellido;
                
                $_SESSION['success'] = 'Perfil actualizado correctamente';
            } else {
                $_SESSION['error'] = 'Error al actualizar perfil';
            }
            
            header("Location: index.php?entidad=usuario&accion=verPerfil");
            exit;
        }
    }
    
    // Cambiar contraseña
    public function cambiarPassword() {
        if (!Usuario::estaLogueado()) {
            header("Location: index.php?entidad=auth&accion=mostrarLogin");
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario_actual = Usuario::obtenerUsuarioActual();
            $password_actual = $_POST['password_actual'];
            $password_nueva = $_POST['password_nueva'];
            $password_confirmar = $_POST['password_confirmar'];
            
            if ($password_nueva !== $password_confirmar) {
                $_SESSION['error'] = 'Las contraseñas no coinciden';
                header("Location: index.php?entidad=usuario&accion=verPerfil");
                exit;
            }
            
            $resultado = Usuario::cambiarPassword($usuario_actual['id'], $password_actual, $password_nueva);
            
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
            } else {
                $_SESSION['error'] = $resultado['message'];
            }
            
            header("Location: index.php?entidad=usuario&accion=verPerfil");
            exit;
        }
    }
    
    // Mis mascotas
    public function misMascotas() {
        if (!Usuario::estaLogueado()) {
            header("Location: index.php?entidad=auth&accion=mostrarLogin");
            exit;
        }
        
        $usuario = Usuario::obtenerUsuarioActual();
        $mascotas = $this->obtenerMascotasDelUsuario($usuario['id']);
        
        require("Vista/usuario/mis_mascotas.php");
    }
    
    // Mis citas
    public function misCitas() {
        if (!Usuario::estaLogueado()) {
            header("Location: index.php?entidad=auth&accion=mostrarLogin");
            exit;
        }
        
        $usuario = Usuario::obtenerUsuarioActual();
        $citas = $this->obtenerCitasDelUsuario($usuario['id']);
        
        require("Vista/usuario/mis_citas.php");
    }
    
    // === MÉTODOS AUXILIARES ===
    
    private function obtenerEstadisticasUsuario($id_usuario) {
        $conn = conexion::conectar();
        $stats = [];
        
        // Total de mascotas del usuario
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM mascotas m 
                                INNER JOIN duenos d ON m.dni_dueno = d.dni 
                                WHERE d.id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $stats['total_mascotas'] = $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();
        
        // Total de citas del usuario
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM citas c
                                INNER JOIN mascotas m ON c.id_mascota = m.id
                                INNER JOIN duenos d ON m.dni_dueno = d.dni
                                WHERE d.id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $stats['total_citas'] = $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();
        
        // Citas pendientes
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM citas c
                                INNER JOIN mascotas m ON c.id_mascota = m.id
                                INNER JOIN duenos d ON m.dni_dueno = d.dni
                                WHERE d.id_usuario = ? AND c.estado = 'Pendiente'");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $stats['citas_pendientes'] = $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();
        
        // Próxima cita
        $stmt = $conn->prepare("SELECT c.*, m.nombre as nombre_mascota, v.nombre as nombre_veterinario
                                FROM citas c
                                INNER JOIN mascotas m ON c.id_mascota = m.id
                                INNER JOIN veterinarios v ON c.id_veterinario = v.id
                                INNER JOIN duenos d ON m.dni_dueno = d.dni
                                WHERE d.id_usuario = ? AND c.estado = 'Pendiente'
                                AND c.fecha >= CURDATE()
                                ORDER BY c.fecha ASC, c.hora ASC
                                LIMIT 1");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $stats['proxima_cita'] = $resultado->num_rows > 0 ? $resultado->fetch_assoc() : null;
        $stmt->close();
        
        $conn->close();
        return $stats;
    }
    
    private function obtenerMascotasDelUsuario($id_usuario) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("SELECT m.* FROM mascotas m
                                INNER JOIN duenos d ON m.dni_dueno = d.dni
                                WHERE d.id_usuario = ?
                                ORDER BY m.fecha_registro DESC");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $mascotas = [];
        while ($fila = $resultado->fetch_assoc()) {
            $mascotas[] = $fila;
        }
        $stmt->close();
        $conn->close();
        return $mascotas;
    }
    
    private function obtenerCitasDelUsuario($id_usuario) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("SELECT c.*, m.nombre as nombre_mascota, v.nombre as nombre_veterinario
                                FROM citas c
                                INNER JOIN mascotas m ON c.id_mascota = m.id
                                INNER JOIN veterinarios v ON c.id_veterinario = v.id
                                INNER JOIN duenos d ON m.dni_dueno = d.dni
                                WHERE d.id_usuario = ?
                                ORDER BY c.fecha DESC, c.hora DESC");
        $stmt->bind_param("i", $id_usuario);
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
}
?>