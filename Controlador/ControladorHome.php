<?php
class ControladorHome {
    
    public function ver() {
        // Si el usuario está logueado, redirigir según su rol
        if (Usuario::estaLogueado()) {
            if (Usuario::esAdministrador()) {
                header("Location: index.php?entidad=admin&accion=dashboard");
            } else {
                header("Location: index.php?entidad=usuario&accion=dashboard");
            }
            exit;
        }
        
        // Si no está logueado, mostrar página de inicio
        require("Vista/home.php");
    }
}
?>