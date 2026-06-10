<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Veterinaria Ralah Pets</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="header-container">
            <a href="index.php?entidad=admin&accion=dashboard" class="logo">
                <span style="font-size: 2em;">🐾</span>
                <h1>Ralah Pets - Admin</h1>
            </a>
            <nav class="main-nav">
                <a href="index.php?entidad=admin&accion=dashboard" class="nav-link">Dashboard</a>
                <a href="index.php?entidad=admin&accion=listarUsuarios" class="nav-link active">Usuarios</a>
                <a href="index.php?entidad=auth&accion=logout" class="btn-login">Cerrar Sesión</a>
            </nav>
        </div>
    </header>

    <!-- Main Container -->
    <div class="main-container">
        <h2 style="color: var(--primary); margin-bottom: 30px;">👥 Gestión de Usuarios</h2>
        
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>DNI</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr><td colspan="8" class="text-center">No hay usuarios registrados</td></tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= $usuario['id'] ?></td>
                            <td><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></td>
                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                            <td><?= htmlspecialchars($usuario['dni']) ?></td>
                            <td>
                                <span class="badge badge-<?= $usuario['rol'] == 'administrador' ? 'danger' : 'primary' ?>">
                                    <?= ucfirst($usuario['rol']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?= $usuario['activo'] ? 'success' : 'secondary' ?>">
                                    <?= $usuario['activo'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></td>
                            <td>
                                <?php if ($usuario['rol'] != 'administrador'): ?>
                                    <a href="index.php?entidad=admin&accion=cambiarEstadoUsuario&id=<?= $usuario['id'] ?>&activo=<?= $usuario['activo'] ? 0 : 1 ?>" 
                                       class="btn btn-sm btn-<?= $usuario['activo'] ? 'warning' : 'success' ?>"
                                       onclick="return confirm('¿Cambiar estado de este usuario?')">
                                        <?= $usuario['activo'] ? 'Desactivar' : 'Activar' ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <p><strong>© <?= date('Y') ?> Veterinaria Ralah Pets</strong></p>
        </div>
    </footer>
</body>
</html>