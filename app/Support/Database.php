<?php

declare(strict_types=1);

namespace App\Support;

use mysqli;
use RuntimeException;

final class Database
{
    private static ?mysqli $instance = null;

    public static function getConnection(): mysqli
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $host = Env::get('DB_HOST', '127.0.0.1') ?? '127.0.0.1';
        $port = (int) (Env::get('DB_PORT', '3306') ?? 3306);
        $db   = Env::get('DB_DATABASE', 'veterinaria_x') ?? 'veterinaria_x';
        $user = Env::get('DB_USERNAME', 'root') ?? 'root';
        $pass = Env::get('DB_PASSWORD', '') ?? '';

        try {
            $conn = new mysqli($host, $user, $pass, $db, $port);
            $conn->set_charset('utf8mb4');
            self::$instance = $conn;
            return self::$instance;
        } catch (\Throwable $e) {
            Logger::error('Error al conectar con la base de datos', $e);
            throw new RuntimeException('No se pudo establecer conexión con la base de datos.');
        }
    }
}
