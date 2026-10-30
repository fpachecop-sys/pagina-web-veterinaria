<?php
require_once 'Modelo/Usuario.php';

class ControladorAuth {
    
    // Mostrar página de login
    public function mostrarLogin() {
        // Si ya está logueado, redirigir según rol
        if (Usuario::estaLogueado()) {
            if (Usuario::esAdministrador()) {
                header("Location: index.php?entidad=admin&accion=dashboard");
            } else {
                header("Location: index.php?entidad=usuario&accion=dashboard");
            }
            exit;
        }
        require("Vista/auth/login.php");
    }
    
    // Procesar login
    public function procesarLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'Por favor complete todos los campos';
                header("Location: index.php?entidad=auth&accion=mostrarLogin");
                exit;
            }
            
            $resultado = Usuario::login($email, $password);
            
            if ($resultado['success']) {
                // Redirigir según rol
                if ($resultado['rol'] === 'administrador') {
                    header("Location: index.php?entidad=admin&accion=dashboard");
                } else {
                    header("Location: index.php?entidad=usuario&accion=dashboard");
                }
                exit;
            } else {
                session_start();
                $_SESSION['error'] = $resultado['message'];
                header("Location: index.php?entidad=auth&accion=mostrarLogin");
                exit;
            }
        }
    }
    
    // Mostrar formulario de registro
    public function mostrarRegistro() {
        // Si ya está logueado, redirigir
        if (Usuario::estaLogueado()) {
            if (Usuario::esAdministrador()) {
                header("Location: index.php?entidad=admin&accion=dashboard");
            } else {
                header("Location: index.php?entidad=usuario&accion=dashboard");
            }
            exit;
        }
        require("Vista/auth/registro.php");
    }
    
    // Procesar registro
    public function procesarRegistro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $apellido = $_POST['apellido'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $direccion = $_POST['direccion'] ?? '';
            $dni = $_POST['dni'] ?? '';
            
            // Validaciones
            if (empty($nombre) || empty($apellido) || empty($email) || empty($password) || empty($dni)) {
                session_start();
                $_SESSION['error'] = 'Por favor complete todos los campos obligatorios';
                header("Location: index.php?entidad=auth&accion=mostrarRegistro");
                exit;
            }
            
            if ($password !== $password_confirm) {
                session_start();
                $_SESSION['error'] = 'Las contraseñas no coinciden';
                header("Location: index.php?entidad=auth&accion=mostrarRegistro");
                exit;
            }
            
            if (strlen($password) < 6) {
                session_start();
                $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres';
                header("Location: index.php?entidad=auth&accion=mostrarRegistro");
                exit;
            }
            
            if (strlen($dni) !== 8 || !ctype_digit($dni)) {
                session_start();
                $_SESSION['error'] = 'El DNI debe tener 8 dígitos';
                header("Location: index.php?entidad=auth&accion=mostrarRegistro");
                exit;
            }
            
            // Registrar usuario
            $resultado = Usuario::registrar($nombre, $apellido, $email, $password, $telefono, $direccion, $dni);
            
            session_start();
            if ($resultado['success']) {
                $_SESSION['success'] = 'Registro exitoso. Ya puede iniciar sesión';
                header("Location: index.php?entidad=auth&accion=mostrarLogin");
            } else {
                $_SESSION['error'] = $resultado['message'];
                header("Location: index.php?entidad=auth&accion=mostrarRegistro");
            }
            exit;
        }
    }
    
    // Cerrar sesión
    public function logout() {
        Usuario::logout();
        header("Location: index.php");
        exit;
    }
}
?>