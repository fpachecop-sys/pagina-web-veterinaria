<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Validators\CatalogoValidator;
use PHPUnit\Framework\TestCase;

final class CatalogoValidatorTest extends TestCase
{
    public function testValidateDuenoSuccess(): void
    {
        $data = [
            'dni'       => '75085925',
            'nombre'    => 'Franco Pacheco',
            'telefono'  => '978475665',
            'direccion' => 'Lima Norte',
        ];
        $this->assertNull(CatalogoValidator::validateDueno($data));
    }

    public function testValidateMascotaRejectsNegativeOrExcessiveAge(): void
    {
        $data = [
            'nombre'    => 'Oto',
            'especie'   => 'Perro',
            'raza'      => 'Pitbull',
            'edad'      => -2,
            'dni_dueno' => '75085925',
        ];
        $this->assertNotNull(CatalogoValidator::validateMascota($data));
    }

    public function testValidateVeterinarioRequiresValidEmail(): void
    {
        $data = [
            'nombre'       => 'Dr. Roberto Vega',
            'especialidad' => 'Cirugía',
            'correo'       => 'correo_invalido',
            'telefono'     => '999888777',
        ];
        $this->assertNotNull(CatalogoValidator::validateVeterinario($data));
    }
}
