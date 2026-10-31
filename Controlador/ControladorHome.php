<?php

declare(strict_types=1);

require_once __DIR__ . '/../Modelo/Usuario.php';

class ControladorHome
{
    public function ver(): void
    {
        $estaAutenticado = Usuario::estaAutenticado();
        $esAdmin = Usuario::esAdministrador();
        $usuario = Usuario::obtenerUsuarioActual();

        include __DIR__ . '/../Vista/home.php';
    }
}