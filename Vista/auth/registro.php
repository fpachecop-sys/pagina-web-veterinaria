<?php
\App\Support\Session::start();
$oldRegister = \App\Support\Session::get('old_register', []);
\App\Support\Session::remove('old_register');
$oldRegister = is_array($oldRegister) ? $oldRegister : [];
$escapeOld = static fn (string $key): string => htmlspecialchars(
    (string) ($oldRegister[$key] ?? ''),
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);
?>
<style>
        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: auto;
            padding: 40px 20px;
        }
        
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
            padding: 40px;
            animation: slideIn 0.5s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .register-header h1 {
            color: var(--primary);
            font-size: 2em;
            margin-bottom: 10px;
        }
        
        .register-header p {
            color: var(--gray);
            font-size: 1em;
        }
        
        .register-icon {
            font-size: 4em;
            margin-bottom: 15px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--light);
        }
        
        .login-link a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .back-home {
            text-align: center;
            margin-top: 15px;
        }
        
        .back-home a {
            color: var(--gray);
            text-decoration: none;
            font-size: 0.9em;
        }
        
        .back-home a:hover {
            color: var(--primary);
        }
        
        .required-field::after {
            content: ' *';
            color: var(--danger);
        }
    </style>


    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="register-icon">🐾</div>
                <h1>Crear Cuenta</h1>
                <p>Regístrate para acceder a todos nuestros servicios</p>
            </div>
            
            <form action="index.php?entidad=auth&accion=procesarRegistro" method="POST">
                <?= \App\Support\Csrf::field() ?>
                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre" class="form-label required-field">Nombre</label>
                        <input 
                            type="text" 
                            id="nombre" 
                            name="nombre" 
                            class="form-control" 
                            placeholder="Juan"
                            value="<?= $escapeOld('nombre') ?>"
                            autocomplete="given-name"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="apellido" class="form-label required-field">Apellido</label>
                        <input 
                            type="text" 
                            id="apellido" 
                            name="apellido" 
                            class="form-control" 
                            placeholder="Pérez"
                            value="<?= $escapeOld('apellido') ?>"
                            autocomplete="family-name"
                            required
                        >
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="dni" class="form-label required-field">DNI</label>
                        <input 
                            type="text" 
                            id="dni" 
                            name="dni" 
                            class="form-control" 
                            placeholder="12345678"
                            pattern="[0-9]{8}"
                            title="Debe ingresar 8 dígitos"
                            value="<?= $escapeOld('dni') ?>"
                            autocomplete="off"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input 
                            type="text" 
                            id="telefono" 
                            name="telefono" 
                            class="form-control" 
                            placeholder="987654321"
                            pattern="[0-9]{9}"
                            title="Debe ingresar 9 dígitos"
                            value="<?= $escapeOld('telefono') ?>"
                            autocomplete="tel"
                        >
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label required-field">Correo Electrónico</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        placeholder="tucorreo@ejemplo.com"
                        value="<?= $escapeOld('email') ?>"
                        autocomplete="email"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input 
                        type="text" 
                        id="direccion" 
                        name="direccion" 
                        class="form-control" 
                        placeholder="Av. Principal 123, Lima"
                        value="<?= $escapeOld('direccion') ?>"
                        autocomplete="street-address"
                    >
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="form-label required-field">Contraseña</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            placeholder="••••••••"
                            minlength="6"
                            autocomplete="new-password"
                            required
                        >
                        <small style="color: var(--gray); font-size: 0.85em;">Mínimo 6 caracteres</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirm" class="form-label required-field">Confirmar Contraseña</label>
                        <input 
                            type="password" 
                            id="password_confirm" 
                            name="confirmar_password" 
                            class="form-control" 
                            placeholder="••••••••"
                            minlength="6"
                            autocomplete="new-password"
                            required
                        >
                        <small id="password-match-message" aria-live="polite" style="color: var(--danger); font-size: 0.85em;"></small>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    Registrarse
                </button>
            </form>
            
            <div class="login-link">
                <p>¿Ya tienes una cuenta? 
                    <a href="index.php?entidad=auth&accion=mostrarLogin">Inicia sesión aquí</a>
                </p>
            </div>
            
            <div class="back-home">
                <a href="index.php">← Volver al inicio</a>
            </div>
        </div>
    </div>
    
    <script>
        const registerForm = document.querySelector('form');
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirm');
        const matchMessage = document.getElementById('password-match-message');

        function validatePasswordMatch() {
            const mismatch = confirmInput.value !== '' && passwordInput.value !== confirmInput.value;
            confirmInput.setCustomValidity(mismatch ? 'Las contraseñas no coinciden.' : '');
            matchMessage.textContent = mismatch ? 'Las contraseñas no coinciden.' : '';
            return !mismatch;
        }

        passwordInput.addEventListener('input', validatePasswordMatch);
        confirmInput.addEventListener('input', validatePasswordMatch);
        registerForm.addEventListener('submit', function (event) {
            if (!validatePasswordMatch()) {
                event.preventDefault();
                confirmInput.reportValidity();
                confirmInput.focus();
            }
        });
    </script>
