<?php

declare(strict_types=1);

require_once __DIR__ . '/../Servicios/conexion.php';

use App\Repositories\UsuarioRepository;
use App\Services\AuthService;
use App\Support\Database;
use App\Support\Session;

class Usuario
{
    public static function registrar(array $datos): array
    {
        return (new AuthService())->registrar($datos);
    }

    public static function login(string $email, string $password): array
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        return (new AuthService())->login($email, $password, (string) $ip);
    }

    public static function logout(): array
    {
        Session::destroy();
        return ['exito' => true, 'mensaje' => 'Sesión cerrada exitosamente'];
    }

    public static function estaAutenticado(): bool
    {
        Session::start();
        return isset($_SESSION['usuario_id']);
    }

    public static function esAdministrador(): bool
    {
        Session::start();
        return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'administrador';
    }

    public static function obtenerUsuarioActual(): ?array
    {
        Session::start();
        if (!isset($_SESSION['usuario_id'])) {
            return null;
        }
        return [
            'id'       => $_SESSION['usuario_id'],
            'nombre'   => $_SESSION['usuario_nombre'] ?? '',
            'apellido' => $_SESSION['usuario_apellido'] ?? '',
            'email'    => $_SESSION['usuario_email'] ?? '',
            'rol'      => $_SESSION['usuario_rol'] ?? 'usuario',
            'dni'      => $_SESSION['usuario_dni'] ?? '',
        ];
    }

    public static function obtenerPorId(int|string $id): ?array
    {
        $repo = new UsuarioRepository(Database::getConnection());
        return $repo->findById((int) $id);
    }

    public static function actualizarPerfil(int|string $id, array $datos): bool
    {
        $res = (new AuthService())->actualizarPerfil((int) $id, $datos);
        return $res['exito'];
    }

    public static function cambiarPassword(int|string $id, string $passwordActual, string $passwordNueva): array
    {
        return (new AuthService())->cambiarPassword((int) $id, $passwordActual, $passwordNueva);
    }

    public static function listarTodos(): array
    {
        $conn = Database::getConnection();
        $sql = "SELECT id, nombre, apellido, email, telefono, dni, rol, activo, fecha_registro 
                FROM usuarios ORDER BY fecha_registro DESC";
        $resultado = $conn->query($sql);
        $usuarios = [];
        while ($fila = $resultado->fetch_assoc()) {
            $usuarios[] = $fila;
        }
        return $usuarios;
    }

    public static function cambiarEstado(int|string $id, int|string $activo): bool
    {
        $conn = Database::getConnection();
        $idInt = (int) $id;
        $activoInt = ((int) $activo === 1) ? 1 : 0;
        $stmt = $conn->prepare("UPDATE usuarios SET activo = ? WHERE id = ?");
        $stmt->bind_param('ii', $activoInt, $idInt);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
