<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\Csrf;
use App\Support\Helper;
use App\Support\Router;
use App\Validators\AuthValidator;
use PHPUnit\Framework\TestCase;

final class AuthAndSecurityTest extends TestCase
{
    public function testValidatorAcceptsValidRegistration(): void
    {
        $data = [
            'nombre'             => 'Franco',
            'apellido'           => 'Pacheco',
            'email'              => 'franco@example.com',
            'dni'                => '75085925',
            'telefono'           => '978475665',
            'direccion'          => 'Lima Norte',
            'password'           => 'ClaveSegura123',
            'confirmar_password' => 'ClaveSegura123',
        ];

        $this->assertNull(AuthValidator::validateRegistro($data));
    }

    public function testValidatorRejectsInvalidDniAndPhone(): void
    {
        $data = [
            'nombre'             => 'Franco',
            'apellido'           => 'Pacheco',
            'email'              => 'franco@example.com',
            'dni'                => '123', // Invalido
            'telefono'           => '978475665',
            'direccion'          => 'Lima',
            'password'           => '123456',
            'confirmar_password' => '123456',
        ];

        $this->assertNotNull(AuthValidator::validateRegistro($data));
    }

    public function testValidatorRejectsMismatchedPasswords(): void
    {
        $data = [
            'nombre'             => 'Franco',
            'apellido'           => 'Pacheco',
            'email'              => 'franco@example.com',
            'dni'                => '75085925',
            'telefono'           => '978475665',
            'direccion'          => 'Lima',
            'password'           => '123456',
            'confirmar_password' => '654321',
        ];

        $this->assertSame('Las contraseñas no coinciden.', AuthValidator::validateRegistro($data));
    }

    public function testRouterClassifiesPublicAndAdminRoutes(): void
    {
        $this->assertTrue(Router::isPublicRoute('auth', 'mostrarLogin'));
        $this->assertFalse(Router::isPublicRoute('dueno', 'eliminar'));

        $this->assertTrue(Router::isAdminRoute('dueno', 'eliminar'));
        $this->assertFalse(Router::isAdminRoute('usuario', 'perfil'));
    }

    public function testHelperEscapesXssPayloads(): void
    {
        $dirty = '<script>alert("xss")</script>';
        $this->assertSame('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', Helper::e($dirty));
    }

    public function testCsrfTokenGenerationAndVerification(): void
    {
        $token = Csrf::token();
        $this->assertNotEmpty($token);
        $this->assertTrue(Csrf::verify($token));
        $this->assertFalse(Csrf::verify('token_falso_123'));
    }
}
