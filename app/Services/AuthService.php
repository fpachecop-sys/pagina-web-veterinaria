<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\LoginIntentoRepository;
use App\Repositories\UsuarioRepository;
use App\Support\Database;
use App\Support\Logger;
use App\Support\Session;
use App\Validators\AuthValidator;
use Throwable;

final class AuthService
{
    private UsuarioRepository $usuarios;
    private LoginIntentoRepository $intentos;

    public function __construct()
    {
        $db = Database::getConnection();
        $this->usuarios = new UsuarioRepository($db);
        $this->intentos = new LoginIntentoRepository($db);
    }

    /**
     * @return array{exito: bool, mensaje: string, rol?: string}
     */
    public function login(string $email, string $password, string $ip): array
    {
        $emailNorm = strtolower(trim($email));
        if ($emailNorm === '' || $password === '') {
            return ['exito' => false, 'mensaje' => 'Por favor completa todos los campos.'];
        }

        if ($this->intentos->isLocked($emailNorm, $ip)) {
            return [
                'exito' => false,
                'mensaje' => 'Demasiados intentos fallidos. Por seguridad, espera 15 minutos antes de volver a intentar.',
            ];
        }

        $usuario = $this->usuarios->findByEmail($emailNorm);

        if ($usuario === null || (int) $usuario['activo'] !== 1 || !password_verify($password, (string) $usuario['password'])) {
            $this->intentos->recordFailedAttempt($emailNorm, $ip);
            return ['exito' => false, 'mensaje' => 'Credenciales incorrectas o cuenta inactiva.'];
        }

        if (password_needs_rehash((string) $usuario['password'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $this->usuarios->updatePassword((int) $usuario['id'], $newHash);
        }

        $this->intentos->clearAttempts($emailNorm, $ip);

        Session::start();
        Session::regenerate();
        Session::set('usuario_id', (int) $usuario['id']);
        Session::set('usuario_nombre', (string) $usuario['nombre']);
        Session::set('usuario_apellido', (string) $usuario['apellido']);
        Session::set('usuario_email', (string) $usuario['email']);
        Session::set('usuario_rol', (string) $usuario['rol']);
        Session::set('usuario_dni', (string) ($usuario['dni'] ?? ''));

        return [
            'exito'   => true,
            'rol'     => (string) $usuario['rol'],
            'mensaje' => 'Bienvenido ' . $usuario['nombre'],
        ];
    }

    /**
     * @param array<string, mixed> $datos
     * @return array{exito: bool, mensaje: string, id?: int}
     */
    public function registrar(array $datos): array
    {
        $errorValidacion = AuthValidator::validateRegistro($datos);
        if ($errorValidacion !== null) {
            return ['exito' => false, 'mensaje' => $errorValidacion];
        }

        $nombre = trim((string) $datos['nombre']);
        $apellido = trim((string) $datos['apellido']);
        $email = strtolower(trim((string) $datos['email']));
        $dni = trim((string) $datos['dni']);
        $telefono = trim((string) $datos['telefono']);
        $direccion = trim((string) $datos['direccion']);
        $password = (string) $datos['password'];
        $rol = isset($datos['rol']) && $datos['rol'] === 'administrador' ? 'administrador' : 'usuario';

        if ($this->usuarios->existsEmail($email)) {
            return ['exito' => false, 'mensaje' => 'El correo electrónico ya está registrado.'];
        }

        if ($this->usuarios->existsDniInUsuarios($dni) || $this->usuarios->existsDniInDuenos($dni)) {
            return [
                'exito'   => false,
                'mensaje' => 'El DNI ingresado ya se encuentra registrado en el sistema. Si ya eres cliente, contacta a la clínica.',
            ];
        }

        $db = Database::getConnection();
        $db->begin_transaction();

        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $idUsuario = $this->usuarios->createUsuario($nombre, $apellido, $email, $hash, $telefono, $direccion, $dni, $rol);

            if ($rol === 'usuario') {
                $nombreCompleto = $nombre . ' ' . $apellido;
                $this->usuarios->createDuenoVinculado($dni, $idUsuario, $nombreCompleto, $telefono, $direccion);
            }

            $db->commit();
            return ['exito' => true, 'mensaje' => 'Usuario registrado exitosamente', 'id' => $idUsuario];
        } catch (Throwable $e) {
            $db->rollback();
            Logger::error('Error en transaccion de registro de usuario', $e);
            return ['exito' => false, 'mensaje' => 'No se pudo completar el registro. Inténtalo de nuevo.'];
        }
    }

    /**
     * @param array<string, mixed> $datos
     * @return array{exito: bool, mensaje: string}
     */
    public function actualizarPerfil(int $idUsuarioSesion, array $datos): array
    {
        $error = AuthValidator::validatePerfil($datos);
        if ($error !== null) {
            return ['exito' => false, 'mensaje' => $error];
        }

        $nombre = trim((string) $datos['nombre']);
        $apellido = trim((string) $datos['apellido']);
        $dni = trim((string) $datos['dni']);
        $telefono = trim((string) $datos['telefono']);
        $direccion = trim((string) $datos['direccion']);

        if ($this->usuarios->existsDniInUsuarios($dni, $idUsuarioSesion) || $this->usuarios->existsDniInDuenos($dni, $idUsuarioSesion)) {
            return ['exito' => false, 'mensaje' => 'El DNI ingresado ya pertenece a otro usuario registrado.'];
        }

        $db = Database::getConnection();
        $db->begin_transaction();

        try {
            $this->usuarios->updatePerfilUsuario($idUsuarioSesion, $nombre, $apellido, $telefono, $direccion, $dni);
            $nombreCompleto = $nombre . ' ' . $apellido;
            $this->usuarios->updateDuenoVinculado($idUsuarioSesion, $dni, $nombreCompleto, $telefono, $direccion);

            $db->commit();

            Session::start();
            Session::set('usuario_nombre', $nombre);
            Session::set('usuario_apellido', $apellido);
            Session::set('usuario_dni', $dni);

            return ['exito' => true, 'mensaje' => 'Perfil actualizado correctamente.'];
        } catch (Throwable $e) {
            $db->rollback();
            Logger::error('Error al actualizar perfil de usuario ID ' . $idUsuarioSesion, $e);
            return ['exito' => false, 'mensaje' => 'Error al actualizar el perfil.'];
        }
    }

    /**
     * @return array{exito: bool, mensaje: string}
     */
    public function cambiarPassword(int $idUsuarioSesion, string $actual, string $nueva): array
    {
        $error = AuthValidator::validatePasswordStrength($nueva);
        if ($error !== null) {
            return ['exito' => false, 'mensaje' => $error];
        }

        $usuario = $this->usuarios->findById($idUsuarioSesion);
        if ($usuario === null || !password_verify($actual, (string) $usuario['password'])) {
            return ['exito' => false, 'mensaje' => 'La contraseña actual es incorrecta.'];
        }

        $nuevoHash = password_hash($nueva, PASSWORD_DEFAULT);
        $this->usuarios->updatePassword($idUsuarioSesion, $nuevoHash);

        return ['exito' => true, 'mensaje' => 'Contraseña actualizada exitosamente.'];
    }
}
