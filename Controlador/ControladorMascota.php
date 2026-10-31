<?php

declare(strict_types=1);

require_once __DIR__ . '/../Modelo/Mascota.php';
require_once __DIR__ . '/../Modelo/Dueno.php';
require_once __DIR__ . '/../Modelo/Usuario.php';

use App\Support\Csrf;
use App\Support\Logger;
use App\Support\Session;
use App\Validators\CatalogoValidator;

class ControladorMascota
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
        $mascotas = Mascota::listarTodas();
        $datos = $mascotas;
        include __DIR__ . '/../Vista/mascotas/listar.php';
    }

    public function agregar(): void
    {
        $this->verificarAdmin();
        $duenos = Dueno::listarTodos();
        include __DIR__ . '/../Vista/mascotas/agregar.php';
    }

    public function mostrarFormularioAgregar(): void
    {
        $this->agregar();
    }

    public function guardarNuevo(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=mascota&accion=agregar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=mascota&accion=agregar');
            exit();
        }

        $error = CatalogoValidator::validateMascota($_POST);
        if ($error !== null) {
            Session::setFlash('error', $error);
            header('Location: index.php?entidad=mascota&accion=agregar');
            exit();
        }

        try {
            Mascota::guardar(
                trim((string) $_POST['nombre']),
                trim((string) $_POST['especie']),
                trim((string) $_POST['raza']),
                (int) $_POST['edad'],
                trim((string) $_POST['dni_dueno'])
            );
            Session::setFlash('exito', 'Mascota registrada exitosamente.');
            Session::setFlash('success', 'Mascota registrada exitosamente.');
        } catch (\Throwable $e) {
            Logger::error('Error guardando mascota', $e);
            Session::setFlash('error', 'Error al registrar la mascota. Verifique que el dueño exista.');
        }

        header('Location: index.php?entidad=mascota&accion=listar');
        exit();
    }

    public function editar(): void
    {
        $this->verificarAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        $mascota = Mascota::obtenerPorId($id);
        if ($mascota === null) {
            Session::setFlash('error', 'Mascota no encontrada.');
            header('Location: index.php?entidad=mascota&accion=listar');
            exit();
        }
        $m = $mascota;
        $duenos = Dueno::listarTodos();
        include __DIR__ . '/../Vista/mascotas/editar.php';
    }

    public function mostrarFormularioEditar(): void
    {
        $this->editar();
    }

    public function actualizar(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=mascota&accion=listar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=mascota&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? 0);
        $error = CatalogoValidator::validateMascota($_POST);
        if ($error !== null) {
            Session::setFlash('error', $error);
            header('Location: index.php?entidad=mascota&accion=editar&id=' . $id);
            exit();
        }

        try {
            Mascota::actualizar(
                $id,
                trim((string) $_POST['nombre']),
                trim((string) $_POST['especie']),
                trim((string) $_POST['raza']),
                (int) $_POST['edad'],
                trim((string) $_POST['dni_dueno'])
            );
            Session::setFlash('exito', 'Mascota actualizada correctamente.');
            Session::setFlash('success', 'Mascota actualizada correctamente.');
        } catch (\Throwable $e) {
            Logger::error('Error actualizando mascota', $e);
            Session::setFlash('error', 'Error al actualizar los datos de la mascota.');
        }

        header('Location: index.php?entidad=mascota&accion=listar');
        exit();
    }

    public function eliminar(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Session::setFlash('error', 'Método no permitido.');
            header('Location: index.php?entidad=mascota&accion=listar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=mascota&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            Session::setFlash('error', 'El identificador de la mascota no es válido.');
            header('Location: index.php?entidad=mascota&accion=listar');
            exit();
        }
        try {
            Mascota::eliminar($id);
            Session::setFlash('exito', 'Mascota desactivada preservando su historial clínico.');
            Session::setFlash('success', 'Mascota desactivada preservando su historial clínico.');
        } catch (\Throwable $e) {
            Logger::error('Error desactivando mascota', $e);
            Session::setFlash('error', 'No se pudo desactivar la mascota.');
        }

        header('Location: index.php?entidad=mascota&accion=listar');
        exit();
    }
}
