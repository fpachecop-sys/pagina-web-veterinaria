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

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $mysqli = new mysqli($host, $user, $pass, $db, $port);
    $mysqli->set_charset('utf8mb4');
} catch (\Throwable $e) {
    fwrite(STDERR, "Error conectando a la BD: " . $e->getMessage() . PHP_EOL);
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

$migrationsDir = dirname(__DIR__) . '/Database/migrations';
if (!is_dir($migrationsDir)) {
    $migrationsDir = dirname(__DIR__) . '/database/migrations';
}

$files = glob($migrationsDir . '/*.sql');
sort($files);

$executedCount = 0;
foreach ($files as $filePath) {
    $version = basename($filePath);
    if (in_array($version, $applied, true)) {
        continue;
    }

    echo "Ejecutando: $version... ";
    $rawSql = file_get_contents($filePath);
    
    // Limpiar BOM si existiera
    $rawSql = preg_replace('/^\xEF\xBB\xBF/', '', $rawSql);
    $lines = explode("\n", $rawSql);
    $cleanLines = array_filter($lines, function ($line) {
        $t = trim($line);
        return $t !== '' && !str_starts_with($t, '--');
    });
    $cleanSql = implode("\n", $cleanLines);

    $queries = array_filter(
        array_map('trim', explode(';', $cleanSql)),
        fn($q) => !empty($q)
    );

    $mysqli->query("SET FOREIGN_KEY_CHECKS = 0");

    try {
        foreach ($queries as $query) {
            try {
                $mysqli->query($query);
            } catch (\mysqli_sql_exception $sqle) {
                // Códigos 1060 (columna duplicada) o 1061 (clave/índice duplicado)
                if ($sqle->getCode() === 1060 || $sqle->getCode() === 1061) {
                    continue;
                }
                throw $sqle;
            }
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
    echo "Migraciones completadas con exito." . PHP_EOL;
}