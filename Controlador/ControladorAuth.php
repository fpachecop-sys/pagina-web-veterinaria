<?php

declare(strict_types=1);

require_once __DIR__ . '/../Modelo/Usuario.php';

use App\Support\Csrf;
use App\Support\Session;

class ControladorAuth
{
    /**
     * Conserva los campos no sensibles del registro para mostrarlos tras una
     * validación fallida. Nunca guarda las contraseñas en la sesión.
     *
     * @return array<string, string>
     */
    private function obtenerCamposSegurosRegistro(): array
    {
        $campos = ['nombre', 'apellido', 'dni', 'telefono', 'email', 'direccion'];
        $datos = [];

        foreach ($campos as $campo) {
            $valor = $_POST[$campo] ?? '';
            $datos[$campo] = is_scalar($valor) ? (string) $valor : '';
        }

        return $datos;
    }

    public function mostrarLogin(): void
    {
        if (Usuario::estaAutenticado()) {
            if (Usuario::esAdministrador()) {
                header('Location: index.php?entidad=admin&accion=dashboard');
            } else {
                header('Location: index.php?entidad=usuario&accion=dashboard');
            }
            exit();
        }
        include __DIR__ . '/../Vista/auth/login.php';
    }

    public function mostrarRegistro(): void
    {
        if (Usuario::estaAutenticado()) {
            header('Location: index.php?entidad=usuario&accion=dashboard');
            exit();
        }
        include __DIR__ . '/../Vista/auth/registro.php';
    }

    public function procesarLogin(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=auth&accion=mostrarLogin');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido. Por favor intenta nuevamente.');
            header('Location: index.php?entidad=auth&accion=mostrarLogin');
            exit();
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $resultado = Usuario::login($email, $password);

        if ($resultado['exito']) {
            if (($resultado['rol'] ?? '') === 'administrador') {
                header('Location: index.php?entidad=admin&accion=dashboard');
            } else {
                header('Location: index.php?entidad=usuario&accion=dashboard');
            }
        } else {
            Session::setFlash('error', $resultado['mensaje']);
            header('Location: index.php?entidad=auth&accion=mostrarLogin');
        }
        exit();
    }

    public function procesarRegistro(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=auth&accion=mostrarRegistro');
            exit();
        }

        Session::start();
        $camposSeguros = $this->obtenerCamposSegurosRegistro();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::set('old_register', $camposSeguros);
            Session::setFlash('error', 'La sesión del formulario expiró. Intenta nuevamente.');
            header('Location: index.php?entidad=auth&accion=mostrarRegistro');
            exit();
        }

        $datos = [
            'nombre'             => trim((string) ($_POST['nombre'] ?? '')),
            'apellido'           => trim((string) ($_POST['apellido'] ?? '')),
            'email'              => trim((string) ($_POST['email'] ?? '')),
            'password'           => (string) ($_POST['password'] ?? ''),
            'confirmar_password' => (string) ($_POST['confirmar_password'] ?? $_POST['password_confirm'] ?? ''),
            'telefono'           => trim((string) ($_POST['telefono'] ?? '')),
            'direccion'          => trim((string) ($_POST['direccion'] ?? '')),
            'dni'                => trim((string) ($_POST['dni'] ?? '')),
            'rol'                => 'usuario',
        ];

        Session::set('old_register', $camposSeguros);
        $resultado = Usuario::registrar($datos);

        if ($resultado['exito']) {
            Session::remove('old_register');
            Session::setFlash('success', $resultado['mensaje']);
            header('Location: index.php?entidad=auth&accion=mostrarLogin');
        } else {
            Session::setFlash('error', $resultado['mensaje']);
            header('Location: index.php?entidad=auth&accion=mostrarRegistro');
        }
        exit();
    }

    public function logout(): void
    {
        Usuario::logout();
        header('Location: index.php?entidad=home&accion=ver');
        exit();
    }
}
