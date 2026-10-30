<?php
require_once 'Modelo/Dueno.php';

class ControladorDueno {
    
    public function listar() {
        $datos = Dueno::obtenerTodos();
        require("Vista/duenos/listar.php");
    }
    
    public function mostrarFormularioAgregar() {
        require("Vista/duenos/agregar.php");
    }
    
    public function guardarNuevo() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dni = $_POST['dni'];
            $nombre = $_POST['nombre'];
            $telefono = $_POST['telefono'];
            $direccion = $_POST['direccion'];
            
            Dueno::agregar($dni, $nombre, $telefono, $direccion);
            header("Location: index.php?entidad=dueno&accion=listar");
            exit;
        }
    }
    
    public function mostrarFormularioEditar() {
        $dni = $_GET['dni'];
        $dueno = Dueno::obtenerPorDni($dni);
        require("Vista/duenos/editar.php");
    }
    
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dni = $_POST['dni'];
            $nombre = $_POST['nombre'];
            $telefono = $_POST['telefono'];
            $direccion = $_POST['direccion'];
            
            Dueno::actualizar($dni, $nombre, $telefono, $direccion);
            header("Location: index.php?entidad=dueno&accion=listar");
            exit;
        }
    }
    
    public function eliminar() {
        $dni = $_GET['dni'];
        Dueno::eliminar($dni);
        header("Location: index.php?entidad=dueno&accion=listar");
        exit;
    }
}
?>