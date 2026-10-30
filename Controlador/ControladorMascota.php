<?php
require_once 'Modelo/Mascota.php';

class ControladorMascota {
    
    public function listar() {
        $datos = Mascota::obtenerTodos();
        require("Vista/mascotas/listar.php");
    }
    
    public function mostrarFormularioAgregar() {
        $duenos = Mascota::obtenerDuenos();
        require("Vista/mascotas/agregar.php");
    }
    
    public function guardarNuevo() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $especie = $_POST['especie'];
            $raza = $_POST['raza'];
            $edad = $_POST['edad'];
            $dni_dueno = $_POST['dni_dueno'];
            
            Mascota::agregar($nombre, $especie, $raza, $edad, $dni_dueno);
            header("Location: index.php?entidad=mascota&accion=listar");
            exit;
        }
    }
    
    public function mostrarFormularioEditar() {
        $id = $_GET['id'];
        $mascota = Mascota::obtenerPorId($id);
        $duenos = Mascota::obtenerDuenos();
        require("Vista/mascotas/editar.php");
    }
    
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $nombre = $_POST['nombre'];
            $especie = $_POST['especie'];
            $raza = $_POST['raza'];
            $edad = $_POST['edad'];
            $dni_dueno = $_POST['dni_dueno'];
            
            Mascota::actualizar($id, $nombre, $especie, $raza, $edad, $dni_dueno);
            header("Location: index.php?entidad=mascota&accion=listar");
            exit;
        }
    }
    
    public function eliminar() {
        $id = $_GET['id'];
        Mascota::eliminar($id);
        header("Location: index.php?entidad=mascota&accion=listar");
        exit;
    }
}
?>