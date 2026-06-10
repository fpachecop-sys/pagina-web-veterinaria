<?php
require_once 'Modelo/Video.php';
require_once 'Modelo/Usuario.php';

class ControladorVideo {
    
    // ADMIN: Listar todos (con opciones de editar/eliminar)
    public function listar() {
        // Solo administradores
        if (!Usuario::esAdministrador()) {
            $_SESSION['error'] = 'No tienes permisos para esta sección.';
            header("Location: index.php?entidad=usuario&accion=dashboard");
            exit;
        }
        
        $datos = Video::obtenerTodos();
        require("Vista/videos/listar.php");
    }
    
    // ADMIN: Mostrar formulario agregar
    public function mostrarFormularioAgregar() {
        if (!Usuario::esAdministrador()) {
            $_SESSION['error'] = 'No tienes permisos para esta sección.';
            header("Location: index.php?entidad=usuario&accion=dashboard");
            exit;
        }
        
        require("Vista/videos/agregar.php");
    }
    
    // ADMIN: Guardar nuevo
    public function guardarNuevo() {
        if (!Usuario::esAdministrador()) {
            $_SESSION['error'] = 'No tienes permisos para esta sección.';
            header("Location: index.php?entidad=usuario&accion=dashboard");
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = $_POST['titulo'];
            $descripcion = $_POST['descripcion'];
            $url_video = $_POST['url_video'];
            $categoria = $_POST['categoria'];
            
            Video::agregar($titulo, $descripcion, $url_video, $categoria);
            header("Location: index.php?entidad=video&accion=listar");
            exit;
        }
    }
    
    // ADMIN: Mostrar formulario editar
    public function mostrarFormularioEditar() {
        if (!Usuario::esAdministrador()) {
            $_SESSION['error'] = 'No tienes permisos para esta sección.';
            header("Location: index.php?entidad=usuario&accion=dashboard");
            exit;
        }
        
        $id = $_GET['id'];
        $video = Video::obtenerPorId($id);
        require("Vista/videos/editar.php");
    }
    
    // ADMIN: Actualizar
    public function actualizar() {
        if (!Usuario::esAdministrador()) {
            $_SESSION['error'] = 'No tienes permisos para esta sección.';
            header("Location: index.php?entidad=usuario&accion=dashboard");
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $titulo = $_POST['titulo'];
            $descripcion = $_POST['descripcion'];
            $url_video = $_POST['url_video'];
            $categoria = $_POST['categoria'];
            $activo = isset($_POST['activo']) ? 1 : 0;
            
            Video::actualizar($id, $titulo, $descripcion, $url_video, $categoria, $activo);
            header("Location: index.php?entidad=video&accion=listar");
            exit;
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
        Video::eliminar($id);
        header("Location: index.php?entidad=video&accion=listar");
        exit;
    }
    
    // PÚBLICO: Ver videos (usuarios y visitantes)
    public function verPublico() {
        // No requiere autenticación, cualquiera puede ver
        $datos = Video::obtenerTodos();
        require("Vista/videos/ver_publico.php");
    }
}
?>