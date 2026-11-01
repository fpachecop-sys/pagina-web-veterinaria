<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Validators\InteraccionValidator;
use PHPUnit\Framework\TestCase;

final class InteraccionValidatorTest extends TestCase
{
    public function testValidateReclamacionSuccess(): void
    {
        $data = [
            'nombre_cliente'   => 'Franco Pacheco',
            'dni_cliente'      => '75085925',
            'email_cliente'    => 'cliente@test.com',
            'telefono_cliente' => '978475665',
            'tipo'             => 'Reclamo',
            'detalle'          => 'Detalle del reclamo sobre la atención recibida.',
        ];
        $this->assertNull(InteraccionValidator::validateReclamacion($data));
    }

    public function testValidateReclamacionSupportsAlternativeDocumentTypesAndFieldNames(): void
    {
        $data = [
            'nombres'          => 'Carlos Mendoza',
            'tipo_documento'   => 'CE',
            'numero_documento' => '001234567',
            'email'            => 'carlos@test.com',
            'telefono'         => '987654321',
            'tipo_reclamo'     => 'Queja',
            'detalle'          => 'Demora en recepción.',
        ];
        $this->assertNull(InteraccionValidator::validateReclamacion($data));
    }

    public function testValidateReclamacionRejectsInvalidDni(): void
    {
        $data = [
            'nombre_cliente' => 'Franco Pacheco',
            'dni_cliente'    => '1234',
            'email_cliente'  => 'cliente@test.com',
            'tipo'           => 'Queja',
            'detalle'        => 'Queja sobre atención.',
        ];
        $this->assertSame('El DNI debe contener exactamente 8 dígitos numéricos.', InteraccionValidator::validateReclamacion($data));
    }

    public function testValidateReclamacionRejectsInvalidEmailPhoneAndType(): void
    {
        $base = [
            'nombre_cliente'   => 'Franco Pacheco',
            'dni_cliente'      => '75085925',
            'email_cliente'    => 'email-malo',
            'telefono_cliente' => '978475665',
            'tipo'             => 'Reclamo',
            'detalle'          => 'Detalle válido.',
        ];
        $this->assertSame('Debe ingresar un correo electrónico válido.', InteraccionValidator::validateReclamacion($base));

        $base['email_cliente'] = 'cliente@test.com';
        $base['telefono_cliente'] = '123';
        $this->assertSame('El teléfono debe contener entre 7 y 15 dígitos numéricos.', InteraccionValidator::validateReclamacion($base));

        $base['telefono_cliente'] = '978475665';
        $base['tipo'] = 'Consulta';
        $this->assertSame('El tipo debe ser Reclamo o Queja.', InteraccionValidator::validateReclamacion($base));
    }

    public function testValidateReclamacionRejectsEmptyDetail(): void
    {
        $data = [
            'nombre_cliente' => 'Franco Pacheco',
            'dni_cliente'    => '75085925',
            'email_cliente'  => 'cliente@test.com',
            'tipo'           => 'Reclamo',
            'detalle'        => '',
        ];
        $this->assertSame('El detalle es obligatorio (máximo 1000 caracteres).', InteraccionValidator::validateReclamacion($data));
    }

    public function testValidateSugerenciaSuccess(): void
    {
        $data = [
            'nombre'     => 'Franco Pacheco',
            'email'      => 'franco@test.com',
            'sugerencia' => 'Excelente atención, sugiero ampliar horario los domingos.',
        ];
        $this->assertNull(InteraccionValidator::validateSugerencia($data));
    }

    public function testValidateSugerenciaRequiresNameAndMessage(): void
    {
        $this->assertSame(
            'El nombre es obligatorio (máximo 100 caracteres).',
            InteraccionValidator::validateSugerencia(['nombre' => '', 'mensaje' => 'Hola'])
        );

        $this->assertSame(
            'El mensaje de sugerencia es obligatorio.',
            InteraccionValidator::validateSugerencia(['nombre' => 'Franco', 'mensaje' => ''])
        );
    }

    public function testValidateSugerenciaRejectsInvalidOptionalEmail(): void
    {
        $data = [
            'nombre'  => 'Franco',
            'email'   => 'correo_invalido',
            'mensaje' => 'Sugerencia de prueba',
        ];
        $this->assertSame('El correo electrónico no es válido.', InteraccionValidator::validateSugerencia($data));
    }

    public function testValidateVideoAcceptsValidHttpUrl(): void
    {
        $data = [
            'titulo'    => 'Cuidados dentales en perros',
            'url_video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ];
        $this->assertNull(InteraccionValidator::validateVideo($data));
    }

    public function testValidateVideoAllowsEmptyUrlWhenUploadingLocalFile(): void
    {
        $data = [
            'titulo'    => 'Promoción de baños medicados',
            'url_video' => '',
        ];
        $this->assertNull(InteraccionValidator::validateVideo($data, true));
        $this->assertNotNull(InteraccionValidator::validateVideo($data, false));
    }

    public function testValidateVideoRejectsInvalidOrNonHttpUrl(): void
    {
        $this->assertNotNull(InteraccionValidator::validateVideo([
            'titulo' => 'Consejos para cachorros',
            'url'    => 'url_invalida',
        ]));

        $this->assertSame(
            'Debe ingresar una URL http o https válida para el video.',
            InteraccionValidator::validateVideo([
                'titulo' => 'Video inseguro',
                'url'    => 'javascript:alert(1)',
            ])
        );
    }
}