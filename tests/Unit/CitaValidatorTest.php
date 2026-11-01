<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Validators\CitaValidator;
use PHPUnit\Framework\TestCase;

final class CitaValidatorTest extends TestCase
{
    public function testValidateAcceptsValidAppointment(): void
    {
        $data = [
            'fecha'          => '2026-11-15',
            'hora'           => '10:30',
            'id_mascota'     => 1,
            'id_veterinario' => 2,
            'motivo'         => 'Control general y vacunas',
            'estado'         => 'Pendiente',
        ];
        $this->assertNull(CitaValidator::validate($data));
    }

    public function testValidateAcceptsTimeWithSeconds(): void
    {
        $data = [
            'fecha'          => '2026-12-01',
            'hora'           => '16:45:00',
            'id_mascota'     => 3,
            'id_veterinario' => 1,
            'motivo'         => 'Chequeo postoperatorio',
        ];
        $this->assertNull(CitaValidator::validate($data));
    }

    public function testValidateRejectsInvalidDateFormat(): void
    {
        $data = [
            'fecha'          => 'fecha-invalida',
            'hora'           => '10:30',
            'id_mascota'     => 1,
            'id_veterinario' => 2,
            'motivo'         => 'Consulta',
        ];
        $this->assertSame('La fecha de la cita no tiene un formato válido (YYYY-MM-DD).', CitaValidator::validate($data));
    }

    public function testValidateRejectsInvalidTimeFormat(): void
    {
        $data = [
            'fecha'          => '2026-11-15',
            'hora'           => '25:00',
            'id_mascota'     => 1,
            'id_veterinario' => 2,
            'motivo'         => 'Consulta general',
        ];
        $this->assertSame('La hora de la cita no tiene un formato válido (HH:MM).', CitaValidator::validate($data));
    }

    public function testValidateRejectsInvalidMascotaOrVeterinarioId(): void
    {
        $data = [
            'fecha'          => '2026-11-15',
            'hora'           => '10:30',
            'id_mascota'     => 0,
            'id_veterinario' => 2,
            'motivo'         => 'Consulta general',
        ];
        $this->assertSame('Debe seleccionar una mascota válida.', CitaValidator::validate($data));

        $data['id_mascota'] = 1;
        $data['id_veterinario'] = -5;
        $this->assertSame('Debe seleccionar un veterinario válido.', CitaValidator::validate($data));
    }

    public function testValidateRejectsEmptyOrExcessiveMotivo(): void
    {
        $data = [
            'fecha'          => '2026-11-15',
            'hora'           => '10:30',
            'id_mascota'     => 1,
            'id_veterinario' => 2,
            'motivo'         => '   ',
        ];
        $this->assertSame('El motivo de la cita es obligatorio y no debe superar los 200 caracteres.', CitaValidator::validate($data));

        $data['motivo'] = str_repeat('M', 201);
        $this->assertSame('El motivo de la cita es obligatorio y no debe superar los 200 caracteres.', CitaValidator::validate($data));
    }

    public function testValidateRejectsInvalidEstadoInDataArray(): void
    {
        $data = [
            'fecha'          => '2026-11-15',
            'hora'           => '10:30',
            'id_mascota'     => 1,
            'id_veterinario' => 2,
            'motivo'         => 'Baño medicado',
            'estado'         => 'Reprogramada',
        ];
        $this->assertSame('El estado de la cita no es válido.', CitaValidator::validate($data));
    }

    public function testIsValidEstadoCatalog(): void
    {
        $this->assertTrue(CitaValidator::isValidEstado('Pendiente'));
        $this->assertTrue(CitaValidator::isValidEstado('Atendida'));
        $this->assertTrue(CitaValidator::isValidEstado('Cancelada'));
        $this->assertFalse(CitaValidator::isValidEstado('Inventado'));
    }
}