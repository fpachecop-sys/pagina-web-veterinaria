<?php
require_once 'Modelo/Veterinario.php';

class ControladorVeterinario {
    
    public function listar() {
        $datos = Veterinario::obtenerTodos();
        require("Vista/veterinarios/listar.php");
    }
    
    public function mostrarFormularioAgregar() {
        require("Vista/veterinarios/agregar.php");
    }
    
    public function guardarNuevo() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $especialidad = $_POST['especialidad'];
            $correo = $_POST['correo'];
            $telefono = $_POST['telefono'];
            
            Veterinario::agregar($nombre, $especialidad, $correo, $telefono);
            header("Location: index.php?entidad=veterinario&accion=listar");
            exit;
        }
    }
    
    public function mostrarFormularioEditar() {
        $id = $_GET['id'];
        $veterinario = Veterinario::obtenerPorId($id);
        require("Vista/veterinarios/editar.php");
    }
    
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $nombre = $_POST['nombre'];
            $especialidad = $_POST['especialidad'];
            $correo = $_POST['correo'];
            $telefono = $_POST['telefono'];
            
            Veterinario::actualizar($id, $nombre, $especialidad, $correo, $telefono);
            header("Location: index.php?entidad=veterinario&accion=listar");
            exit;
        }
    }
    
    public function eliminar() {
        $id = $_GET['id'];
        Veterinario::eliminar($id);
        header("Location: index.php?entidad=veterinario&accion=listar");
        exit;
    }
}
?>