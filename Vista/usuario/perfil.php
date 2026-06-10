<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Veterinaria Ralah Pets</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .perfil-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .perfil-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .perfil-card h3 {
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--light);
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--light);
        }
        
        .info-label {
            font-weight: 600;
            color: var(--dark);
        }
        
        .info-value {
            color: var(--gray);
        }
        
        .edit-section {
            background: linear-gradient(135deg, rgba(22, 160, 133, 0.05) 0%, rgba(14, 118, 100, 0.05) 100%);
            padding: 25px;
            border-radius: 12px;
            margin-top: 20px;
        }
        
        @media (max-width: 768px) {
            .perfil-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="header-container">
            <a href="index.php?entidad=usuario&accion=dashboard" class="logo">
                <span style="font-size: 2em;">🐾</span>
                <h1>Ralah Pets</h1>
            </a>
            <nav class="main-nav">
                <a href="index.php?entidad=usuario&accion=dashboard" class="nav-link">Mi Panel</a>
                <a href="index.php?entidad=usuario&accion=verPerfil" class="nav-link active">Mi Perfil</a>
                <a href="index.php?entidad=usuario&accion=misMascotas" class="nav-link">Mis Mascotas</a>
                <a href="index.php?entidad=usuario&accion=misCitas" class="nav-link">Mis Citas</a>
                <a href="index.php?entidad=ubicacion&accion=ver" class="nav-link">Ubicación</a>
                <a href="index.php?entidad=video&accion=verPublico" class="nav-link">Videos</a>
                <a href="index.php?entidad=auth&accion=logout" class="btn-login">Cerrar Sesión</a>
            </nav>
        </div>
    </header>

    <!-- Main Container -->
    <div class="main-container">
        <h2 style="color: var(--primary); margin-bottom: 30px;">👤 Mi Perfil</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="perfil-container">
            <!-- Información Personal -->
            <div class="perfil-card">
                <h3>📋 Información Personal</h3>
                <div class="info-row">
                    <span class="info-label">Nombre:</span>
                    <span class="info-value"><?= htmlspecialchars($usuario['nombre']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Apellido:</span>
                    <span class="info-value"><?= htmlspecialchars($usuario['apellido']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">DNI:</span>
                    <span class="info-value"><?= htmlspecialchars($usuario['dni']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Correo:</span>
                    <span class="info-value"><?= htmlspecialchars($usuario['email']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Teléfono:</span>
                    <span class="info-value"><?= htmlspecialchars($usuario['telefono'] ?: 'No registrado') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Dirección:</span>
                    <span class="info-value"><?= htmlspecialchars($usuario['direccion'] ?: 'No registrada') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Miembro desde:</span>
                    <span class="info-value"><?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></span>
                </div>
            </div>

            <!-- Editar Perfil -->
            <div class="perfil-card">
                <h3>✏️ Editar Información</h3>
                <form action="index.php?entidad=usuario&accion=editarPerfil" method="POST">
                    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                    
                    <div class="form-group">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" 
                               value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input type="text" id="apellido" name="apellido" class="form-control" 
                               value="<?= htmlspecialchars($usuario['apellido']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="dni" class="form-label">DNI</label>
                        <input type="text" id="dni" name="dni" class="form-control" 
                               value="<?= htmlspecialchars($usuario['dni']) ?>" 
                               pattern="[0-9]{8}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" class="form-control" 
                               value="<?= htmlspecialchars($usuario['telefono']) ?>" 
                               pattern="[0-9]{9}">
                    </div>
                    
                    <div class="form-group">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" id="direccion" name="direccion" class="form-control" 
                               value="<?= htmlspecialchars($usuario['direccion']) ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        Guardar Cambios
                    </button>
                </form>
            </div>
        </div>

        <!-- Cambiar Contraseña -->
        <div class="edit-section">
            <h3 style="color: var(--primary); margin-bottom: 20px;">🔒 Cambiar Contraseña</h3>
            <form action="index.php?entidad=usuario&accion=cambiarPassword" method="POST" style="max-width: 600px;">
                <div class="form-group">
                    <label for="password_actual" class="form-label">Contraseña Actual</label>
                    <input type="password" id="password_actual" name="password_actual" 
                           class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password_nueva" class="form-label">Nueva Contraseña</label>
                    <input type="password" id="password_nueva" name="password_nueva" 
                           class="form-control" minlength="6" required>
                </div>
                
                <div class="form-group">
                    <label for="password_confirmar" class="form-label">Confirmar Nueva Contraseña</label>
                    <input type="password" id="password_confirmar" name="password_confirmar" 
                           class="form-control" minlength="6" required>
                </div>
                
                <button type="submit" class="btn btn-warning">
                    Cambiar Contraseña
                </button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <p><strong>© <?= date('Y') ?> Veterinaria Ralah Pets</strong></p>
            <p>Los Olivos Huandoy, San Martin de Porres - Lima, Perú</p>
            <p>📞 943 626 841 | 📧 info@veterinariaralah.com</p>
        </div>
    </footer>
</body>
</html>