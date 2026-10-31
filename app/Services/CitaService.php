<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CitaRepository;
use App\Support\Database;
use App\Support\Logger;
use App\Validators\CitaValidator;
use Throwable;

final class CitaService
{
    private CitaRepository $citas;

    public function __construct()
    {
        $this->citas = new CitaRepository(Database::getConnection());
    }

    /**
     * @param array<string, mixed> $datos
     * @return array{exito: bool, mensaje: string}
     */
    public function agendarCita(array $datos): array
    {
        $error = CitaValidator::validate($datos);
        if ($error !== null) {
            return ['exito' => false, 'mensaje' => $error];
        }

        $fecha = trim((string)$datos['fecha']);
        $hora = trim((string)$datos['hora']);
        $idMascota = (int)$datos['id_mascota'];
        $idVeterinario = (int)$datos['id_veterinario'];
        $motivo = trim((string)$datos['motivo']);

        $db = Database::getConnection();$db->begin_transaction();

        try {
            if ($this->citas->hasScheduleConflict($idVeterinario,$fecha, $hora)) {$db->rollback();
                return [
                    'exito'   => false,
                    'mensaje' => 'El veterinario seleccionado ya tiene una cita programada en esa fecha y hora.',
                ];
            }

            $this->citas->create($fecha,$hora, $idMascota,$idVeterinario, $motivo);$db->commit();

            return ['exito' => true, 'mensaje' => 'Cita registrada exitosamente.'];
        } catch (Throwable $e) {$db->rollback();
            Logger::error('Error agendando cita', $e);
            return ['exito' => false, 'mensaje' => 'No se pudo agendar la cita por conflicto de horario o datos inválidos.'];
        }
    }

    /**
     * @param array<string, mixed> $datos
     * @return array{exito: bool, mensaje: string}
     */
    public function actualizarCita(int $id, array$datos): array
    {
        $error = CitaValidator::validate($datos);
        if ($error !== null) {
            return ['exito' => false, 'mensaje' => $error];
        }

        $fecha = trim((string)$datos['fecha']);
        $hora = trim((string)$datos['hora']);
        $idMascota = (int)$datos['id_mascota'];
        $idVeterinario = (int)$datos['id_veterinario'];
        $motivo = trim((string)$datos['motivo']);
        $estado = trim((string) ($datos['estado'] ?? 'Pendiente'));

        $db = Database::getConnection();$db->begin_transaction();

        try {
            if ($estado !== 'Cancelada' && $this->citas->hasScheduleConflict($idVeterinario, $fecha,$hora, $id)) {$db->rollback();
                return [
                    'exito'   => false,
                    'mensaje' => 'Conflicto de horario: el veterinario ya tiene otra cita activa en ese bloque.',
                ];
            }

            $this->citas->update($id,$fecha, $hora,$idMascota, $idVeterinario,$motivo, $estado);$db->commit();

            return ['exito' => true, 'mensaje' => 'Cita actualizada correctamente.'];
        } catch (Throwable $e) {$db->rollback();
            Logger::error('Error actualizando cita ID ' . $id,$e);
            return ['exito' => false, 'mensaje' => 'No se pudo actualizar la cita.'];
        }
    }

    /**
     * @return array{exito: bool, mensaje: string}
     */
    public function cambiarEstado(int $id, string$nuevoEstado): array
    {
        if (!CitaValidator::isValidEstado($nuevoEstado)) {
            return ['exito' => false, 'mensaje' => 'Estado de cita no válido.'];
        }

        $cita = $this->citas->findById($id);
        if ($cita === null) {
            return ['exito' => false, 'mensaje' => 'La cita indicada no existe.'];
        }

        $db = Database::getConnection();$db->begin_transaction();

        try {
            if ($nuevoEstado !== 'Cancelada' && $this->citas->hasScheduleConflict((int)$cita['id_veterinario'], (string) $cita['fecha'], (string)$cita['hora'], $id)) {$db->rollback();
                return ['exito' => false, 'mensaje' => 'No se puede reactivar la cita porque ese horario ya fue ocupado.'];
            }

            $this->citas->updateEstado($id, $nuevoEstado);$db->commit();
            return ['exito' => true, 'mensaje' => 'Estado de la cita actualizado a ' . $nuevoEstado . '.'];
        } catch (Throwable $e) {$db->rollback();
            Logger::error('Error cambiando estado de cita', $e);
            return ['exito' => false, 'mensaje' => 'No se pudo cambiar el estado de la cita.'];
        }
    }
}