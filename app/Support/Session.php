<?php

declare(strict_types=1);

namespace App\Support;

final class Session
{
    private const TIMEOUT_SECONDS = 1800; // 30 minutos

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            self::checkActivity();
            return;
        }

        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isSecure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
        self::checkActivity();
    }

    private static function checkActivity(): void
    {
        $now = time();
        if (isset($_SESSION['_last_activity']) && ($now - (int) $_SESSION['_last_activity']) > self::TIMEOUT_SECONDS) {
            self::destroy();
            session_start();
            self::setFlash('error', 'Tu sesión ha expirado por inactividad. Por favor inicia sesión nuevamente.');
        }
        $_SESSION['_last_activity'] = $now;
    }

    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
            $_SESSION['_last_activity'] = time();
        }
    }

    public static function destroy(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function setFlash(string $key, string $message): void
    {
        $_SESSION[$key] = $message;
    }

    public static function getFlash(string $key): ?string
    {
        if (!isset($_SESSION[$key])) {
            return null;
        }
        $msg = (string) $_SESSION[$key];
        unset($_SESSION[$key]);
        return $msg;
    }
}
