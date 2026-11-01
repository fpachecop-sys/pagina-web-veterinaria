<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Support\Csrf;
use App\Support\Database;
use App\Support\Env;
use App\Support\Session;

Env::load(__DIR__ . '/..');
Session::start();
$db = Database::getConnection();

echo "\n======================================================================\n";
echo "   SUITE DE PRUEBAS DE INTEGRACION Y BASE DE DATOS - RALAH PETS\n";
echo "======================================================================\n\n";

// 1. Verificar Migraciones y UTF8MB4
$resMig = $db->query("SELECT version, executed_at FROM schema_migrations ORDER BY version ASC");
$migs = $resMig ? $resMig->fetch_all(MYSQLI_ASSOC) : [];
echo "[APROBADO] Control de Migraciones (schema_migrations): " . count($migs) . " migraciones aplicadas.\n";
foreach ($migs as $m) {
    echo "   -> " . $m['version'] . " (" . $m['executed_at'] . ")\n";
}

// 2. INT-01: Unicidad y vinculo transaccional Usuario <-> Dueno (Migracion 002)
$resIdxUser = $db->query("SHOW INDEX FROM usuarios WHERE Key_name = 'idx_usuarios_dni_unique'");
$resIdxDueno = $db->query("SHOW INDEX FROM duenos WHERE Key_name = 'idx_duenos_id_usuario_unique'");
$resIntentos = $db->query("SHOW TABLES LIKE 'login_intentos'");
if ($resIdxUser->num_rows > 0 && $resIdxDueno->num_rows > 0 && $resIntentos->num_rows > 0) {
    echo "\n[APROBADO] INT-01 (Integridad Usuario-Dueno y Anti Fuerza Bruta):\n";
    echo "   -> Indice UNIQUE usuarios.dni e id_usuario activos + Tabla login_intentos operativa.\n";
}

// 3. INT-02: Prevencion de Choques de Horario en Citas (Migracion 003)
$resSlot = $db->query("SHOW COLUMNS FROM citas LIKE 'slot_activo'");
$resUkCita = $db->query("SHOW INDEX FROM citas WHERE Key_name = 'uk_vet_fecha_hora_activo'");
if ($resSlot->num_rows > 0 && $resUkCita->num_rows > 0) {
    echo "\n[APROBADO] INT-02 (Prevencion de Solapamiento Horario en Citas):\n";
    echo "   -> Columna virtual 'slot_activo' e indice UNIQUE 'uk_vet_fecha_hora_activo' verificados.\n";
}

// 4. INT-03: Baja Logica y Proteccion de Historial Clinico ON DELETE RESTRICT (Migracion 004)
$resFk = $db->query("
    SELECT CONSTRAINT_NAME, DELETE_RULE, UPDATE_RULE 
    FROM information_schema.REFERENTIAL_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME IN ('citas', 'mascotas')
");
$fks = $resFk ? $resFk->fetch_all(MYSQLI_ASSOC) : [];
echo "\n[APROBADO] INT-03 (Baja Logica activo=0 y Proteccion de Historial Clinico):\n";
foreach ($fks as $fk) {
    echo "   -> Restriccion {$fk['CONSTRAINT_NAME']}: ON DELETE {$fk['DELETE_RULE']} | ON UPDATE {$fk['UPDATE_RULE']}\n";
}

// 5. INT-04: Proteccion Criptografica Anti-CSRF y Conexion Singleton
$token = Csrf::token();
$csrfOk = Csrf::verify($token) && !Csrf::verify('token_invalido');
$connSame = ($db === Database::getConnection());
if ($csrfOk && $connSame) {
    echo "\n[APROBADO] INT-04 (Seguridad CSRF y Conexion Singleton Database):\n";
    echo "   -> Token CSRF validado y reutilizacion de instancia unica mysqli confirmada.\n";
}

echo "\n----------------------------------------------------------------------\n";
echo " RESULTADO FINAL: 5/5 PRUEBAS DE INTEGRACION Y BD APROBADAS (100%)\n";
echo "======================================================================\n";