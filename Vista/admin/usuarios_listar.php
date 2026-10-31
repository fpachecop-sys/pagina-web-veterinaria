<!-- Header -->
    

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
                                    <form action="index.php?entidad=admin&accion=cambiarEstadoUsuario" method="POST" style="display:inline" onsubmit="return confirm('¿Cambiar estado de este usuario?')">
                                      <?= \App\Support\Csrf::field() ?>
                                      <input type="hidden" name="id" value="<?= (int) $usuario['id'] ?>">
                                      <input type="hidden" name="activo" value="<?= $usuario['activo'] ? 0 : 1 ?>">
                                      <button type="submit" class="btn btn-sm btn-<?= $usuario['activo'] ? 'warning' : 'success' ?>">
                                        <?= $usuario['activo'] ? 'Desactivar' : 'Activar' ?>
                                      </button>
                                    </form>
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
