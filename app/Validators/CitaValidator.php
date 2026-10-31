<?php

declare(strict_types=1);

namespace App\Validators;

final class CitaValidator
{
    private const ESTADOS_VALIDOS = ['Pendiente', 'Atendida', 'Cancelada'];

    /**
     * @param array<string, mixed> $data
     */
    public static function validate(array $data): ?string
    {
        $fecha = trim((string) ($data['fecha'] ?? ''));
        $hora = trim((string) ($data['hora'] ?? ''));
        $idMascota = (int) ($data['id_mascota'] ?? 0);
        $idVeterinario = (int) ($data['id_veterinario'] ?? 0);
        $motivo = trim((string) ($data['motivo'] ?? ''));

        if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $fecha) || strtotime($fecha) === false) {
            return 'La fecha de la cita no tiene un formato válido (YYYY-MM-DD).';
        }

        // Valida formato de hora HH:MM o HH:MM:SS
        if (!preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9](?::[0-5][0-9])?$/', $hora)) {
            return 'La hora de la cita no tiene un formato válido (HH:MM).';
        }

        if ($idMascota <= 0) {
            return 'Debe seleccionar una mascota válida.';
        }
        if ($idVeterinario <= 0) {
            return 'Debe seleccionar un veterinario válido.';
        }
        if ($motivo === '' || mb_strlen($motivo) > 200) {
            return 'El motivo de la cita es obligatorio y no debe superar los 200 caracteres.';
        }

        if (isset($data['estado']) && !self::isValidEstado((string) $data['estado'])) {
            return 'El estado de la cita no es válido.';
        }

        return null;
    }

    public static function isValidEstado(string $estado): bool
    {
        return in_array($estado, self::ESTADOS_VALIDOS, true);
    }
}