<?php

declare(strict_types=1);

namespace App\Validators;

final class AuthValidator
{
    /**
     * @param array<string, mixed> $data
     */
    public static function validateRegistro(array $data): ?string
    {
        $nombre = trim((string) ($data['nombre'] ?? ''));
        $apellido = trim((string) ($data['apellido'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $dni = trim((string) ($data['dni'] ?? ''));
        $telefono = trim((string) ($data['telefono'] ?? ''));
        $direccion = trim((string) ($data['direccion'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        $confirmacionEncontrada = false;
        $confirmar = '';
        foreach (['confirmar_password', 'password_confirm', 'password_confirmar'] as $campoConfirmacion) {
            if (array_key_exists($campoConfirmacion, $data)) {
                $valorConfirmacion = $data[$campoConfirmacion];
                $confirmar = is_scalar($valorConfirmacion) ? (string) $valorConfirmacion : '';
                $confirmacionEncontrada = true;
                break;
            }
        }

        if ($nombre === '') {
            return 'El nombre es obligatorio y no debe superar los 100 caracteres.';
        }
        if (mb_strlen($nombre) > 100) {
            return 'El nombre es obligatorio y no debe superar los 100 caracteres.';
        }
        if ($apellido === '') {
            return 'El apellido es obligatorio y no debe superar los 100 caracteres.';
        }
        if (mb_strlen($apellido) > 100) {
            return 'El apellido es obligatorio y no debe superar los 100 caracteres.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Ingresa un correo electrónico válido.';
        }
        if (strlen($email) > 100) {
            return 'Ingresa un correo electrónico válido.';
        }
        if (!preg_match('/^\d{8}$/', $dni)) {
            return 'El DNI debe contener exactamente 8 dígitos numéricos.';
        }
        if ($telefono !== '' && !preg_match('/^\d{9}$/', $telefono)) {
            return 'El teléfono debe contener exactamente 9 dígitos numéricos.';
        }
        if (mb_strlen($direccion) > 200) {
            return 'La dirección no debe superar los 200 caracteres.';
        }
        if (!$confirmacionEncontrada || $confirmar === '') {
            return 'La confirmación de contraseña es obligatoria.';
        }
        if ($password !== $confirmar) {
            return 'Las contraseñas no coinciden.';
        }

        return self::validatePasswordStrength($password);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function validatePerfil(array $data): ?string
    {
        $nombre = trim((string) ($data['nombre'] ?? ''));
        $apellido = trim((string) ($data['apellido'] ?? ''));
        $dni = trim((string) ($data['dni'] ?? ''));
        $telefono = trim((string) ($data['telefono'] ?? ''));
        $direccion = trim((string) ($data['direccion'] ?? ''));

        if ($nombre === '') {
            return 'El nombre es obligatorio.';
        }
        if (mb_strlen($nombre) > 100) {
            return 'El nombre es obligatorio.';
        }
        if ($apellido === '') {
            return 'El apellido es obligatorio.';
        }
        if (mb_strlen($apellido) > 100) {
            return 'El apellido es obligatorio.';
        }
        if (!preg_match('/^\d{8}$/', $dni)) {
            return 'El DNI debe tener exactamente 8 dígitos.';
        }
        if ($telefono !== '' && !preg_match('/^\d{9}$/', $telefono)) {
            return 'El teléfono debe tener exactamente 9 dígitos.';
        }
        if (mb_strlen($direccion) > 200) {
            return 'La dirección no debe superar los 200 caracteres.';
        }

        return null;
    }

    public static function validatePasswordStrength(string $password): ?string
    {
        if (strlen($password) < 6) {
            return 'La contraseña debe tener al menos 6 caracteres.';
        }
        return null;
    }
}
