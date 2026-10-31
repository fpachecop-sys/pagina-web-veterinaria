<?php

declare(strict_types=1);

require_once __DIR__ . '/../Modelo/Sugerencia.php';
require_once __DIR__ . '/../Modelo/Usuario.php';

use App\Support\Csrf;
use App\Support\Logger;
use App\Support\Session;
use App\Validators\InteraccionValidator;

class ControladorSugerencia
{
    public function crear(): void
    {
        include __DIR__ . '/../Vista/sugerencias/formulario.php';
    }

    public function mostrarFormulario(): void
    {
        $this->crear();
    }

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=sugerencia&accion=mostrarFormulario');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=sugerencia&accion=mostrarFormulario');
            exit();
        }

        $error = InteraccionValidator::validateSugerencia($_POST);
        if ($error !== null) {
            Session::setFlash('error', $error);
            header('Location: index.php?entidad=sugerencia&accion=mostrarFormulario');
            exit();
        }

        try {
            $nombre = trim((string) ($_POST['nombre'] ?? $_POST['nombre_cliente'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? $_POST['email_cliente'] ?? ''));
            $telefono = trim((string) ($_POST['telefono'] ?? ''));
            $categoria = trim((string) ($_POST['categoria'] ?? 'General'));
            $mensaje = trim((string) ($_POST['sugerencia'] ?? $_POST['mensaje'] ?? ''));

            Sugerencia::guardar($nombre, $email, $mensaje, $telefono, $categoria);
            Session::setFlash('exito', '¡Muchas gracias por su sugerencia! La tomaremos en cuenta para mejorar.');
            Session::setFlash('success', '¡Muchas gracias por su sugerencia! La tomaremos en cuenta para mejorar.');
        } catch (\Throwable $e) {
            Logger::error('Error guardando sugerencia', $e);
            Session::setFlash('error', 'No se pudo enviar la sugerencia.');
        }

        header('Location: index.php?entidad=sugerencia&accion=mostrarFormulario');
        exit();
    }

    public function listar(): void
    {
        if (!Usuario::esAdministrador()) {
            header('Location: index.php?entidad=home&accion=ver');
            exit();
        }
        $sugerencias = Sugerencia::listarTodas();
        $datos = $sugerencias;
        include __DIR__ . '/../Vista/sugerencias/listar.php';
    }

    public function eliminar(): void
    {
        if (!Usuario::esAdministrador()) {
            header('Location: index.php?entidad=home&accion=ver');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Session::setFlash('error', 'Método no permitido.');
            header('Location: index.php?entidad=sugerencia&accion=listar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=sugerencia&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            Session::setFlash('error', 'El identificador de la sugerencia no es válido.');
            header('Location: index.php?entidad=sugerencia&accion=listar');
            exit();
        }
        if (Sugerencia::eliminar($id)) {
            Session::setFlash('exito', 'Sugerencia eliminada.');
            Session::setFlash('success', 'Sugerencia eliminada.');
        } else {
            Session::setFlash('error', 'No se pudo eliminar la sugerencia.');
        }

        header('Location: index.php?entidad=sugerencia&accion=listar');
        exit();
    }
}
