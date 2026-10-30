<?php
require_once 'Servicios/conexion.php';

class Usuario {
    
    // Registrar nuevo usuario
    public static function registrar($nombre, $apellido, $email, $password, $telefono = '', $direccion = '', $dni = '') {
        $conn = conexion::conectar();
        
        // Verificar si el email ya existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $stmt->close();
            $conn->close();
            return ['success' => false, 'message' => 'El correo electrónico ya está registrado'];
        }
        $stmt->close();
        
        // Encriptar contraseña
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insertar usuario
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, email, password, telefono, direccion, dni, rol) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, 'usuario')");
        $stmt->bind_param("sssssss", $nombre, $apellido, $email, $password_hash, $telefono, $direccion, $dni);
        
        if ($stmt->execute()) {
            $id_usuario = $conn->insert_id;
            
            // Crear registro en duenos automáticamente
            $nombre_completo = $nombre . ' ' . $apellido;
            $stmt2 = $conn->prepare("INSERT INTO duenos (dni, nombre, telefono, direccion, id_usuario) 
                                     VALUES (?, ?, ?, ?, ?)");
            $stmt2->bind_param("ssssi", $dni, $nombre_completo, $telefono, $direccion, $id_usuario);
            $stmt2->execute();
            $stmt2->close();
            
            $stmt->close();
            $conn->close();
            return ['success' => true, 'message' => 'Usuario registrado correctamente'];
        } else {
            $stmt->close();
            $conn->close();
            return ['success' => false, 'message' => 'Error al registrar usuario'];
        }
    }
    
    // Iniciar sesión
    public static function login($email, $password) {
        $conn = conexion::conectar();
        
        $stmt = $conn->prepare("SELECT id, nombre, apellido, email, password, rol, activo FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows === 0) {
            $stmt->close();
            $conn->close();
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }
        
        $usuario = $resultado->fetch_assoc();
        $stmt->close();
        $conn->close();
        
        // Verificar si el usuario está activo
        if (!$usuario['activo']) {
            return ['success' => false, 'message' => 'Usuario desactivado. Contacte al administrador'];
        }
        
        // Verificar contraseña
        if (password_verify($password, $usuario['password'])) {
            // Crear sesión
            session_start();
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_apellido'] = $usuario['apellido'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_rol'] = $usuario['rol'];
            $_SESSION['usuario_logged_in'] = true;
            
            return ['success' => true, 'rol' => $usuario['rol']];
        } else {
            return ['success' => false, 'message' => 'Contraseña incorrecta'];
        }
    }
    
    // Cerrar sesión
    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        return true;
    }

    // Verificar si está logueado
    public static function estaLogueado() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['usuario_logged_in']) && $_SESSION['usuario_logged_in'] === true;
    }
    
    // Verificar si es administrador
    public static function esAdministrador() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'administrador';
    }
    
    // Obtener datos del usuario logueado
    public static function obtenerUsuarioActual() {
        if (!self::estaLogueado()) {
            return null;
        }
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'],
            'apellido' => $_SESSION['usuario_apellido'],
            'email' => $_SESSION['usuario_email'],
            'rol' => $_SESSION['usuario_rol']
        ];
    }
    
    // Obtener usuario por ID
    public static function obtenerPorId($id) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("SELECT id, nombre, apellido, email, telefono, direccion, dni, rol, activo, fecha_registro 
                                FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
    
    // Actualizar perfil
    public static function actualizarPerfil($id, $nombre, $apellido, $telefono, $direccion, $dni) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("UPDATE usuarios 
                                SET nombre=?, apellido=?, telefono=?, direccion=?, dni=? 
                                WHERE id=?");
        $stmt->bind_param("sssssi", $nombre, $apellido, $telefono, $direccion, $dni, $id);
        $resultado = $stmt->execute();
        
        // Actualizar también en duenos si existe
        if ($resultado) {
            $nombre_completo = $nombre . ' ' . $apellido;
            $stmt2 = $conn->prepare("UPDATE duenos 
                                     SET nombre=?, telefono=?, direccion=?, dni=? 
                                     WHERE id_usuario=?");
            $stmt2->bind_param("ssssi", $nombre_completo, $telefono, $direccion, $dni, $id);
            $stmt2->execute();
            $stmt2->close();
        }
        
        $stmt->close();
        $conn->close();
        return $resultado;
    }
    
    // Cambiar contraseña
    public static function cambiarPassword($id, $password_actual, $password_nueva) {
        $conn = conexion::conectar();
        
        // Verificar contraseña actual
        $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!password_verify($password_actual, $resultado['password'])) {
            $conn->close();
            return ['success' => false, 'message' => 'La contraseña actual es incorrecta'];
        }
        
        // Actualizar contraseña
        $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET password=? WHERE id=?");
        $stmt->bind_param("si", $password_hash, $id);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        
        if ($resultado) {
            return ['success' => true, 'message' => 'Contraseña actualizada correctamente'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar contraseña'];
        }
    }
    
    // Obtener todos los usuarios (solo admin)
    public static function obtenerTodos() {
        $conn = conexion::conectar();
        $sql = "SELECT id, nombre, apellido, email, telefono, dni, rol, activo, fecha_registro 
                FROM usuarios 
                ORDER BY fecha_registro DESC";
        $resultado = $conn->query($sql);
        $usuarios = [];
        while ($fila = $resultado->fetch_assoc()) {
            $usuarios[] = $fila;
        }
        $conn->close();
        return $usuarios;
    }
    
    // Cambiar estado de usuario (activar/desactivar)
    public static function cambiarEstado($id, $activo) {
        $conn = conexion::conectar();
        $stmt = $conn->prepare("UPDATE usuarios SET activo=? WHERE id=?");
        $stmt->bind_param("ii", $activo, $id);
        $resultado = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $resultado;
    }
}
?>