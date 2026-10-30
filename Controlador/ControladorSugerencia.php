<?php
require_once 'Modelo/Sugerencia.php';
require_once 'Modelo/Usuario.php';

class ControladorSugerencia {
    
    // ADMIN: Listar sugerencias
    public function listar() {
        if (!Usuario::esAdministrador()) {
            $_SESSION['error'] = 'No tienes permisos para esta sección.';
            header("Location: index.php?entidad=usuario&accion=dashboard");
            exit;
        }
        
        $datos = Sugerencia::obtenerTodos();
        require("Vista/sugerencias/listar.php");
    }
    
    // PÚBLICO: Mostrar formulario
    public function mostrarFormulario() {
        // Cualquiera puede acceder
        require("Vista/sugerencias/formulario.php");
    }
    
    // PÚBLICO: Guardar sugerencia
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $email = $_POST['email'];
            $telefono = $_POST['telefono'] ?? '';
            $categoria = $_POST['categoria'];
            $sugerencia = $_POST['sugerencia'];
            
            Sugerencia::agregar($nombre, $email, $telefono, $categoria, $sugerencia);
            
            echo "<script>alert('¡Gracias por tu sugerencia! La hemos recibido correctamente.');
                  window.location='index.php?entidad=sugerencia&accion=mostrarFormulario';</script>";
        }
    }
    
    // ADMIN: Eliminar
    public function eliminar() {
        if (!Usuario::esAdministrador()) {
            $_SESSION['error'] = 'No tienes permisos para esta sección.';
            header("Location: index.php?entidad=usuario&accion=dashboard");
            exit;
        }
        
        $id = $_GET['id'];
        Sugerencia::eliminar($id);
        header("Location: index.php?entidad=sugerencia&accion=listar");
        exit;
    }
}
?>