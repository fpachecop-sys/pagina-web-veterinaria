<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;
use RuntimeException;

final class VideoMediaStorage
{
    private const RELATIVE_DIRECTORY = 'assets/uploads/media';
    private const MAX_IMAGE_BYTES = 8_388_608;
    private const MAX_VIDEO_BYTES = 125_829_120;

    /** @var array<string, array{kind: string, mime: string}> */
    private const ALLOWED_TYPES = [
        'jpg'  => ['kind' => 'image', 'mime' => 'image/jpeg'],
        'jpeg' => ['kind' => 'image', 'mime' => 'image/jpeg'],
        'png'  => ['kind' => 'image', 'mime' => 'image/png'],
        'webp' => ['kind' => 'image', 'mime' => 'image/webp'],
        'gif'  => ['kind' => 'image', 'mime' => 'image/gif'],
        'mp4'  => ['kind' => 'video', 'mime' => 'video/mp4'],
        'webm' => ['kind' => 'video', 'mime' => 'video/webm'],
        'ogv'  => ['kind' => 'video', 'mime' => 'video/ogg'],
        'ogg'  => ['kind' => 'video', 'mime' => 'video/ogg'],
    ];

    /** @param array<string, mixed> $file */
    public static function store(array $file, string $expectedKind): string
    {
        if (!in_array($expectedKind, ['image', 'video'], true)) {
            throw new InvalidArgumentException('El tipo de archivo solicitado no es válido.');
        }

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException(self::uploadErrorMessage((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE)));
        }

        $temporaryPath = (string) ($file['tmp_name'] ?? '');
        $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
        $fileType = self::ALLOWED_TYPES[$extension] ?? null;

        if ($fileType === null || $fileType['kind'] !== $expectedKind) {
            throw new InvalidArgumentException($expectedKind === 'image'
                ? 'Sube una imagen JPG, PNG, WEBP o GIF para la promoción.'
                : 'Sube un video MP4, WEBM u OGG.');
        }

        $size = (int) ($file['size'] ?? 0);
        $maxBytes = $expectedKind === 'image' ? self::MAX_IMAGE_BYTES : self::MAX_VIDEO_BYTES;
        if ($size <= 0 || $size > $maxBytes) {
            throw new InvalidArgumentException($expectedKind === 'image'
                ? 'La imagen debe pesar como máximo 8 MB.'
                : 'El video debe pesar como máximo 120 MB.');
        }

        if (!is_uploaded_file($temporaryPath) || !is_file($temporaryPath)) {
            throw new InvalidArgumentException('No se pudo verificar el archivo recibido. Vuelve a seleccionarlo.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($temporaryPath);
        if ($mime !== $fileType['mime']) {
            throw new InvalidArgumentException('El contenido del archivo no coincide con su formato.');
        }

        $projectRoot = dirname(__DIR__, 2);
        $directory = $projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, self::RELATIVE_DIRECTORY);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException('No se pudo preparar la carpeta de archivos multimedia.');
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        if (!move_uploaded_file($temporaryPath, $directory . DIRECTORY_SEPARATOR . $filename)) {
            throw new RuntimeException('No se pudo guardar el archivo multimedia.');
        }

        return self::RELATIVE_DIRECTORY . '/' . $filename;
    }

    public static function isManagedPath(string $path): bool
    {
        if (!str_starts_with($path, self::RELATIVE_DIRECTORY . '/')) {
            return false;
        }

        $filename = substr($path, strlen(self::RELATIVE_DIRECTORY) + 1);
        return $filename !== '' && basename($filename) === $filename
            && preg_match('/^[a-f0-9]{32}\.(?:jpg|jpeg|png|webp|gif|mp4|webm|ogv|ogg)$/i', $filename) === 1;
    }

    public static function delete(string $path): void
    {
        if (!self::isManagedPath($path)) {
            return;
        }

        $projectRoot = dirname(__DIR__, 2);
        $absolutePath = $projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
        if (is_file($absolutePath)) {
            @unlink($absolutePath);
        }
    }

    private static function uploadErrorMessage(int $error): string
    {
        return match ($error) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'El archivo supera el límite permitido por el servidor.',
            UPLOAD_ERR_PARTIAL => 'El archivo llegó incompleto. Vuelve a intentar la carga.',
            UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE => 'El servidor no pudo recibir el archivo.',
            UPLOAD_ERR_NO_FILE => 'Selecciona el archivo que deseas cargar.',
            default => 'No se pudo recibir el archivo seleccionado.',
        };
    }
}
