<?php

declare(strict_types=1);

/**
 * Script de sincronización integral entre Vistas, Controladores, Modelos,
 * Servicios, Repositorios, Router y Esquema SQL (veterinaria_x).
 */

$base = __DIR__;

function writeCleanUtf8(string $relativePath, string $content): void
{
    $fullPath = __DIR__ . '/' . ltrim($relativePath, '/');
    $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
    file_put_contents($fullPath, $content);
    echo "[OK] Actualizado: $relativePath\n";
}

// 1. app/Support/Router.php
writeCleanUtf8('app/Support/Router.php', <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Support;

use ReflectionMethod;

final class Router
{
    /**
     * @var array<string, array{file: string, class: string}>
     */
    private const CONTROLLERS = [
        'home'        => ['file' => 'ControladorHome.php',        'class' => 'ControladorHome'],
        'auth'        => ['file' => 'ControladorAuth.php',        'class' => 'ControladorAuth'],
        'usuario'     => ['file' => 'ControladorUsuario.php',     'class' => 'ControladorUsuario'],
        'admin'       => ['file' => 'ControladorAdmin.php',       'class' => 'ControladorAdmin'],
        'dueno'       => ['file' => 'ControladorDueno.php',       'class' => 'ControladorDueno'],
        'mascota'     => ['file' => 'ControladorMascota.php',     'class' => 'ControladorMascota'],
        'cita'        => ['file' => 'ControladorCita.php',        'class' => 'ControladorCita'],
        'veterinario' => ['file' => 'ControladorVeterinario.php', 'class' => 'ControladorVeterinario'],
        'reporte'     => ['file' => 'ControladorReporte.php',     'class' => 'ControladorReporte'],
        'reclamacion' => ['file' => 'ControladorReclamacion.php', 'class' => 'ControladorReclamacion'],
        'sugerencia'  => ['file' => 'ControladorSugerencia.php',  'class' => 'ControladorSugerencia'],
        'video'       => ['file' => 'ControladorVideo.php',       'class' => 'ControladorVideo'],
        'ubicacion'   => ['file' => 'ControladorUbicacion.php',   'class' => 'ControladorUbicacion'],
    ];

    /**
     * @var array<string, list<string>>
     */
    private const PUBLIC_ROUTES = [
        'home'        => ['ver'],
        'auth'        => ['mostrarLogin', 'mostrarRegistro', 'procesarLogin', 'procesarRegistro', 'logout'],
        'reclamacion' => ['crear', 'mostrarFormulario', 'guardar'],
        'sugerencia'  => ['crear', 'mostrarFormulario', 'guardar'],
        'video'       => ['verPublico'],
        'ubicacion'   => ['ver'],
        'veterinario' => ['verPublico'],
    ];

    /**
     * @var array<string, list<string>>
     */
    private const ADMIN_ROUTES = [
        'admin'       => ['dashboard', 'usuarios', 'listarUsuarios', 'cambiarEstadoUsuario'],
        'dueno'       => ['listar', 'agregar', 'mostrarFormularioAgregar', 'guardarNuevo', 'editar', 'mostrarFormularioEditar', 'actualizar', 'eliminar'],
        'mascota'     => ['listar', 'agregar', 'mostrarFormularioAgregar', 'guardarNuevo', 'editar', 'mostrarFormularioEditar', 'actualizar', 'eliminar'],
        'cita'        => ['listar', 'agregar', 'mostrarFormularioAgregar', 'guardarNuevo', 'editar', 'mostrarFormularioEditar', 'actualizar', 'cambiarEstado', 'eliminar'],
        'veterinario' => ['listar', 'agregar', 'mostrarFormularioAgregar', 'guardarNuevo', 'editar', 'mostrarFormularioEditar', 'actualizar', 'eliminar'],
        'reporte'     => ['ver'],
        'reclamacion' => ['listar', 'cambiarEstado', 'eliminar'],
        'sugerencia'  => ['listar', 'eliminar'],
        'video'       => ['listar', 'agregar', 'mostrarFormularioAgregar', 'guardar', 'guardarNuevo', 'editar', 'mostrarFormularioEditar', 'actualizar', 'eliminar'],
    ];

    public static function sendSecurityHeaders(): void
    {
        if (!headers_sent()) {
            header('X-Frame-Options: SAMEORIGIN');
            header('X-Content-Type-Options: nosniff');
            header('Referrer-Policy: strict-origin-when-cross-origin');
        }
    }

    public static function isPublicRoute(string $entidad, string $accion): bool
    {
        return isset(self::PUBLIC_ROUTES[$entidad])
            && in_array($accion, self::PUBLIC_ROUTES[$entidad], true);
    }

    public static function isAdminRoute(string $entidad, string $accion): bool
    {
        return isset(self::ADMIN_ROUTES[$entidad])
            && in_array($accion, self::ADMIN_ROUTES[$entidad], true);
    }

    public static function isStandaloneView(string $entidad, string $accion): bool
    {
        return in_array($entidad, ['home', 'auth', 'admin', 'usuario'], true);
    }

    public static function authorize(string $entidad, string $accion, bool $isAuthenticated, bool $isAdmin): void
    {
        if (!isset(self::CONTROLLERS[$entidad])) {
            header('Location: index.php?entidad=home&accion=ver');
            exit();
        }

        if (!self::isPublicRoute($entidad, $accion) && !$isAuthenticated) {
            header('Location: index.php?entidad=auth&accion=mostrarLogin');
            exit();
        }

        if (self::isAdminRoute($entidad, $accion) && !$isAdmin) {
            header('Location: index.php?entidad=usuario&accion=dashboard');
            exit();
        }
    }

    public static function dispatch(string $baseDir, string $entidad, string $accion): void
    {
        if (!isset(self::CONTROLLERS[$entidad])) {
            echo '<p>Entidad no válida.</p>';
            return;
        }

        $meta = self::CONTROLLERS[$entidad];
        $filePath = $baseDir . '/Controlador/' . $meta['file'];

        if (!file_exists($filePath)) {
            echo '<p>Controlador no disponible.</p>';
            return;
        }

        require_once $filePath;
        $className = $meta['class'];

        if (!class_exists($className)) {
            echo '<p>Clase de controlador no encontrada.</p>';
            return;
        }

        $controller = new $className();

        if (method_exists($controller, $accion)) {
            $reflection = new ReflectionMethod($controller, $accion);
            if ($reflection->isPublic() && !$reflection->isStatic() && !$reflection->isConstructor()) {
                $controller->$accion();
                return;
            }
        }

        http_response_code(404);
        echo '<p>La acción solicitada no existe o no está permitida.</p>';
    }
}
PHP
);

// 2. app/Validators/AuthValidator.php
writeCleanUtf8('app/Validators/AuthValidator.php', <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Validators;

final class AuthValidator
{
    /**
     * @param array<string, mixed> $data
     */
    public static function validateRegistro(array $data): ?string
    {
        $nombre = trim((string) ($data['nombre'] ?? ''));
        $apellido = trim((string) ($data['apellido'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $dni = trim((string) ($data['dni'] ?? ''));
        $telefono = trim((string) ($data['telefono'] ?? ''));
        $direccion = trim((string) ($data['direccion'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        $confirmar = '';
        if (isset($data['confirmar_password']) && $data['confirmar_password'] !== '') {
            $confirmar = (string) $data['confirmar_password'];
        } elseif (isset($data['password_confirm']) && $data['password_confirm'] !== '') {
            $confirmar = (string) $data['password_confirm'];
        } elseif (isset($data['password_confirmar']) && $data['password_confirmar'] !== '') {
            $confirmar = (string) $data['password_confirmar'];
        }

        if ($nombre === '') {
            return 'El nombre es obligatorio y no debe superar los 100 caracteres.';
        }
        if (mb_strlen($nombre) > 100) {
            return 'El nombre es obligatorio y no debe superar los 100 caracteres.';
        }
        if ($apellido === '') {
            return 'El apellido es obligatorio y no debe superar los 100 caracteres.';
        }
        if (mb_strlen($apellido) > 100) {
            return 'El apellido es obligatorio y no debe superar los 100 caracteres.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Ingresa un correo electrónico válido.';
        }
        if (strlen($email) > 100) {
            return 'Ingresa un correo electrónico válido.';
        }
        if (!preg_match('/^\d{8}$/', $dni)) {
            return 'El DNI debe contener exactamente 8 dígitos numéricos.';
        }
        if ($telefono !== '' && !preg_match('/^\d{9}$/', $telefono)) {
            return 'El teléfono debe contener exactamente 9 dígitos numéricos.';
        }
        if (mb_strlen($direccion) > 200) {
            return 'La dirección no debe superar los 200 caracteres.';
        }
        if ($password !== $confirmar) {
            return 'Las contraseñas no coinciden.';
        }

        return self::validatePasswordStrength($password);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function validatePerfil(array $data): ?string
    {
        $nombre = trim((string) ($data['nombre'] ?? ''));
        $apellido = trim((string) ($data['apellido'] ?? ''));
        $dni = trim((string) ($data['dni'] ?? ''));
        $telefono = trim((string) ($data['telefono'] ?? ''));
        $direccion = trim((string) ($data['direccion'] ?? ''));

        if ($nombre === '') {
            return 'El nombre es obligatorio.';
        }
        if (mb_strlen($nombre) > 100) {
            return 'El nombre es obligatorio.';
        }
        if ($apellido === '') {
            return 'El apellido es obligatorio.';
        }
        if (mb_strlen($apellido) > 100) {
            return 'El apellido es obligatorio.';
        }
        if (!preg_match('/^\d{8}$/', $dni)) {
            return 'El DNI debe tener exactamente 8 dígitos.';
        }
        if ($telefono !== '' && !preg_match('/^\d{9}$/', $telefono)) {
            return 'El teléfono debe tener exactamente 9 dígitos.';
        }
        if (mb_strlen($direccion) > 200) {
            return 'La dirección no debe superar los 200 caracteres.';
        }

        return null;
    }

    public static function validatePasswordStrength(string $password): ?string
    {
        if (strlen($password) < 6) {
            return 'La contraseña debe tener al menos 6 caracteres.';
        }
        return null;
    }
}
PHP
);

// 3. app/Validators/InteraccionValidator.php
writeCleanUtf8('app/Validators/InteraccionValidator.php', <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Validators;

final class InteraccionValidator
{
    /**
     * @param array<string, mixed> $data
     */
    public static function validateReclamacion(array $data): ?string
    {
        $nombre = trim((string) ($data['nombre_cliente'] ?? $data['nombres'] ?? ''));
        $dni = trim((string) ($data['dni_cliente'] ?? $data['numero_documento'] ?? ''));
        $email = trim((string) ($data['email_cliente'] ?? $data['email'] ?? ''));
        $telefono = trim((string) ($data['telefono_cliente'] ?? $data['telefono'] ?? ''));
        $tipo = trim((string) ($data['tipo'] ?? $data['tipo_reclamo'] ?? ''));
        $detalle = trim((string) ($data['detalle'] ?? ''));
        $tipoDoc = trim((string) ($data['tipo_documento'] ?? 'DNI'));

        if ($nombre === '') {
            return 'El nombre del cliente es obligatorio (máximo 100 caracteres).';
        }
        if (mb_strlen($nombre) > 100) {
            return 'El nombre del cliente es obligatorio (máximo 100 caracteres).';
        }
        if ($tipoDoc === 'DNI' && !preg_match('/^[0-9]{8}$/', $dni)) {
            return 'El DNI debe contener exactamente 8 dígitos numéricos.';
        }
        if ($dni === '') {
            return 'El número de documento es obligatorio.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Debe ingresar un correo electrónico válido.';
        }
        if (mb_strlen($email) > 100) {
            return 'Debe ingresar un correo electrónico válido.';
        }
        if ($telefono !== '' && !preg_match('/^[0-9]{7,15}$/', $telefono)) {
            return 'El teléfono debe contener entre 7 y 15 dígitos numéricos.';
        }
        if (!in_array($tipo, ['Reclamo', 'Queja'], true)) {
            return 'El tipo debe ser Reclamo o Queja.';
        }
        if ($detalle === '') {
            return 'El detalle es obligatorio (máximo 1000 caracteres).';
        }
        if (mb_strlen($detalle) > 1000) {
            return 'El detalle es obligatorio (máximo 1000 caracteres).';
        }

        return null;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function validateSugerencia(array $data): ?string
    {
        $nombre = trim((string) ($data['nombre_cliente'] ?? $data['nombre'] ?? ''));
        $email = trim((string) ($data['email_cliente'] ?? $data['email'] ?? ''));
        $mensaje = trim((string) ($data['mensaje'] ?? $data['sugerencia'] ?? ''));

        if ($nombre === '') {
            return 'El nombre es obligatorio (máximo 100 caracteres).';
        }
        if (mb_strlen($nombre) > 100) {
            return 'El nombre es obligatorio (máximo 100 caracteres).';
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'El correo electrónico no es válido.';
        }
        if ($mensaje === '') {
            return 'El mensaje de sugerencia es obligatorio.';
        }
        if (mb_strlen($mensaje) > 1000) {
            return 'El mensaje de sugerencia es obligatorio.';
        }

        return null;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function validateVideo(array $data): ?string
    {
        $titulo = trim((string) ($data['titulo'] ?? ''));
        $url = trim((string) ($data['url_video'] ?? $data['url'] ?? ''));

        if ($titulo === '') {
            return 'El título del video es obligatorio (máximo 150 caracteres).';
        }
        if (mb_strlen($titulo) > 150) {
            return 'El título del video es obligatorio (máximo 150 caracteres).';
        }
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            return 'Debe ingresar una URL válida para el video.';
        }

        return null;
    }
}
PHP
);

// 4. app/Repositories/DashboardRepository.php
writeCleanUtf8('app/Repositories/DashboardRepository.php', <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

final class DashboardRepository
{
    public function __construct(private readonly mysqli $db)
    {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getCitasPorDniDueno(string $dni): array
    {
        if ($dni === '') {
            return [];
        }
        $sql = "SELECT c.id, c.fecha, c.hora, c.motivo, c.estado, 
                       m.nombre AS nombre_mascota, m.especie, 
                       v.nombre AS nombre_veterinario, v.especialidad 
                FROM citas c 
                INNER JOIN mascotas m ON c.id_mascota = m.id 
                INNER JOIN veterinarios v ON c.id_veterinario = v.id 
                WHERE m.dni_dueno = ? 
                ORDER BY c.fecha DESC, c.hora DESC";
        $stmt = $this->db->prepare($sql);
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

    /**
     * @return array<string, mixed>
     */
    public function getEstadisticasUsuario(string $dni): array
    {
        if ($dni === '') {
            return [
                'total_mascotas'   => 0,
                'total_citas'      => 0,
                'citas_pendientes' => 0,
                'citas_atendidas'  => 0,
                'proxima_cita'     => null,
            ];
        }

        $hoyLima = date('Y-m-d');
        $stats = [];

        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM mascotas WHERE dni_dueno = ? AND activo = 1");
        $stmt->bind_param('s', $dni);
        $stmt->execute();
        $stats['total_mascotas'] = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
        $stmt->close();

        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) AS total_citas,
                SUM(CASE WHEN c.estado = 'Pendiente' THEN 1 ELSE 0 END) AS pendientes,
                SUM(CASE WHEN c.estado = 'Atendida' THEN 1 ELSE 0 END) AS atendidas
            FROM citas c
            INNER JOIN mascotas m ON c.id_mascota = m.id
            WHERE m.dni_dueno = ?
        ");
        $stmt->bind_param('s', $dni);
        $stmt->execute();
        $rowCitas = $stmt->get_result()->fetch_assoc();
        $stats['total_citas']      = (int) ($rowCitas['total_citas'] ?? 0);
        $stats['citas_pendientes'] = (int) ($rowCitas['pendientes'] ?? 0);
        $stats['citas_atendidas']  = (int) ($rowCitas['atendidas'] ?? 0);
        $stmt->close();

        $stmt = $this->db->prepare("
            SELECT c.fecha, c.hora, c.motivo, m.nombre AS nombre_mascota, v.nombre AS nombre_veterinario 
            FROM citas c 
            INNER JOIN mascotas m ON c.id_mascota = m.id 
            INNER JOIN veterinarios v ON c.id_veterinario = v.id 
            WHERE m.dni_dueno = ? AND c.estado = 'Pendiente' AND c.fecha >= ? 
            ORDER BY c.fecha ASC, c.hora ASC LIMIT 1
        ");
        $stmt->bind_param('ss', $dni, $hoyLima);
        $stmt->execute();
        $stats['proxima_cita'] = $stmt->get_result()->fetch_assoc() ?: null;
        $stmt->close();

        return $stats;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getCitasRecientesAdmin(int $limit = 10): array
    {
        $sql = "SELECT c.id, c.fecha, c.hora, c.motivo, c.estado,
                       m.nombre AS nombre_mascota, m.especie,
                       d.nombre AS nombre_dueno, d.telefono AS telefono_dueno,
                       v.nombre AS nombre_veterinario, v.especialidad
                FROM citas c
                INNER JOIN mascotas m ON c.id_mascota = m.id
                INNER JOIN duenos d ON m.dni_dueno = d.dni
                INNER JOIN veterinarios v ON c.id_veterinario = v.id
                ORDER BY c.fecha DESC, c.hora DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $res = $stmt->get_result();
        $citas = [];
        while ($fila = $res->fetch_assoc()) {
            $citas[] = $fila;
        }
        $stmt->close();
        return $citas;
    }
}
PHP
);

// 5. app/Repositories/InteraccionRepository.php
writeCleanUtf8('app/Repositories/InteraccionRepository.php', <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

final class InteraccionRepository
{
    public function __construct(private readonly mysqli $db)
    {
    }

    public function createReclamacion(array $d): bool
    {
        $tipoDoc   = trim((string) ($d['tipo_documento'] ?? 'DNI'));
        $numDoc    = trim((string) ($d['numero_documento'] ?? $d['dni_cliente'] ?? ''));
        $nombres   = trim((string) ($d['nombres'] ?? $d['nombre_cliente'] ?? ''));
        $apellidos = trim((string) ($d['apellidos'] ?? ''));
        $telefono  = trim((string) ($d['telefono'] ?? $d['telefono_cliente'] ?? ''));
        $email     = trim((string) ($d['email'] ?? $d['email_cliente'] ?? ''));
        $direccion = trim((string) ($d['direccion'] ?? 'No especificada'));
        $tipoRec   = trim((string) ($d['tipo_reclamo'] ?? $d['tipo'] ?? 'Reclamo'));
        $detalle   = trim((string) ($d['detalle'] ?? ''));
        $pedido    = trim((string) ($d['pedido'] ?? $d['pedido_cliente'] ?? ''));

        $sql = "INSERT INTO reclamaciones 
                (tipo_documento, numero_documento, nombres, apellidos, telefono, email, direccion, tipo_reclamo, detalle, pedido, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pendiente')";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(
            'ssssssssss',
            $tipoDoc,
            $numDoc,
            $nombres,
            $apellidos,
            $telefono,
            $email,
            $direccion,
            $tipoRec,
            $detalle,
            $pedido
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listReclamaciones(): array
    {
        $res = $this->db->query("SELECT * FROM reclamaciones ORDER BY fecha_registro DESC");
        $rows = [];
        while ($r = $res->fetch_assoc()) {
            $rows[] = $r;
        }
        return $rows;
    }

    public function updateEstadoReclamacion(int $id, string $estado): bool
    {
        $stmt = $this->db->prepare("UPDATE reclamaciones SET estado = ? WHERE id = ?");
        $stmt->bind_param('si', $estado, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function deleteReclamacion(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM reclamaciones WHERE id = ?");
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function createSugerencia(array $d): bool
    {
        $nombre     = trim((string) ($d['nombre'] ?? $d['nombre_cliente'] ?? ''));
        $email      = trim((string) ($d['email'] ?? $d['email_cliente'] ?? ''));
        $telefono   = trim((string) ($d['telefono'] ?? ''));
        $categoria  = trim((string) ($d['categoria'] ?? 'General'));
        $sugerencia = trim((string) ($d['sugerencia'] ?? $d['mensaje'] ?? ''));

        $sql = "INSERT INTO sugerencias (nombre, email, telefono, categoria, sugerencia, estado) 
                VALUES (?, ?, ?, ?, ?, 'Pendiente')";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssss', $nombre, $email, $telefono, $categoria, $sugerencia);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listSugerencias(): array
    {
        $res = $this->db->query("SELECT * FROM sugerencias ORDER BY fecha_registro DESC");
        $rows = [];
        while ($r = $res->fetch_assoc()) {
            $rows[] = $r;
        }
        return $rows;
    }

    public function deleteSugerencia(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM sugerencias WHERE id = ?");
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listVideos(): array
    {
        $res = $this->db->query("SELECT * FROM videos ORDER BY id DESC");
        $rows = [];
        while ($r = $res->fetch_assoc()) {
            $rows[] = $r;
        }
        return $rows;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findVideoById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM videos WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row ?: null;
    }

    public function createVideo(string $titulo, string $descripcion, string $urlVideo, string $categoria = 'Servicios'): bool
    {
        $sql = "INSERT INTO videos (titulo, descripcion, url_video, categoria, activo) VALUES (?, ?, ?, ?, 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssss', $titulo, $descripcion, $urlVideo, $categoria);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function updateVideo(int $id, string $titulo, string $descripcion, string $urlVideo, string $categoria = 'Servicios', int $activo = 1): bool
    {
        $sql = "UPDATE videos SET titulo = ?, descripcion = ?, url_video = ?, categoria = ?, activo = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssssii', $titulo, $descripcion, $urlVideo, $categoria, $activo, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function deleteVideo(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM videos WHERE id = ?");
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
PHP
);

// 6. Modelos (Usuario, Reporte, Reclamacion, Sugerencia, Video)
writeCleanUtf8('Modelo/Usuario.php', <<<'PHP'
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

    public static function estaLogueado(): bool
    {
        return self::estaAutenticado();
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
PHP
);

writeCleanUtf8('Modelo/Reporte.php', <<<'PHP'
<?php

declare(strict_types=1);

require_once __DIR__ . '/../Servicios/conexion.php';

use App\Support\Database;

class Reporte
{
    public static function obtenerEstadisticas(): array
    {
        $conn = Database::getConnection();
        $stats = [];

        $r = $conn->query("SELECT COUNT(*) AS total FROM usuarios WHERE rol = 'usuario' AND activo = 1");
        $stats['total_usuarios'] = (int) ($r->fetch_assoc()['total'] ?? 0);

        $r = $conn->query("SELECT COUNT(*) AS total FROM duenos WHERE activo = 1");
        $stats['total_duenos'] = (int) ($r->fetch_assoc()['total'] ?? 0);

        $r = $conn->query("SELECT COUNT(*) AS total FROM mascotas WHERE activo = 1");
        $stats['total_mascotas'] = (int) ($r->fetch_assoc()['total'] ?? 0);

        $r = $conn->query("SELECT COUNT(*) AS total FROM veterinarios WHERE activo = 1");
        $stats['total_veterinarios'] = (int) ($r->fetch_assoc()['total'] ?? 0);

        $r = $conn->query("
            SELECT 
                COUNT(*) AS total_citas,
                SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) AS pendientes,
                SUM(CASE WHEN estado = 'Atendida' THEN 1 ELSE 0 END) AS atendidas,
                SUM(CASE WHEN estado = 'Cancelada' THEN 1 ELSE 0 END) AS canceladas
            FROM citas
        ");
        $rowCitas = $r->fetch_assoc();
        $stats['total_citas']      = (int) ($rowCitas['total_citas'] ?? 0);
        $stats['citas_pendientes'] = (int) ($rowCitas['pendientes'] ?? 0);
        $stats['citas_atendidas']  = (int) ($rowCitas['atendidas'] ?? 0);
        $stats['citas_canceladas'] = (int) ($rowCitas['canceladas'] ?? 0);

        return $stats;
    }

    public static function obtenerCitasPorEstado(): array
    {
        $conn = Database::getConnection();
        $res = $conn->query("SELECT estado, COUNT(*) AS cantidad FROM citas GROUP BY estado");
        $citas = [];
        while ($fila = $res->fetch_assoc()) {
            $citas[$fila['estado']] = (int) $fila['cantidad'];
        }
        return $citas;
    }

    public static function obtenerMascotasPorEspecie(): array
    {
        $conn = Database::getConnection();
        $res = $conn->query("SELECT especie, COUNT(*) AS cantidad FROM mascotas WHERE activo = 1 GROUP BY especie");
        $mascotas = [];
        while ($fila = $res->fetch_assoc()) {
            $mascotas[] = $fila;
        }
        return $mascotas;
    }

    public static function obtenerVeterinariosPopulares(): array
    {
        $conn = Database::getConnection();
        $sql = "SELECT v.id, v.nombre, v.especialidad, COUNT(c.id) AS total_citas
                FROM veterinarios v
                LEFT JOIN citas c ON v.id = c.id_veterinario
                WHERE v.activo = 1
                GROUP BY v.id, v.nombre, v.especialidad
                ORDER BY total_citas DESC, v.nombre ASC";
        $res = $conn->query($sql);
        $veterinarios = [];
        while ($fila = $res->fetch_assoc()) {
            $veterinarios[] = $fila;
        }
        return $veterinarios;
    }

    public static function obtenerCitasPorMes(): array
    {
        $conn = Database::getConnection();
        $res = $conn->query("SELECT DATE_FORMAT(fecha, '%Y-%m') AS mes, COUNT(*) AS cantidad FROM citas GROUP BY mes ORDER BY mes DESC LIMIT 6");
        $citas = [];
        while ($fila = $res->fetch_assoc()) {
            $citas[] = $fila;
        }
        return array_reverse($citas);
    }
}
PHP
);

writeCleanUtf8('Modelo/Sugerencia.php', <<<'PHP'
<?php

declare(strict_types=1);

require_once __DIR__ . '/../Servicios/conexion.php';

use App\Repositories\InteraccionRepository;
use App\Support\Database;

class Sugerencia
{
    public static function listarTodas(): array
    {
        return (new InteraccionRepository(Database::getConnection()))->listSugerencias();
    }

    public static function guardar(string $nombre, string $email, string $mensaje, string $telefono = '', string $categoria = 'General'): bool
    {
        return (new InteraccionRepository(Database::getConnection()))->createSugerencia([
            'nombre'     => $nombre,
            'email'      => $email,
            'telefono'   => $telefono,
            'categoria'  => $categoria,
            'sugerencia' => $mensaje,
        ]);
    }

    public static function eliminar(int|string $id): bool
    {
        return (new InteraccionRepository(Database::getConnection()))->deleteSugerencia((int) $id);
    }
}
PHP
);

writeCleanUtf8('Modelo/Video.php', <<<'PHP'
<?php

declare(strict_types=1);

require_once __DIR__ . '/../Servicios/conexion.php';

use App\Repositories\InteraccionRepository;
use App\Support\Database;

class Video
{
    public static function listarTodos(): array
    {
        return (new InteraccionRepository(Database::getConnection()))->listVideos();
    }

    public static function obtenerPorId(int|string $id): ?array
    {
        return (new InteraccionRepository(Database::getConnection()))->findVideoById((int) $id);
    }

    public static function guardar(string $titulo, string $descripcion, string $urlVideo, string $categoria = 'Servicios'): bool
    {
        return (new InteraccionRepository(Database::getConnection()))->createVideo($titulo, $descripcion, $urlVideo, $categoria);
    }

    public static function actualizar(int|string $id, string $titulo, string $descripcion, string $urlVideo, string $categoria = 'Servicios', int $activo = 1): bool
    {
        return (new InteraccionRepository(Database::getConnection()))->updateVideo((int) $id, $titulo, $descripcion, $urlVideo, $categoria, $activo);
    }

    public static function eliminar(int|string $id): bool
    {
        return (new InteraccionRepository(Database::getConnection()))->deleteVideo((int) $id);
    }
}
PHP
);

// Continuación desde 7. Controlador/ControladorDueno.php
writeCleanUtf8('Controlador/ControladorDueno.php', <<<'PHP'
<?php

declare(strict_types=1);

require_once __DIR__ . '/../Modelo/Dueno.php';
require_once __DIR__ . '/../Modelo/Usuario.php';

use App\Support\Csrf;
use App\Support\Logger;
use App\Support\Session;
use App\Validators\CatalogoValidator;

class ControladorDueno
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
        $duenos = Dueno::listarTodos();
        $datos = $duenos;
        include __DIR__ . '/../Vista/duenos/listar.php';
    }

    public function agregar(): void
    {
        $this->verificarAdmin();
        include __DIR__ . '/../Vista/duenos/agregar.php';
    }

    public function mostrarFormularioAgregar(): void
    {
        $this->agregar();
    }

    public function guardarNuevo(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=dueno&accion=agregar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=dueno&accion=agregar');
            exit();
        }

        $error = CatalogoValidator::validateDueno($_POST);
        if ($error !== null) {
            Session::setFlash('error', $error);
            header('Location: index.php?entidad=dueno&accion=agregar');
            exit();
        }

        $dni = trim((string) $_POST['dni']);
        $nombre = trim((string) $_POST['nombre']);
        $telefono = trim((string) $_POST['telefono']);
        $direccion = trim((string) $_POST['direccion']);

        try {
            $ok = Dueno::guardar($dni, $nombre, $telefono, $direccion);
            if ($ok) {
                Session::setFlash('exito', 'Dueño registrado exitosamente.');
                Session::setFlash('success', 'Dueño registrado exitosamente.');
            } else {
                Session::setFlash('error', 'El DNI ingresado ya se encuentra registrado activo.');
                header('Location: index.php?entidad=dueno&accion=agregar');
                exit();
            }
        } catch (\Throwable $e) {
            Logger::error('Error guardando dueno', $e);
            Session::setFlash('error', 'Error al guardar el dueño.');
        }

        header('Location: index.php?entidad=dueno&accion=listar');
        exit();
    }

    public function editar(): void
    {
        $this->verificarAdmin();
        $dni = trim((string) ($_GET['dni'] ?? ''));
        $dueno = Dueno::obtenerPorDni($dni);
        if ($dueno === null) {
            Session::setFlash('error', 'Dueño no encontrado.');
            header('Location: index.php?entidad=dueno&accion=listar');
            exit();
        }
        $d = $dueno;
        include __DIR__ . '/../Vista/duenos/editar.php';
    }

    public function mostrarFormularioEditar(): void
    {
        $this->editar();
    }

    public function actualizar(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=dueno&accion=listar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=dueno&accion=listar');
            exit();
        }

        $error = CatalogoValidator::validateDueno($_POST);
        $dni = trim((string) ($_POST['dni'] ?? ''));
        if ($error !== null) {
            Session::setFlash('error', $error);
            header('Location: index.php?entidad=dueno&accion=editar&dni=' . urlencode($dni));
            exit();
        }

        $nombre = trim((string) $_POST['nombre']);
        $telefono = trim((string) $_POST['telefono']);
        $direccion = trim((string) $_POST['direccion']);

        try {
            Dueno::actualizar($dni, $nombre, $telefono, $direccion);
            Session::setFlash('exito', 'Datos del dueño actualizados correctamente.');
            Session::setFlash('success', 'Datos del dueño actualizados correctamente.');
        } catch (\Throwable $e) {
            Logger::error('Error actualizando dueno', $e);
            Session::setFlash('error', 'No se pudo actualizar el dueño.');
        }

        header('Location: index.php?entidad=dueno&accion=listar');
        exit();
    }

    public function eliminar(): void
    {
        $this->verificarAdmin();
        Session::start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=dueno&accion=listar');
            exit();
        }

        $dni = trim((string) ($_POST['dni'] ?? $_GET['dni'] ?? ''));
        try {
            Dueno::eliminar($dni);
            Session::setFlash('exito', 'Dueño y sus mascotas asociados han sido desactivados preservando el historial.');
            Session::setFlash('success', 'Dueño y sus mascotas asociados han sido desactivados preservando el historial.');
        } catch (\Throwable $e) {
            Logger::error('Error desactivando dueno', $e);
            Session::setFlash('error', 'No se pudo desactivar el registro.');
        }

        header('Location: index.php?entidad=dueno&accion=listar');
        exit();
    }
}
PHP
);

// 8. Controlador/ControladorMascota.php
writeCleanUtf8('Controlador/ControladorMascota.php', <<<'PHP'
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
        Session::start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=mascota&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
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
PHP
);

// 9. Controlador/ControladorVeterinario.php
writeCleanUtf8('Controlador/ControladorVeterinario.php', <<<'PHP'
<?php

declare(strict_types=1);

require_once __DIR__ . '/../Modelo/Veterinario.php';
require_once __DIR__ . '/../Modelo/Usuario.php';

use App\Repositories\VeterinarioRepository;
use App\Support\Csrf;
use App\Support\Database;
use App\Support\Logger;
use App\Support\Session;
use App\Validators\CatalogoValidator;

class ControladorVeterinario
{
    private function verificarAdmin(): void
    {
        if (!Usuario::esAdministrador()) {
            header('Location: index.php?entidad=home&accion=ver');
            exit();
        }
    }

    public function verPublico(): void
    {
        $veterinarios = Veterinario::listarTodos();
        $datos = $veterinarios;
        include __DIR__ . '/../Vista/veterinarios/ver_publico.php';
    }

    public function listar(): void
    {
        $this->verificarAdmin();
        $veterinarios = Veterinario::listarTodos();
        $datos = $veterinarios;
        include __DIR__ . '/../Vista/veterinarios/listar.php';
    }

    public function agregar(): void
    {
        $this->verificarAdmin();
        include __DIR__ . '/../Vista/veterinarios/agregar.php';
    }

    public function mostrarFormularioAgregar(): void
    {
        $this->agregar();
    }

    public function guardarNuevo(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=veterinario&accion=agregar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=veterinario&accion=agregar');
            exit();
        }

        $error = CatalogoValidator::validateVeterinario($_POST);
        if ($error !== null) {
            Session::setFlash('error', $error);
            header('Location: index.php?entidad=veterinario&accion=agregar');
            exit();
        }

        $correo = strtolower(trim((string) $_POST['correo']));
        $repo = new VeterinarioRepository(Database::getConnection());
        if ($repo->existsEmail($correo)) {
            Session::setFlash('error', 'El correo electrónico ya está registrado en otro veterinario.');
            header('Location: index.php?entidad=veterinario&accion=agregar');
            exit();
        }

        try {
            Veterinario::guardar(
                trim((string) $_POST['nombre']),
                trim((string) $_POST['especialidad']),
                $correo,
                trim((string) $_POST['telefono'])
            );
            Session::setFlash('exito', 'Veterinario registrado exitosamente.');
            Session::setFlash('success', 'Veterinario registrado exitosamente.');
        } catch (\Throwable $e) {
            Logger::error('Error guardando veterinario', $e);
            Session::setFlash('error', 'No se pudo registrar el veterinario.');
        }

        header('Location: index.php?entidad=veterinario&accion=listar');
        exit();
    }

    public function editar(): void
    {
        $this->verificarAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        $veterinario = Veterinario::obtenerPorId($id);
        if ($veterinario === null) {
            Session::setFlash('error', 'Veterinario no encontrado.');
            header('Location: index.php?entidad=veterinario&accion=listar');
            exit();
        }
        $v = $veterinario;
        include __DIR__ . '/../Vista/veterinarios/editar.php';
    }

    public function mostrarFormularioEditar(): void
    {
        $this->editar();
    }

    public function actualizar(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=veterinario&accion=listar');
            exit();
        }

        Session::start();
        if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=veterinario&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? 0);
        $error = CatalogoValidator::validateVeterinario($_POST);
        if ($error !== null) {
            Session::setFlash('error', $error);
            header('Location: index.php?entidad=veterinario&accion=editar&id=' . $id);
            exit();
        }

        $correo = strtolower(trim((string) $_POST['correo']));
        $repo = new VeterinarioRepository(Database::getConnection());
        if ($repo->existsEmail($correo, $id)) {
            Session::setFlash('error', 'El correo electrónico ya pertenece a otro veterinario.');
            header('Location: index.php?entidad=veterinario&accion=editar&id=' . $id);
            exit();
        }

        try {
            Veterinario::actualizar(
                $id,
                trim((string) $_POST['nombre']),
                trim((string) $_POST['especialidad']),
                $correo,
                trim((string) $_POST['telefono'])
            );
            Session::setFlash('exito', 'Datos del veterinario actualizados correctamente.');
            Session::setFlash('success', 'Datos del veterinario actualizados correctamente.');
        } catch (\Throwable $e) {
            Logger::error('Error actualizando veterinario', $e);
            Session::setFlash('error', 'No se pudo actualizar el veterinario.');
        }

        header('Location: index.php?entidad=veterinario&accion=listar');
        exit();
    }

    public function eliminar(): void
    {
        $this->verificarAdmin();
        Session::start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=veterinario&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        try {
            Veterinario::eliminar($id);
            Session::setFlash('exito', 'Veterinario desactivado conservando su historial de citas.');
            Session::setFlash('success', 'Veterinario desactivado conservando su historial de citas.');
        } catch (\Throwable $e) {
            Logger::error('Error desactivando veterinario', $e);
            Session::setFlash('error', 'No se pudo desactivar el veterinario.');
        }

        header('Location: index.php?entidad=veterinario&accion=listar');
        exit();
    }
}
PHP
);

// 10. Controlador/ControladorCita.php
writeCleanUtf8('Controlador/ControladorCita.php', <<<'PHP'
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
        $datos = $citas;
        include __DIR__ . '/../Vista/citas/listar.php';
    }

    public function agregar(): void
    {
        $this->verificarAdmin();
        $mascotas = Mascota::listarTodas();
        $veterinarios = Veterinario::listarTodos();
        include __DIR__ . '/../Vista/citas/agregar.php';
    }

    public function mostrarFormularioAgregar(): void
    {
        $this->agregar();
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
            Session::setFlash('success', $res['mensaje']);
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
        $c = $cita;
        $mascotas = Mascota::listarTodas();
        $veterinarios = Veterinario::listarTodos();
        include __DIR__ . '/../Vista/citas/editar.php';
    }

    public function mostrarFormularioEditar(): void
    {
        $this->editar();
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
            Session::setFlash('success', $res['mensaje']);
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
        Session::start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=cita&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        $estado = trim((string) ($_POST['estado'] ?? $_GET['estado'] ?? ''));

        $res = (new CitaService())->cambiarEstado($id, $estado);
        if ($res['exito']) {
            Session::setFlash('exito', $res['mensaje']);
            Session::setFlash('success', $res['mensaje']);
        } else {
            Session::setFlash('error', $res['mensaje']);
        }

        header('Location: index.php?entidad=cita&accion=listar');
        exit();
    }

    public function eliminar(): void
    {
        $this->verificarAdmin();
        Session::start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=cita&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        if (Cita::eliminar($id)) {
            Session::setFlash('exito', 'Cita eliminada correctamente.');
            Session::setFlash('success', 'Cita eliminada correctamente.');
        } else {
            Session::setFlash('error', 'No se pudo eliminar la cita.');
        }

        header('Location: index.php?entidad=cita&accion=listar');
        exit();
    }
}
PHP
);

// 11. Controlador/ControladorReclamacion.php
writeCleanUtf8('Controlador/ControladorReclamacion.php', <<<'PHP'
<?php

declare(strict_types=1);

require_once __DIR__ . '/../Modelo/Reclamacion.php';
require_once __DIR__ . '/../Modelo/Usuario.php';

use App\Support\Csrf;
use App\Support\Logger;
use App\Support\Session;
use App\Validators\InteraccionValidator;

class ControladorReclamacion
{
    public function crear(): void
    {
        include __DIR__ . '/../Vista/reclamaciones/formulario.php';
    }

    public function mostrarFormulario(): void
    {
        $this->crear();
    }

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=reclamacion&accion=mostrarFormulario');
            exit();
        }

        Session::start();
        if (isset($_POST['csrf_token']) && !Csrf::verify($_POST['csrf_token'])) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=reclamacion&accion=mostrarFormulario');
            exit();
        }

        $error = InteraccionValidator::validateReclamacion($_POST);
        if ($error !== null) {
            Session::setFlash('error', $error);
            header('Location: index.php?entidad=reclamacion&accion=mostrarFormulario');
            exit();
        }

        try {
            Reclamacion::guardar($_POST);
            Session::setFlash('exito', 'Su reclamación o queja ha sido registrada con éxito en el Libro de Reclamaciones.');
            Session::setFlash('success', 'Su reclamación o queja ha sido registrada con éxito en el Libro de Reclamaciones.');
        } catch (\Throwable $e) {
            Logger::error('Error registrando reclamacion', $e);
            Session::setFlash('error', 'Ocurrió un error al registrar su solicitud.');
        }

        header('Location: index.php?entidad=reclamacion&accion=mostrarFormulario');
        exit();
    }

    public function listar(): void
    {
        if (!Usuario::esAdministrador()) {
            header('Location: index.php?entidad=home&accion=ver');
            exit();
        }
        $reclamaciones = Reclamacion::listarTodas();
        $datos = $reclamaciones;
        include __DIR__ . '/../Vista/reclamaciones/listar.php';
    }

    public function cambiarEstado(): void
    {
        if (!Usuario::esAdministrador()) {
            header('Location: index.php?entidad=home&accion=ver');
            exit();
        }

        Session::start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=reclamacion&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        $estado = trim((string) ($_POST['estado'] ?? $_GET['estado'] ?? 'Pendiente'));

        if (Reclamacion::cambiarEstado($id, $estado)) {
            Session::setFlash('exito', 'Estado actualizado a ' . $estado);
            Session::setFlash('success', 'Estado actualizado a ' . $estado);
        } else {
            Session::setFlash('error', 'No se pudo actualizar el estado.');
        }

        header('Location: index.php?entidad=reclamacion&accion=listar');
        exit();
    }

    public function eliminar(): void
    {
        if (!Usuario::esAdministrador()) {
            header('Location: index.php?entidad=home&accion=ver');
            exit();
        }

        Session::start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=reclamacion&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        if (Reclamacion::eliminar($id)) {
            Session::setFlash('exito', 'Registro eliminado del Libro de Reclamaciones.');
            Session::setFlash('success', 'Registro eliminado del Libro de Reclamaciones.');
        } else {
            Session::setFlash('error', 'No se pudo eliminar el registro.');
        }

        header('Location: index.php?entidad=reclamacion&accion=listar');
        exit();
    }
}
PHP
);

// 12. Controlador/ControladorSugerencia.php
writeCleanUtf8('Controlador/ControladorSugerencia.php', <<<'PHP'
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
        if (isset($_POST['csrf_token']) && !Csrf::verify($_POST['csrf_token'])) {
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

        Session::start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=sugerencia&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
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
PHP
);

// 13. Controlador/ControladorVideo.php
writeCleanUtf8('Controlador/ControladorVideo.php', <<<'PHP'
<?php

declare(strict_types=1);

require_once __DIR__ . '/../Modelo/Video.php';
require_once __DIR__ . '/../Modelo/Usuario.php';

use App\Support\Csrf;
use App\Support\Logger;
use App\Support\Session;
use App\Validators\InteraccionValidator;

class ControladorVideo
{
    private function verificarAdmin(): void
    {
        if (!Usuario::esAdministrador()) {
            header('Location: index.php?entidad=home&accion=ver');
            exit();
        }
    }

    public function verPublico(): void
    {
        $videos = Video::listarTodos();
        $datos = $videos;
        include __DIR__ . '/../Vista/videos/ver_publico.php';
    }

    public function listar(): void
    {
        $this->verificarAdmin();
        $videos = Video::listarTodos();
        $datos = $videos;
        include __DIR__ . '/../Vista/videos/listar.php';
    }

    public function agregar(): void
    {
        $this->verificarAdmin();
        include __DIR__ . '/../Vista/videos/agregar.php';
    }

    public function mostrarFormularioAgregar(): void
    {
        $this->agregar();
    }

    public function guardar(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=video&accion=agregar');
            exit();
        }

        Session::start();
        if (isset($_POST['csrf_token']) && !Csrf::verify($_POST['csrf_token'])) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=video&accion=agregar');
            exit();
        }

        $error = InteraccionValidator::validateVideo($_POST);
        if ($error !== null) {
            Session::setFlash('error', $error);
            header('Location: index.php?entidad=video&accion=agregar');
            exit();
        }

        try {
            $urlVideo = trim((string) ($_POST['url_video'] ?? $_POST['url'] ?? ''));
            $categoria = trim((string) ($_POST['categoria'] ?? 'Servicios'));
            Video::guardar(
                trim((string) $_POST['titulo']),
                trim((string) ($_POST['descripcion'] ?? '')),
                $urlVideo,
                $categoria
            );
            Session::setFlash('exito', 'Video publicado exitosamente.');
            Session::setFlash('success', 'Video publicado exitosamente.');
        } catch (\Throwable $e) {
            Logger::error('Error guardando video', $e);
            Session::setFlash('error', 'No se pudo guardar el video.');
        }

        header('Location: index.php?entidad=video&accion=listar');
        exit();
    }

    public function guardarNuevo(): void
    {
        $this->guardar();
    }

    public function editar(): void
    {
        $this->verificarAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        $video = Video::obtenerPorId($id);
        if ($video === null) {
            Session::setFlash('error', 'Video no encontrado.');
            header('Location: index.php?entidad=video&accion=listar');
            exit();
        }
        $v = $video;
        include __DIR__ . '/../Vista/videos/editar.php';
    }

    public function mostrarFormularioEditar(): void
    {
        $this->editar();
    }

    public function actualizar(): void
    {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?entidad=video&accion=listar');
            exit();
        }

        Session::start();
        if (isset($_POST['csrf_token']) && !Csrf::verify($_POST['csrf_token'])) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=video&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? 0);
        $error = InteraccionValidator::validateVideo($_POST);
        if ($error !== null) {
            Session::setFlash('error', $error);
            header('Location: index.php?entidad=video&accion=editar&id=' . $id);
            exit();
        }

        try {
            $urlVideo = trim((string) ($_POST['url_video'] ?? $_POST['url'] ?? ''));
            $categoria = trim((string) ($_POST['categoria'] ?? 'Servicios'));
            $activo = isset($_POST['activo']) ? (int) $_POST['activo'] : 1;
            Video::actualizar(
                $id,
                trim((string) $_POST['titulo']),
                trim((string) ($_POST['descripcion'] ?? '')),
                $urlVideo,
                $categoria,
                $activo
            );
            Session::setFlash('exito', 'Video actualizado correctamente.');
            Session::setFlash('success', 'Video actualizado correctamente.');
        } catch (\Throwable $e) {
            Logger::error('Error actualizando video', $e);
            Session::setFlash('error', 'No se pudo actualizar el video.');
        }

        header('Location: index.php?entidad=video&accion=listar');
        exit();
    }

    public function eliminar(): void
    {
        $this->verificarAdmin();
        Session::start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Token de seguridad inválido.');
            header('Location: index.php?entidad=video&accion=listar');
            exit();
        }

        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        if (Video::eliminar($id)) {
            Session::setFlash('exito', 'Video eliminado correctamente.');
            Session::setFlash('success', 'Video eliminado correctamente.');
        } else {
            Session::setFlash('error', 'No se pudo eliminar el video.');
        }

        header('Location: index.php?entidad=video&accion=listar');
        exit();
    }
}
PHP
);

// 14. Controlador/ControladorReporte.php
writeCleanUtf8('Controlador/ControladorReporte.php', <<<'PHP'
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
PHP
);

// 15. Parchear formularios en Vistas que aún no tengan Csrf::field()
$formViews = [
    'Vista/auth/login.php',
    'Vista/auth/registro.php',
    'Vista/usuario/perfil.php',
    'Vista/duenos/agregar.php',
    'Vista/duenos/editar.php',
    'Vista/mascotas/agregar.php',
    'Vista/mascotas/editar.php',
    'Vista/veterinarios/agregar.php',
    'Vista/veterinarios/editar.php',
    'Vista/citas/agregar.php',
    'Vista/citas/editar.php',
    'Vista/reclamaciones/formulario.php',
    'Vista/sugerencias/formulario.php',
    'Vista/videos/agregar.php',
    'Vista/videos/editar.php',
];

foreach ($formViews as $relView) {
    $full = __DIR__ . '/' . $relView;
    if (!file_exists($full)) {
        continue;
    }
    $html = file_get_contents($full);
    $html = preg_replace('/^\xEF\xBB\xBF/', '', $html);
    if (!str_contains($html, 'Csrf::field()')) {
        $html = preg_replace(
            '/(<form\b[^>]*method=["\']POST["\'][^>]*>)/i',
            "$1\n            <?= \\App\\Support\\Csrf::field() ?>",
            $html
        );
        file_put_contents($full, $html);
        echo "[OK] Inyectado CSRF en: $relView\n";
    }
}

echo "\n========================================================\n";
echo " SINCRONIZACIÓN INTEGRAL COMPLETADA CON ÉXITO\n";
echo "========================================================\n";