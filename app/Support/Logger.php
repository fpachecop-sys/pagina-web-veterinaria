<?php

declare(strict_types=1);

namespace App\Support;

use Throwable;

final class Logger
{
    private static string $logFile = __DIR__ . '/../../storage/logs/app.log';

    public static function error(string $message, ?Throwable $exception = null): void
    {
        $dir = dirname(self::$logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $date = date('Y-m-d H:i:s');
        $context = '';

        if ($exception !== null) {
            $context = sprintf(
                " | Exception: %s in %s:%d\nStack trace:\n%s",
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                $exception->getTraceAsString()
            );
        }

        $entry = sprintf("[%s] ERROR: %s%s\n", $date, $message, $context);
        file_put_contents(self::$logFile, $entry, FILE_APPEND | LOCK_EX);
    }
}
