<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Lista de Mascotas - Veterinaria Ralah Pets</title>
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
    }

    thead th {
      background: linear-gradient(135deg, #16a085 0%, #0e7664 100%);
      color: white;
      font-weight: 600;
      text-align: center;
      padding: 18px 15px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-size: 13px;
    }

    tbody td {
      padding: 16px 15px;
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
      padding: 8px 16px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 13px;
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

    .badge {
      padding: 8px 12px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 600;
    }

    .badge.bg-primary {
      background: #3498db !important;
    }

    .badge.bg-success {
      background: #27ae60 !important;
    }

    .badge.bg-info {
      background: #16a085 !important;
    }
  </style>
</head>
<body>

<h1>🐾 Listado de Mascotas</h1>

<a href="index.php?entidad=mascota&accion=mostrarFormularioAgregar" class="btn btn-add mb-3">
  ➕ Agregar Mascota
</a>

<div class="table-wrapper">
  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Especie</th>
        <th>Raza</th>
        <th>Edad</th>
        <th>Dueño</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($datos)): ?>
        <tr>
          <td colspan="7" class="text-center">No hay mascotas registradas</td>
        </tr>
      <?php else: ?>
        <?php foreach ($datos as $fila): ?>
        <tr>
          <td><?= htmlspecialchars($fila["id"]) ?></td>
          <td><strong><?= htmlspecialchars($fila["nombre"]) ?></strong></td>
          <td>
            <?php 
              $badgeClass = '';
              if ($fila["especie"] == 'Perro') $badgeClass = 'bg-primary';
              elseif ($fila["especie"] == 'Gato') $badgeClass = 'bg-success';
              else $badgeClass = 'bg-info';
            ?>
            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($fila["especie"]) ?></span>
          </td>
          <td><?= htmlspecialchars($fila["raza"]) ?></td>
          <td><?= htmlspecialchars($fila["edad"]) ?> años</td>
          <td><?= htmlspecialchars($fila["nombre_dueno"]) ?></td>
          <td>
            <a href="index.php?entidad=mascota&accion=mostrarFormularioEditar&id=<?= $fila['id'] ?>" 
               class="btn btn-sm btn-warning">Editar</a>
            <a href="index.php?entidad=mascota&accion=eliminar&id=<?= $fila['id'] ?>" 
               class="btn btn-sm btn-danger" 
               onclick="return confirm('¿Eliminar esta mascota?')">Eliminar</a>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>