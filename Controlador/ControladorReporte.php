<?php

declare(strict_types=1);

require_once __DIR__ . '/../Modelo/Reporte.php';
require_once __DIR__ . '/../Modelo/Usuario.php';

class ControladorReporte
{
    public function ver(): void
    {
        if (!Usuario::esAdministrador()) {
            header('Location: index.php?entidad=home&accion=ver');
            exit();
        }

        $estadisticas = Reporte::obtenerEstadisticas();
        $stats = $estadisticas;
        $citasPorEstado = Reporte::obtenerCitasPorEstado();
        $mascotasPorEspecie = Reporte::obtenerMascotasPorEspecie();
        $veterinariosPopulares = Reporte::obtenerVeterinariosPopulares();
        $citasPorMes = Reporte::obtenerCitasPorMes();

        include __DIR__ . '/../Vista/reportes/ver.php';
    }
}