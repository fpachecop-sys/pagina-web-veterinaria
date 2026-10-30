<?php

declare(strict_types=1);

namespace App\Support;

use Throwable;

final class ErrorHandler
{
    public static function register(): void
    {
        error_reporting(E_ALL);
        ini_set('display_errors', '0');

        set_exception_handler([self::class, 'handleException']);
    }

    public static function handleException(Throwable $e): void
    {
        Logger::error('Excepcion no capturada', $e);

        if (!headers_sent()) {
            http_response_code(500);
        }

        if (Env::isDebug()) {
            echo '<div style="font-family:sans-serif;padding:20px;background:#fee2e2;border:1px solid #ef4444;color:#991b1b;margin:20px;border-radius:8px;">';
            echo '<h2>Error del Sistema (Modo Debug)</h2>';
            echo '<p><strong>Mensaje:</strong> ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
            echo '<p><strong>Archivo:</strong> ' . htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8') . ':' . $e->getLine() . '</p>';
            echo '<pre style="background:#fff;padding:10px;overflow:auto;">' . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8') . '</pre>';
            echo '</div>';
            return;
        }

        echo '<div style="font-family:sans-serif;text-align:center;padding:50px;">';
        echo '<h1>Ocurrió un problema inesperado</h1>';
        echo '<p>Nuestro equipo técnico ha sido notificado. Por favor, intenta nuevamente en unos momentos.</p>';
        echo '<a href="index.php">Volver al inicio</a>';
        echo '</div>';
    }
}
