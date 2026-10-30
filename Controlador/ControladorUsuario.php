<?php

declare(strict_types=1);

require_once __DIR__ . '/../Modelo/Usuario.php';
require_once __DIR__ . '/../Modelo/Mascota.php';
require_once __DIR__ . '/../Modelo/Cita.php';
require_once __DIR__ . '/../Modelo/Dueno.php';

use App\Services\AuthService;
use App\Support\Csrf;
use App\Support\Database;
use App\Support\Session;

class ControladorUsuario
{
    public function __construct()
    {
        if (!Usuario::estaAutenticado()) {
            header('Location: index.php?entidad=auth&accion=mostrarLogin');
            exit();
        }
    }

    public function dashboard(): void
    {
        $usuario = Usuario::obtenerUsuarioActual();
        $dni = (string) ($usuario['dni'] ?? '');
        $misMascotas = $this->obtenerMascotasPorDni($dni);
        $misCitas = $this->obtenerCitasPorDni($dni);
        $estadisticas = $this->obtenerEstadisticasUsuario($dni);

        include __DIR__ . '/../Vista/usuario/dashboard.php';
    }

    public function perfil(): void
    {
        $usuarioActual = Usuario::obtenerUsuarioActual();
        $usuario = Usuario::obtenerPorId((int) $usuarioActual['id']);
        include __DIR__ . '/../Vista/usuario/perfil.php';
    }

    public function editarPerfil(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=usuario&accion=perfil');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=usuario&accion=perfil');
            exit();
        }

        // SEGURIDAD (S2 - Prevención de IDOR): El ID jamás se toma de $_POST['id'], sino de la sesión
        $usuarioActual = Usuario::obtenerUsuarioActual();
        $idSesion = (int) $usuarioActual['id'];

        $datos = [
            'nombre'    => $_POST['nombre'] ?? '',
            'apellido'  => $_POST['apellido'] ?? '',
            'telefono'  => $_POST['telefono'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'dni'       => $_POST['dni'] ?? '',
        ];

        $resultado = (new AuthService())->actualizarPerfil($idSesion, $datos);

        if ($resultado['exito']) {
            Session::setFlash('exito', $resultado['mensaje']);
        } else {
            Session::setFlash('error', $resultado['mensaje']);
        }

        header('Location: index.php?entidad=usuario&accion=perfil');
        exit();
    }

    public function cambiarPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=usuario&accion=perfil');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=usuario&accion=perfil');
            exit();
        }

        $usuarioActual = Usuario::obtenerUsuarioActual();
        $idSesion = (int) $usuarioActual['id'];

        $passwordActual = (string) ($_POST['password_actual'] ?? '');
        $passwordNueva = (string) ($_POST['password_nueva'] ?? '');
        $confirmarPassword = (string) ($_POST['confirmar_password'] ?? '');

        if ($passwordNueva !== $confirmarPassword) {
            Session::setFlash('error', 'Las contraseñas nuevas no coinciden.');
            header('Location: index.php?entidad=usuario&accion=perfil');
            exit();
        }

        $resultado = Usuario::cambiarPassword($idSesion, $passwordActual, $passwordNueva);

        if ($resultado['exito']) {
            Session::setFlash('exito', $resultado['mensaje']);
        } else {
            Session::setFlash('error', $resultado['mensaje']);
        }

        header('Location: index.php?entidad=usuario&accion=perfil');
        exit();
    }

    public function misMascotas(): void
    {
        $usuario = Usuario::obtenerUsuarioActual();
        $mascotas = $this->obtenerMascotasPorDni((string) ($usuario['dni'] ?? ''));
        include __DIR__ . '/../Vista/usuario/mis_mascotas.php';
    }

    public function misCitas(): void
    {
        $usuario = Usuario::obtenerUsuarioActual();
        $citas = $this->obtenerCitasPorDni((string) ($usuario['dni'] ?? ''));
        include __DIR__ . '/../Vista/usuario/mis_citas.php';
    }

    private function obtenerMascotasPorDni(string $dni): array
    {
        if ($dni === '') {
            return [];
        }
        $conn = Database::getConnection();
        $sql = "SELECT id, nombre, especie, raza, edad, dni_dueno, fecha_registro FROM mascotas WHERE dni_dueno = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $dni);
        $stmt->execute();
        $res = $stmt->get_result();
        $mascotas = [];
        while ($fila = $res->fetch_assoc()) {
            $mascotas[] = $fila;
        }
        $stmt->close();
        return $mascotas;
    }

    private function obtenerCitasPorDni(string $dni): array
    {
        if ($dni === '') {
            return [];
        }
        $conn = Database::getConnection();
        $sql = "SELECT c.id, c.fecha, c.hora, c.motivo, c.estado, 
                       m.nombre AS nombre_mascota, m.especie, 
                       v.nombre AS nombre_veterinario, v.especialidad 
                FROM citas c 
                INNER JOIN mascotas m ON c.id_mascota = m.id 
                INNER JOIN veterinarios v ON c.id_veterinario = v.id 
                WHERE m.dni_dueno = ? 
                ORDER BY c.fecha DESC, c.hora DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $dni);
        $stmt->execute();
        $res = $stmt->get_result();
        $citas = [];
        while ($fila = $res->fetch_assoc()) {
            $citas[] = $fila;
        }
        $stmt->close();
        return $citas;
    }

    private function obtenerEstadisticasUsuario(string $dni): array
    {
        if ($dni === '') {
            return ['total_mascotas' => 0, 'citas_pendientes' => 0, 'citas_atendidas' => 0, 'proxima_cita' => null];
        }
        $conn = Database::getConnection();
        $hoyLima = date('Y-m-d');
        $stats = [];

        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM mascotas WHERE dni_dueno = ?");
        $stmt->bind_param('s', $dni);
        $stmt->execute();
        $stats['total_mascotas'] = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
        $stmt->close();

        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM citas c INNER JOIN mascotas m ON c.id_mascota = m.id WHERE m.dni_dueno = ? AND c.estado = 'Pendiente'");
        $stmt->bind_param('s', $dni);
        $stmt->execute();
        $stats['citas_pendientes'] = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
        $stmt->close();

        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM citas c INNER JOIN mascotas m ON c.id_mascota = m.id WHERE m.dni_dueno = ? AND c.estado = 'Atendida'");
        $stmt->bind_param('s', $dni);
        $stmt->execute();
        $stats['citas_atendidas'] = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
        $stmt->close();

        $stmt = $conn->prepare("SELECT c.fecha, c.hora, m.nombre AS nombre_mascota, v.nombre AS nombre_veterinario 
                                FROM citas c 
                                INNER JOIN mascotas m ON c.id_mascota = m.id 
                                INNER JOIN veterinarios v ON c.id_veterinario = v.id 
                                WHERE m.dni_dueno = ? AND c.estado = 'Pendiente' AND c.fecha >= ? 
                                ORDER BY c.fecha ASC, c.hora ASC LIMIT 1");
        $stmt->bind_param('ss', $dni, $hoyLima);
        $stmt->execute();
        $stats['proxima_cita'] = $stmt->get_result()->fetch_assoc() ?: null;
        $stmt->close();

        return $stats;
    }
}
