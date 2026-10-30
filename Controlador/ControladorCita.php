<?php
require_once 'Modelo/Cita.php';

class ControladorCita {
    
    public function listar() {
        $datos = Cita::obtenerTodos();
        require("Vista/citas/listar.php");
    }
    
    public function mostrarFormularioAgregar() {
        $mascotas = Cita::obtenerMascotas();
        $veterinarios = Cita::obtenerVeterinarios();
        require("Vista/citas/agregar.php");
    }
    
    public function guardarNuevo() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fecha = $_POST['fecha'];
            $hora = $_POST['hora'];
            $id_mascota = $_POST['id_mascota'];
            $id_veterinario = $_POST['id_veterinario'];
            $motivo = $_POST['motivo'];
            
            Cita::agregar($fecha, $hora, $id_mascota, $id_veterinario, $motivo);
            header("Location: index.php?entidad=cita&accion=listar");
            exit;
        }
    }
    
    public function mostrarFormularioEditar() {
        $id = $_GET['id'];
        $cita = Cita::obtenerPorId($id);
        $mascotas = Cita::obtenerMascotas();
        $veterinarios = Cita::obtenerVeterinarios();
        require("Vista/citas/editar.php");
    }
    
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $fecha = $_POST['fecha'];
            $hora = $_POST['hora'];
            $id_mascota = $_POST['id_mascota'];
            $id_veterinario = $_POST['id_veterinario'];
            $motivo = $_POST['motivo'];
            $estado = $_POST['estado'];
            
            Cita::actualizar($id, $fecha, $hora, $id_mascota, $id_veterinario, $motivo, $estado);
            header("Location: index.php?entidad=cita&accion=listar");
            exit;
        }
    }
    
    public function cambiarEstado() {
        $id = $_GET['id'];
        $estado = $_GET['estado'];
        Cita::cambiarEstado($id, $estado);
        header("Location: index.php?entidad=cita&accion=listar");
        exit;
    }
    
    public function eliminar() {
        $id = $_GET['id'];
        Cita::eliminar($id);
        header("Location: index.php?entidad=cita&accion=listar");
        exit;
    }
}
?>