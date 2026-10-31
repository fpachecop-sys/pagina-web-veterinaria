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

    public function testValidateRejectsInvalidDateOrTime(): void
    {
        $data = [
            'fecha'          => 'fecha-invalida',
            'hora'           => '10:30',
            'id_mascota'     => 1,
            'id_veterinario' => 2,
            'motivo'         => 'Consulta',
        ];
        $this->assertNotNull(CitaValidator::validate($data));
    }

    public function testValidateRejectsInvalidStatus(): void
    {
        $this->assertTrue(CitaValidator::isValidEstado('Pendiente'));
        $this->assertTrue(CitaValidator::isValidEstado('Atendida'));
        $this->assertTrue(CitaValidator::isValidEstado('Cancelada'));
        $this->assertFalse(CitaValidator::isValidEstado('Inventado'));
    }
}