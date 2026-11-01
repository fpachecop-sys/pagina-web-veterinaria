<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\Csrf;
use App\Support\Env;
use App\Support\Helper;
use App\Support\Router;
use App\Support\Session;
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

    public function testValidatorAcceptsConfirmationFieldUsedByRegistrationForm(): void
    {
        $data = [
            'nombre'           => 'Franco',
            'apellido'         => 'Pacheco',
            'email'            => 'franco@example.com',
            'dni'              => '75085925',
            'telefono'         => '978475665',
            'direccion'        => '',
            'password'         => 'ClaveSegura123',
            'password_confirm' => 'ClaveSegura123',
        ];

        $this->assertNull(AuthValidator::validateRegistro($data));
    }

    public function testValidatorAcceptsPasswordConfirmarAlias(): void
    {
        $data = [
            'nombre'             => 'Mariano',
            'apellido'           => 'Poemape',
            'email'              => 'mariano@example.com',
            'dni'                => '75085925',
            'telefono'           => '',
            'direccion'          => 'Los Olivos',
            'password'           => '123456',
            'password_confirmar' => '123456',
        ];

        $this->assertNull(AuthValidator::validateRegistro($data));
    }

    public function testValidatorRejectsMissingPasswordConfirmation(): void
    {
        $data = [
            'nombre'    => 'Franco',
            'apellido'  => 'Pacheco',
            'email'     => 'franco@example.com',
            'dni'       => '75085925',
            'telefono'  => '978475665',
            'direccion' => 'Lima Norte',
            'password'  => 'ClaveSegura123',
        ];

        $this->assertSame(
            'La confirmación de contraseña es obligatoria.',
            AuthValidator::validateRegistro($data)
        );
    }

    public function testValidatorRejectsEmptyOrLongNameAndSurname(): void
    {
        $base = [
            'nombre'             => '',
            'apellido'           => 'Pacheco',
            'email'              => 'franco@example.com',
            'dni'                => '75085925',
            'password'           => '123456',
            'confirmar_password' => '123456',
        ];
        $this->assertSame('El nombre es obligatorio y no debe superar los 100 caracteres.', AuthValidator::validateRegistro($base));

        $base['nombre'] = 'Franco';
        $base['apellido'] = str_repeat('A', 101);
        $this->assertSame('El apellido es obligatorio y no debe superar los 100 caracteres.', AuthValidator::validateRegistro($base));
    }

    public function testValidatorRejectsInvalidEmailFormat(): void
    {
        $data = [
            'nombre'             => 'Franco',
            'apellido'           => 'Pacheco',
            'email'              => 'correo-sin-arroba',
            'dni'                => '75085925',
            'password'           => '123456',
            'confirmar_password' => '123456',
        ];

        $this->assertSame('Ingresa un correo electrónico válido.', AuthValidator::validateRegistro($data));
    }

    public function testValidatorRejectsInvalidDniAndPhone(): void
    {
        $data = [
            'nombre'             => 'Franco',
            'apellido'           => 'Pacheco',
            'email'              => 'franco@example.com',
            'dni'                => '123',
            'telefono'           => '978475665',
            'direccion'          => 'Lima',
            'password'           => '123456',
            'confirmar_password' => '123456',
        ];
        $this->assertSame('El DNI debe contener exactamente 8 dígitos numéricos.', AuthValidator::validateRegistro($data));

        $data['dni'] = '75085925';
        $data['telefono'] = '12345';
        $this->assertSame('El teléfono debe contener exactamente 9 dígitos numéricos.', AuthValidator::validateRegistro($data));
    }

    public function testValidatorRejectsMismatchedAndShortPasswords(): void
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

        $data['password'] = '12345';
        $data['confirmar_password'] = '12345';
        $this->assertSame('La contraseña debe tener al menos 6 caracteres.', AuthValidator::validateRegistro($data));
    }

    public function testValidatePerfilSuccessAndRejections(): void
    {
        $valid = [
            'nombre'    => 'Franco',
            'apellido'  => 'Pacheco',
            'dni'       => '75085925',
            'telefono'  => '978475665',
            'direccion' => 'Av. Naranjal 1647',
        ];
        $this->assertNull(AuthValidator::validatePerfil($valid));

        $invalidDni = $valid;
        $invalidDni['dni'] = 'ABC12345';
        $this->assertSame('El DNI debe tener exactamente 8 dígitos.', AuthValidator::validatePerfil($invalidDni));

        $invalidPhone = $valid;
        $invalidPhone['telefono'] = '999';
        $this->assertSame('El teléfono debe tener exactamente 9 dígitos.', AuthValidator::validatePerfil($invalidPhone));
    }

    public function testValidatePasswordStrengthBoundary(): void
    {
        $this->assertNotNull(AuthValidator::validatePasswordStrength('12345'));
        $this->assertNull(AuthValidator::validatePasswordStrength('123456'));
    }

    public function testRouterClassifiesPublicAndAdminRoutes(): void
    {
        $this->assertTrue(Router::isPublicRoute('auth', 'mostrarLogin'));
        $this->assertTrue(Router::isPublicRoute('video', 'verPublico'));
        $this->assertFalse(Router::isPublicRoute('dueno', 'eliminar'));

        $this->assertTrue(Router::isAdminRoute('dueno', 'eliminar'));
        $this->assertTrue(Router::isAdminRoute('reporte', 'ver'));
        $this->assertFalse(Router::isAdminRoute('usuario', 'perfil'));
    }

    public function testHelperEscapesXssPayloadsAndNullValues(): void
    {
        $dirty = '<script>alert("xss")</script>';
        $this->assertSame('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', Helper::e($dirty));
        $this->assertSame('', Helper::e(null));
    }

    public function testCsrfTokenGenerationAndVerification(): void
    {
        $token = Csrf::token();
        $this->assertNotEmpty($token);
        $this->assertSame(64, strlen($token));
        $this->assertTrue(Csrf::verify($token));
        $this->assertFalse(Csrf::verify('token_falso_123'));
        $this->assertFalse(Csrf::verify(null));
        $this->assertFalse(Csrf::verify(''));
    }

    public function testCsrfFieldRendersHiddenInput(): void
    {
        $html = Csrf::field();
        $this->assertStringContainsString('<input type="hidden" name="csrf_token" value="', $html);
        $this->assertStringContainsString(Csrf::token(), $html);
    }

    public function testSessionSetGetRemoveAndFlashLifecycle(): void
    {
        Session::start();
        Session::set('clave_prueba', 'valor_123');
        $this->assertSame('valor_123', Session::get('clave_prueba'));

        Session::remove('clave_prueba');
        $this->assertSame('por_defecto', Session::get('clave_prueba', 'por_defecto'));

        Session::setFlash('aviso_test', 'Mensaje temporal');
        $this->assertSame('Mensaje temporal', Session::getFlash('aviso_test'));
        $this->assertNull(Session::getFlash('aviso_test'));
    }

    public function testEnvReturnsDefaultForUndefinedKey(): void
    {
        $this->assertSame('valor_default', Env::get('VARIABLE_INEXISTENTE_XYZ', 'valor_default'));
        $this->assertIsBool(Env::isDebug());
    }
}