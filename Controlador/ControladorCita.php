<?php

declare(strict_types=1);

require_once __DIR__ . '/../Modelo/Cita.php';
require_once __DIR__ . '/../Modelo/Mascota.php';
require_once __DIR__ . '/../Modelo/Veterinario.php';
require_once __DIR__ . '/../Modelo/Usuario.php';

use App\Services\CitaService;
use App\Support\Csrf;
use App\Support\Session;

class ControladorCita
{
    private function verificarAdmin(): void
    {
        if (!Usuario::esAdministrador()) {
            header('Location: index.php?entidad=home&accion=ver');
            exit();
        }
    }

    public function listar(): void
    {
        $this->verificarAdmin();
        $citas = Cita::listarTodas();
        include __DIR__ . '/../Vista/citas/listar.php';
    }

    public function agregar(): void
    {
        $this->verificarAdmin();
        $mascotas = Mascota::listarTodas();
        $veterinarios = Veterinario::listarTodos();
        include __DIR__ . '/../Vista/citas/agregar.php';
    }

    public function guardarNuevo(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=cita&accion=agregar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=cita&accion=agregar');
            exit();
        }

        $res = (new CitaService())->agendarCita($_POST);
        if ($res['exito']) {
            Session::setFlash('exito', $res['mensaje']);
            header('Location: index.php?entidad=cita&accion=listar');
        } else {
            Session::setFlash('error', $res['mensaje']);
            header('Location: index.php?entidad=cita&accion=agregar');
        }
        exit();
    }

    public function editar(): void
    {
        $this->verificarAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        $cita = Cita::obtenerPorId($id);
        if ($cita === null) {
            Session::setFlash('error', 'Cita no encontrada.');
            header('Location: index.php?entidad=cita&accion=listar');
            exit();
        }
        $mascotas = Mascota::listarTodas();
        $veterinarios = Veterinario::listarTodos();
        include __DIR__ . '/../Vista/citas/editar.php';
    }

    public function actualizar(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=cita&accion=listar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=cita&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? 0);
        $res = (new CitaService())->actualizarCita($id, $_POST);
        if ($res['exito']) {
            Session::setFlash('exito', $res['mensaje']);
            header('Location: index.php?entidad=cita&accion=listar');
        } else {
            Session::setFlash('error', $res['mensaje']);
            header('Location: index.php?entidad=cita&accion=editar&id=' . $id);
        }
        exit();
    }

    public function cambiarEstado(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Session::setFlash('error', 'Método no permitido.');
            header('Location: index.php?entidad=cita&accion=listar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=cita&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? 0);
        $estado = trim((string) ($_POST['estado'] ?? ''));

        $res = (new CitaService())->cambiarEstado($id, $estado);
        if ($res['exito']) {
            Session::setFlash('exito', $res['mensaje']);
        } else {
            Session::setFlash('error', $res['mensaje']);
        }

        header('Location: index.php?entidad=cita&accion=listar');
        exit();
    }

    public function eliminar(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Session::setFlash('error', 'Método no permitido.');
            header('Location: index.php?entidad=cita&accion=listar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=cita&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? 0);
        if (Cita::eliminar($id)) {
            Session::setFlash('exito', 'Cita eliminada correctamente.');
        } else {
            Session::setFlash('error', 'No se pudo eliminar la cita.');
        }

        header('Location: index.php?entidad=cita&accion=listar');
        exit();
    }
}