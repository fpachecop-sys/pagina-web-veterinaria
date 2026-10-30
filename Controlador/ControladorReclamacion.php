<?php
require_once 'Modelo/Reclamacion.php';
require_once 'Modelo/Usuario.php';

class ControladorReclamacion {
    
    // ADMIN: Listar reclamaciones
    public function listar() {
        if (!Usuario::esAdministrador()) {
            $_SESSION['error'] = 'No tienes permisos para esta sección.';
            header("Location: index.php?entidad=usuario&accion=dashboard");
            exit;
        }
        
        $datos = Reclamacion::obtenerTodos();
        require("Vista/reclamaciones/listar.php");
    }
    
    // PÚBLICO: Mostrar formulario
    public function mostrarFormulario() {
        // Cualquiera puede acceder
        require("Vista/reclamaciones/formulario.php");
    }
    
    // PÚBLICO: Guardar reclamación
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo_documento = $_POST['tipo_documento'];
            $numero_documento = $_POST['numero_documento'];
            $nombres = $_POST['nombres'];
            $apellidos = $_POST['apellidos'];
            $telefono = $_POST['telefono'];
            $email = $_POST['email'];
            $direccion = $_POST['direccion'];
            $tipo_reclamo = $_POST['tipo_reclamo'];
            $detalle = $_POST['detalle'];
            $pedido = $_POST['pedido'] ?? '';
            
            Reclamacion::agregar($tipo_documento, $numero_documento, $nombres, $apellidos,
                                $telefono, $email, $direccion, $tipo_reclamo, $detalle, $pedido);
            
            echo "<script>alert('Reclamación registrada correctamente. Número de registro: " . 
                 Reclamacion::obtenerUltimoId() . "');
                  window.location='index.php?entidad=reclamacion&accion=mostrarFormulario';</script>";
        }
    }
    
    // ADMIN: Cambiar estado
    public function cambiarEstado() {
        if (!Usuario::esAdministrador()) {
            $_SESSION['error'] = 'No tienes permisos para esta sección.';
            header("Location: index.php?entidad=usuario&accion=dashboard");
            exit;
        }
        
        $id = $_GET['id'];
        $estado = $_GET['estado'];
        Reclamacion::cambiarEstado($id, $estado);
        header("Location: index.php?entidad=reclamacion&accion=listar");
        exit;
    }
}
?>