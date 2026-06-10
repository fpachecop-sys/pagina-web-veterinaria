<?php
class conexion {
    public static function conectar() {
        $host = "localhost";
        $usuario = "root";
        $contraseña = "";
        $base_datos = "veterinaria_x";
        
        $conn = new mysqli($host, $usuario, $contraseña, $base_datos);
        
        if ($conn->connect_error) {
            die("❌ Error de conexión: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8");
        return $conn;
    }
}
?>