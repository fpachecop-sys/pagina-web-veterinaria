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

    public function testValidateDuenoRejectsInvalidDniAndPhone(): void
    {
        $data = [
            'dni'       => '1234567',
            'nombre'    => 'Franco Pacheco',
            'telefono'  => '978475665',
            'direccion' => 'Lima Norte',
        ];
        $this->assertSame('El DNI del dueño debe contener exactamente 8 dígitos numéricos.', CatalogoValidator::validateDueno($data));

        $data['dni'] = '75085925';
        $data['telefono'] = '999888';
        $this->assertSame('El teléfono debe contener exactamente 9 dígitos numéricos.', CatalogoValidator::validateDueno($data));
    }

    public function testValidateDuenoRejectsEmptyNameOrAddress(): void
    {
        $data = [
            'dni'       => '75085925',
            'nombre'    => '',
            'telefono'  => '978475665',
            'direccion' => 'Lima Norte',
        ];
        $this->assertSame('El nombre del dueño es obligatorio y no debe exceder 100 caracteres.', CatalogoValidator::validateDueno($data));

        $data['nombre'] = 'Franco Pacheco';
        $data['direccion'] = '';
        $this->assertSame('La dirección es obligatoria y no debe exceder 200 caracteres.', CatalogoValidator::validateDueno($data));
    }

    public function testValidateMascotaSuccessAndBoundaryAges(): void
    {
        $mascotaCero = [
            'nombre'    => 'Oto',
            'especie'   => 'Perro',
            'raza'      => 'Pitbull',
            'edad'      => 0,
            'dni_dueno' => '75085925',
        ];
        $this->assertNull(CatalogoValidator::validateMascota($mascotaCero));

        $mascotaLimiteSuperior = $mascotaCero;
        $mascotaLimiteSuperior['edad'] = 40;
        $this->assertNull(CatalogoValidator::validateMascota($mascotaLimiteSuperior));
    }

    public function testValidateMascotaRejectsNegativeOrExcessiveAge(): void
    {
        $data = [
            'nombre'    => 'Oto',
            'especie'   => 'Perro',
            'raza'      => 'Pitbull',
            'edad'      => -1,
            'dni_dueno' => '75085925',
        ];
        $this->assertSame('La edad de la mascota debe ser un número válido entre 0 y 40 años.', CatalogoValidator::validateMascota($data));

        $data['edad'] = 41;
        $this->assertSame('La edad de la mascota debe ser un número válido entre 0 y 40 años.', CatalogoValidator::validateMascota($data));
    }

    public function testValidateMascotaRejectsEmptyFieldsAndInvalidOwnerDni(): void
    {
        $data = [
            'nombre'    => '',
            'especie'   => 'Perro',
            'raza'      => 'Pitbull',
            'edad'      => 2,
            'dni_dueno' => '75085925',
        ];
        $this->assertNotNull(CatalogoValidator::validateMascota($data));

        $data['nombre'] = 'Canela';
        $data['especie'] = '';
        $this->assertNotNull(CatalogoValidator::validateMascota($data));

        $data['especie'] = 'Perro';
        $data['raza'] = '';
        $this->assertNotNull(CatalogoValidator::validateMascota($data));

        $data['raza'] = 'Pitbull';
        $data['dni_dueno'] = 'DNI_MALO';
        $this->assertSame('Debe seleccionar un dueño con un DNI válido de 8 dígitos.', CatalogoValidator::validateMascota($data));
    }

    public function testValidateVeterinarioSuccess(): void
    {
        $data = [
            'nombre'       => 'Dra. Lucía Paredes',
            'especialidad' => 'Medicina Interna',
            'correo'       => 'lucia.paredes@ralahpets.com',
            'telefono'     => '987654321',
        ];
        $this->assertNull(CatalogoValidator::validateVeterinario($data));
    }

    public function testValidateVeterinarioRequiresValidEmail(): void
    {
        $data = [
            'nombre'       => 'Dr. Roberto Vega',
            'especialidad' => 'Cirugía',
            'correo'       => 'correo_invalido',
            'telefono'     => '999888777',
        ];
        $this->assertSame('El correo electrónico del veterinario no es válido.', CatalogoValidator::validateVeterinario($data));
    }

    public function testValidateVeterinarioRejectsInvalidPhone(): void
    {
        $data = [
            'nombre'       => 'Dr. Roberto Vega',
            'especialidad' => 'Cirugía',
            'correo'       => 'roberto@ralahpets.com',
            'telefono'     => '12345678',
        ];
        $this->assertSame('El teléfono del veterinario debe contener exactamente 9 dígitos.', CatalogoValidator::validateVeterinario($data));
    }

    public function testValidateVeterinarioRejectsEmptyNameOrSpecialty(): void
    {
        $data = [
            'nombre'       => '',
            'especialidad' => 'Cirugía',
            'correo'       => 'roberto@ralahpets.com',
            'telefono'     => '999888777',
        ];
        $this->assertNotNull(CatalogoValidator::validateVeterinario($data));

        $data['nombre'] = 'Dr. Roberto Vega';
        $data['especialidad'] = '';
        $this->assertNotNull(CatalogoValidator::validateVeterinario($data));
    }
}