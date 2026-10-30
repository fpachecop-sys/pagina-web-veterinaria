<?php

declare(strict_types=1);

namespace App\Validators;

final class CatalogoValidator
{
    /**
     * @param array<string, mixed> $data
     */
    public static function validateDueno(array $data): ?string
    {
        $dni = trim((string) ($data['dni'] ?? ''));
        $nombre = trim((string) ($data['nombre'] ?? ''));
        $telefono = trim((string) ($data['telefono'] ?? ''));
        $direccion = trim((string) ($data['direccion'] ?? ''));

        if (!preg_match('/^\d{8}$/', $dni)) {
            return 'El DNI del dueño debe contener exactamente 8 dígitos numéricos.';
        }
        if ($nombre === '' || strlen($nombre) > 100) {
            return 'El nombre del dueño es obligatorio y no debe exceder 100 caracteres.';
        }
        if (!preg_match('/^\d{9}$/', $telefono)) {
            return 'El teléfono debe contener exactamente 9 dígitos numéricos.';
        }
        if ($direccion === '' || strlen($direccion) > 200) {
            return 'La dirección es obligatoria y no debe exceder 200 caracteres.';
        }

        return null;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function validateMascota(array $data): ?string
    {
        $nombre = trim((string) ($data['nombre'] ?? ''));
        $especie = trim((string) ($data['especie'] ?? ''));
        $raza = trim((string) ($data['raza'] ?? ''));
        $edadRaw = $data['edad'] ?? '';
        $dniDueno = trim((string) ($data['dni_dueno'] ?? ''));

        if ($nombre === '' || strlen($nombre) > 50) {
            return 'El nombre de la mascota es obligatorio (máx. 50 caracteres).';
        }
        if ($especie === '' || strlen($especie) > 50) {
            return 'La especie es obligatoria (máx. 50 caracteres).';
        }
        if ($raza === '' || strlen($raza) > 50) {
            return 'La raza es obligatoria (máx. 50 caracteres).';
        }
        if (!is_numeric($edadRaw) || (int) $edadRaw < 0 || (int) $edadRaw > 40) {
            return 'La edad de la mascota debe ser un número válido entre 0 y 40 años.';
        }
        if (!preg_match('/^\d{8}$/', $dniDueno)) {
            return 'Debe seleccionar un dueño con un DNI válido de 8 dígitos.';
        }

        return null;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function validateVeterinario(array $data): ?string
    {
        $nombre = trim((string) ($data['nombre'] ?? ''));
        $especialidad = trim((string) ($data['especialidad'] ?? ''));
        $correo = trim((string) ($data['correo'] ?? ''));
        $telefono = trim((string) ($data['telefono'] ?? ''));

        if ($nombre === '' || strlen($nombre) > 100) {
            return 'El nombre del veterinario es obligatorio (máx. 100 caracteres).';
        }
        if ($especialidad === '' || strlen($especialidad) > 100) {
            return 'La especialidad es obligatoria (máx. 100 caracteres).';
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($correo) > 100) {
            return 'El correo electrónico del veterinario no es válido.';
        }
        if (!preg_match('/^\d{9}$/', $telefono)) {
            return 'El teléfono del veterinario debe contener exactamente 9 dígitos.';
        }

        return null;
    }
}
