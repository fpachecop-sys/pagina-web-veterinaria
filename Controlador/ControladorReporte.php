<?php
require_once 'Modelo/Reporte.php';

class ControladorReporte {
    
    public function ver() {
        $estadisticas = Reporte::obtenerEstadisticas();
        $mascotasPorEspecie = Reporte::obtenerMascotasPorEspecie();
        $veterinariosPopulares = Reporte::obtenerVeterinariosPopulares();
        $citasPorMes = Reporte::obtenerCitasPorMes();
        
        require("Vista/reportes/ver.php");
    }
}
?>