<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Veterinaria Ralah Pets</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 450px;
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
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: var(--primary);
            font-size: 2em;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: var(--gray);
            font-size: 1em;
        }
        
        .login-icon {
            font-size: 4em;
            margin-bottom: 15px;
        }
        
        .form-divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }
        
        .form-divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--light);
        }
        
        .form-divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            color: var(--gray);
            font-size: 0.9em;
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--light);
        }
        
        .register-link a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }
        
        .register-link a:hover {
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
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">🐾</div>
                <h1>Bienvenido</h1>
                <p>Inicia sesión en tu cuenta</p>
            </div>
            
            <?php
            if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

            if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <form action="index.php?entidad=auth&accion=procesarLogin" method="POST">
                <div class="form-group">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        placeholder="tucorreo@ejemplo.com"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="••••••••"
                        required
                    >
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    Iniciar Sesión
                </button>
            </form>
            
            <div class="register-link">
                <p>¿No tienes una cuenta? 
                    <a href="index.php?entidad=auth&accion=mostrarRegistro">Regístrate aquí</a>
                </p>
            </div>
            
            <div class="back-home">
                <a href="index.php">← Volver al inicio</a>
            </div>
        </div>
    </div>
</body>
</html>