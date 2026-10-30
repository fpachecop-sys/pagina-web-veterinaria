<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Support\Database;
use App\Support\Env;

class conexion
{
    public static function conectar(): mysqli
    {
        Env::load(dirname(__DIR__));
        $db = Database::getConnection();
        
        // Retornamos una conexion compatible con los $conn->close() de los modelos antiguos
        // hasta que cada modelo sea migrado a su Repositorio en las siguientes fases.
        $host = Env::get('DB_HOST', '127.0.0.1') ?? '127.0.0.1';
        $port = (int) (Env::get('DB_PORT', '3306') ?? 3306);
        $name = Env::get('DB_DATABASE', 'veterinaria_x') ?? 'veterinaria_x';
        $user = Env::get('DB_USERNAME', 'root') ?? 'root';
        $pass = Env::get('DB_PASSWORD', '') ?? '';

        $conn = new mysqli($host, $user, $pass, $name, $port);
        $conn->set_charset('utf8mb4');
        return $conn;
    }
}
