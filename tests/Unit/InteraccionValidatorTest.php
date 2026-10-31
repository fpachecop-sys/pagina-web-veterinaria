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

    public function testValidateReclamacionRejectsInvalidDni(): void
    {
        $data = [
            'nombre_cliente' => 'Franco Pacheco',
            'dni_cliente'    => '1234',
            'email_cliente'  => 'cliente@test.com',
            'tipo'           => 'Queja',
            'detalle'        => 'Queja sobre atención.',
        ];
        $this->assertNotNull(InteraccionValidator::validateReclamacion($data));
    }

    public function testValidateSugerenciaRequiresMessage(): void
    {
        $data = [
            'nombre_cliente' => 'Franco',
            'email_cliente'  => 'cliente@test.com',
            'mensaje'        => '',
        ];
        $this->assertNotNull(InteraccionValidator::validateSugerencia($data));
    }

    public function testValidateVideoRequiresValidUrl(): void
    {
        $data = [
            'titulo' => 'Consejos para cachorros',
            'url'    => 'url_invalida',
        ];
        $this->assertNotNull(InteraccionValidator::validateVideo($data));
    }
}