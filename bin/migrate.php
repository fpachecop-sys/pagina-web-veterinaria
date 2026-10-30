<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = (int)($_ENV['DB_PORT'] ?? 3306);
$db   = $_ENV['DB_DATABASE'] ?? 'veterinaria_x';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';

$mysqli = new mysqli($host, $user, $pass, $db, $port);

if ($mysqli->connect_error) {
    fwrite(STDERR, "Error conectando a la BD: " . $mysqli->connect_error . PHP_EOL);
    exit(1);
}

$mysqli->query("
    CREATE TABLE IF NOT EXISTS `schema_migrations` (
        `version` VARCHAR(255) NOT NULL,
        `executed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`version`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

$res = $mysqli->query("SELECT `version` FROM `schema_migrations`");
$applied = [];
while ($row = $res->fetch_assoc()) {
    $applied[] = $row['version'];
}

$migrationsDir = dirname(__DIR__) . '/database/migrations';
$files = glob($migrationsDir . '/*.sql');
sort($files);

$executedCount = 0;
foreach ($files as $filePath) {
    $version = basename($filePath);
    if (in_array($version, $applied, true)) {
        continue;
    }

    echo "Ejecutando: $version... ";
    
    // Obtener y limpiar queries individuales
    $rawSql = file_get_contents($filePath);
    $queries = array_filter(
        array_map('trim', explode(';', $rawSql)),
        fn($q) => !empty($q)
    );

    // Desactivar FK checks para la sesión
    $mysqli->query("SET FOREIGN_KEY_CHECKS = 0");

    try {
        foreach ($queries as $query) {
            $mysqli->query($query);
        }

        // Restaurar FKs que hayan quedado sueltas
        $citasFkCheck = $mysqli->query("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = '{$db}' AND TABLE_NAME = 'citas' AND CONSTRAINT_NAME = 'citas_ibfk_1'
        ");
        if ($citasFkCheck && $citasFkCheck->num_rows === 0) {
            $mysqli->query("ALTER TABLE `citas` ADD CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`id_mascota`) REFERENCES `mascotas` (`id`) ON DELETE CASCADE");
        }

        $mascotasFkCheck = $mysqli->query("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = '{$db}' AND TABLE_NAME = 'mascotas' AND CONSTRAINT_NAME = 'mascotas_ibfk_1'
        ");
        if ($mascotasFkCheck && $mascotasFkCheck->num_rows === 0) {
            $mysqli->query("ALTER TABLE `mascotas` ADD CONSTRAINT `mascotas_ibfk_1` FOREIGN KEY (`dni_dueno`) REFERENCES `duenos` (`dni`) ON DELETE CASCADE");
        }

        $stmt = $mysqli->prepare("INSERT INTO `schema_migrations` (`version`) VALUES (?)");
        $stmt->bind_param('s', $version);
        $stmt->execute();
        $stmt->close();

        echo "[OK]" . PHP_EOL;
        $executedCount++;
    } catch (\Throwable $e) {
        echo "[ERROR]" . PHP_EOL;
        fwrite(STDERR, "Fallo en $version: " . $e->getMessage() . PHP_EOL);
        $mysqli->query("SET FOREIGN_KEY_CHECKS = 1");
        exit(1);
    }

    $mysqli->query("SET FOREIGN_KEY_CHECKS = 1");
}

if ($executedCount === 0) {
    echo "No hay migraciones pendientes." . PHP_EOL;
} else {
    echo "Migraciones completadas con éxito." . PHP_EOL;
}
