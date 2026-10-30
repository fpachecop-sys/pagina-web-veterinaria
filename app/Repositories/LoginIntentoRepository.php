<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

final class LoginIntentoRepository
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;

    public function __construct(private readonly mysqli $db)
    {
    }

    public function isLocked(string $email, string$ip): bool
    {
        $emailNorm = strtolower(trim($email));$sql = "SELECT bloqueado_hasta FROM login_intentos 
                WHERE email = ? AND ip_address = ? AND bloqueado_hasta > NOW() LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ss',$emailNorm, $ip);$stmt->execute();
        $res =$stmt->get_result();
        $locked = ($res && $res->num_rows > 0);$stmt->close();

        return $locked;
    }

    public function recordFailedAttempt(string $email, string$ip): void
    {
        $emailNorm = strtolower(trim($email));$max = self::MAX_ATTEMPTS;
        $mins = self::LOCKOUT_MINUTES;

        $sql = "INSERT INTO login_intentos (email, ip_address, intentos, bloqueado_hasta)
                VALUES (?, ?, 1, NULL)
                ON DUPLICATE KEY UPDATE
                    intentos = IF(bloqueado_hasta IS NOT NULL AND bloqueado_hasta <= NOW(), 1, intentos + 1),
                    bloqueado_hasta = IF(
                        IF(bloqueado_hasta IS NOT NULL AND bloqueado_hasta <= NOW(), 1, intentos + 1) >= ?,
                        DATE_ADD(NOW(), INTERVAL ? MINUTE),
                        bloqueado_hasta
                    )";
        $stmt =$this->db->prepare($sql);$stmt->bind_param('ssii', $emailNorm,$ip, $max,$mins);
        $stmt->execute();$stmt->close();
    }

    public function clearAttempts(string $email, string$ip): void
    {
        $emailNorm = strtolower(trim($email));
        $stmt =$this->db->prepare("DELETE FROM login_intentos WHERE email = ? AND ip_address = ?");
        $stmt->bind_param('ss', $emailNorm,$ip);
        $stmt->execute();$stmt->close();
    }
}
