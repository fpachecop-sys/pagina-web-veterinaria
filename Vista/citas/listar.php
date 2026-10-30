<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Lista de Citas - Veterinaria Ralah Pets</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    h1 {
      color: #16a085;
      text-align: center;
      margin-bottom: 30px;
      font-weight: 700;
    }

    .btn-add {
      background: linear-gradient(135deg, #16a085 0%, #0e7664 100%);
      color: white;
      border: none;
      padding: 12px 30px;
      border-radius: 10px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(22, 160, 133, 0.3);
    }

    .btn-add:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(22, 160, 133, 0.4);
      color: white;
    }

    .table-wrapper {
      overflow-x: auto;
      border-radius: 12px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    table {
      background-color: white;
      margin-top: 20px;
      font-size: 14px;
    }

    thead th {
      background: linear-gradient(135deg, #16a085 0%, #0e7664 100%);
      color: white;
      font-weight: 600;
      text-align: center;
      padding: 18px 12px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-size: 12px;
    }

    tbody td {
      padding: 16px 12px;
      text-align: center;
      vertical-align: middle;
    }

    tbody tr {
      transition: background 0.2s ease;
    }

    tbody tr:hover {
      background: #f8f9fa;
    }

    .btn-sm {
      padding: 6px 12px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 11px;
      margin: 2px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      transition: all 0.2s ease;
    }

    .btn-warning {
      background: #f39c12;
      border: none;
    }

    .btn-warning:hover {
      background: #e67e22;
      transform: translateY(-2px);
    }

    .btn-danger {
      background: #e74c3c;
      border: none;
    }

    .btn-danger:hover {
      background: #c0392b;
      transform: translateY(-2px);
    }

    .btn-success {
      background: #27ae60;
      border: none;
    }

    .btn-success:hover {
      background: #1e8449;
      transform: translateY(-2px);
    }

    .btn-secondary {
      background: #95a5a6;
      border: none;
    }

    .btn-secondary:hover {
      background: #7f8c8d;
      transform: translateY(-2px);
    }

    .badge {
      padding: 8px 12px;
      border-radius: 8px;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
    }

    .badge.pendiente {
      background: #fff3cd;
      color: #856404;
    }

    .badge.atendida {
      background: #d4edda;
      color: #155724;
    }

    .badge.cancelada {
      background: #f8d7da;
      color: #721c24;
    }
  </style>
</head>
<body>

<h1>📅 Listado de Citas</h1>

<a href="index.php?entidad=cita&accion=mostrarFormularioAgregar" class="btn btn-add mb-3">
  ➕ Agendar Cita
</a>

<div class="table-wrapper">
  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>ID</th>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Mascota</th>
        <th>Dueño</th>
        <th>Veterinario</th>
        <th>Motivo</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($datos)): ?>
        <tr>
          <td colspan="9" class="text-center">No hay citas registradas</td>
        </tr>
      <?php else: ?>
        <?php foreach ($datos as $fila): ?>
        <tr>
          <td><?= htmlspecialchars($fila["id"]) ?></td>
          <td><?= date('d/m/Y', strtotime($fila["fecha"])) ?></td>
          <td><?= date('H:i', strtotime($fila["hora"])) ?></td>
          <td><strong><?= htmlspecialchars($fila["nombre_mascota"]) ?></strong></td>
          <td><?= htmlspecialchars($fila["nombre_dueno"]) ?></td>
          <td><?= htmlspecialchars($fila["nombre_veterinario"]) ?></td>
          <td><?= htmlspecialchars($fila["motivo"]) ?></td>
          <td>
            <span class="badge <?= strtolower($fila['estado']) ?>">
              <?= htmlspecialchars($fila["estado"]) ?>
            </span>
          </td>
          <td>
            <?php if ($fila['estado'] == 'Pendiente'): ?>
              <a href="index.php?entidad=cita&accion=cambiarEstado&id=<?= $fila['id'] ?>&estado=Atendida" 
                 class="btn btn-sm btn-success" title="Marcar como Atendida">✓</a>
              <a href="index.php?entidad=cita&accion=cambiarEstado&id=<?= $fila['id'] ?>&estado=Cancelada" 
                 class="btn btn-sm btn-secondary" title="Cancelar">✕</a>
            <?php endif; ?>
            <a href="index.php?entidad=cita&accion=mostrarFormularioEditar&id=<?= $fila['id'] ?>" 
               class="btn btn-sm btn-warning">Editar</a>
            <a href="index.php?entidad=cita&accion=eliminar&id=<?= $fila['id'] ?>" 
               class="btn btn-sm btn-danger" 
               onclick="return confirm('¿Eliminar esta cita?')">Eliminar</a>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>