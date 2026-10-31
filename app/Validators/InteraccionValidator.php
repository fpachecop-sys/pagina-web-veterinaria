<?php

declare(strict_types=1);

namespace App\Validators;

final class InteraccionValidator
{
    /**
     * @param array<string, mixed> $data
     */
    public static function validateReclamacion(array $data): ?string
    {
        $nombre = trim((string) ($data['nombre_cliente'] ?? $data['nombres'] ?? ''));
        $dni = trim((string) ($data['dni_cliente'] ?? $data['numero_documento'] ?? ''));
        $email = trim((string) ($data['email_cliente'] ?? $data['email'] ?? ''));
        $telefono = trim((string) ($data['telefono_cliente'] ?? $data['telefono'] ?? ''));
        $tipo = trim((string) ($data['tipo'] ?? $data['tipo_reclamo'] ?? ''));
        $detalle = trim((string) ($data['detalle'] ?? ''));
        $tipoDoc = trim((string) ($data['tipo_documento'] ?? 'DNI'));

        if ($nombre === '') {
            return 'El nombre del cliente es obligatorio (máximo 100 caracteres).';
        }
        if (mb_strlen($nombre) > 100) {
            return 'El nombre del cliente es obligatorio (máximo 100 caracteres).';
        }
        if ($tipoDoc === 'DNI' && !preg_match('/^[0-9]{8}$/', $dni)) {
            return 'El DNI debe contener exactamente 8 dígitos numéricos.';
        }
        if ($dni === '') {
            return 'El número de documento es obligatorio.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Debe ingresar un correo electrónico válido.';
        }
        if (mb_strlen($email) > 100) {
            return 'Debe ingresar un correo electrónico válido.';
        }
        if ($telefono !== '' && !preg_match('/^[0-9]{7,15}$/', $telefono)) {
            return 'El teléfono debe contener entre 7 y 15 dígitos numéricos.';
        }
        if (!in_array($tipo, ['Reclamo', 'Queja'], true)) {
            return 'El tipo debe ser Reclamo o Queja.';
        }
        if ($detalle === '') {
            return 'El detalle es obligatorio (máximo 1000 caracteres).';
        }
        if (mb_strlen($detalle) > 1000) {
            return 'El detalle es obligatorio (máximo 1000 caracteres).';
        }

        return null;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function validateSugerencia(array $data): ?string
    {
        $nombre = trim((string) ($data['nombre_cliente'] ?? $data['nombre'] ?? ''));
        $email = trim((string) ($data['email_cliente'] ?? $data['email'] ?? ''));
        $mensaje = trim((string) ($data['mensaje'] ?? $data['sugerencia'] ?? ''));

        if ($nombre === '') {
            return 'El nombre es obligatorio (máximo 100 caracteres).';
        }
        if (mb_strlen($nombre) > 100) {
            return 'El nombre es obligatorio (máximo 100 caracteres).';
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'El correo electrónico no es válido.';
        }
        if ($mensaje === '') {
            return 'El mensaje de sugerencia es obligatorio.';
        }
        if (mb_strlen($mensaje) > 1000) {
            return 'El mensaje de sugerencia es obligatorio.';
        }

        return null;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function validateVideo(array $data): ?string
    {
        $titulo = trim((string) ($data['titulo'] ?? ''));
        $url = trim((string) ($data['url_video'] ?? $data['url'] ?? ''));

        if ($titulo === '') {
            return 'El título del video es obligatorio (máximo 150 caracteres).';
        }
        if (mb_strlen($titulo) > 150) {
            return 'El título del video es obligatorio (máximo 150 caracteres).';
        }
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            return 'Debe ingresar una URL válida para el video.';
        }

        return null;
    }
}