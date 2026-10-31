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
        $nombre = trim((string) ($data['nombre_cliente'] ?? ''));
        $dni = trim((string) ($data['dni_cliente'] ?? ''));
        $email = trim((string) ($data['email_cliente'] ?? ''));
        $telefono = trim((string) ($data['telefono_cliente'] ?? ''));
        $tipo = trim((string) ($data['tipo'] ?? ''));
        $detalle = trim((string) ($data['detalle'] ?? ''));

        if ($nombre === '' || mb_strlen($nombre) > 100) {
            return 'El nombre del cliente es obligatorio (máximo 100 caracteres).';
        }
        if (!preg_match('/^[0-9]{8}$/', $dni)) {
            return 'El DNI debe contener exactamente 8 dígitos numéricos.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
            return 'Debe ingresar un correo electrónico válido.';
        }
        if ($telefono !== '' && !preg_match('/^[0-9]{9}$/', $telefono)) {
            return 'El teléfono debe contener 9 dígitos numéricos.';
        }
        if (!in_array($tipo, ['Reclamo', 'Queja'], true)) {
            return 'El tipo debe ser Reclamo o Queja.';
        }
        if ($detalle === '' || mb_strlen($detalle) > 1000) {
            return 'El detalle es obligatorio (máximo 1000 caracteres).';
        }

        return null;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function validateSugerencia(array $data): ?string
    {
        $nombre = trim((string) ($data['nombre_cliente'] ?? ''));
        $email = trim((string) ($data['email_cliente'] ?? ''));
        $mensaje = trim((string) ($data['mensaje'] ?? ''));

        if ($nombre === '' || mb_strlen($nombre) > 100) {
            return 'El nombre es obligatorio (máximo 100 caracteres).';
        }
        if ($email !== '' && (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100)) {
            return 'El correo electrónico no es válido.';
        }
        if ($mensaje === '' || mb_strlen($mensaje) > 1000) {
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
        $url = trim((string) ($data['url'] ?? ''));

        if ($titulo === '' || mb_strlen($titulo) > 150) {
            return 'El título del video es obligatorio (máximo 150 caracteres).';
        }
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            return 'Debe ingresar una URL válida para el video.';
        }

        return null;
    }
}