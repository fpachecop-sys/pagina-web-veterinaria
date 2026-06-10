<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Reclamaciones - Veterinaria X</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        h1 {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
        }
        .table-wrapper {
            overflow-x: auto;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        table {
            background-color: white;
            font-size: 13px;
        }
        thead th {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            font-weight: 600;
            text-align: center;
            padding: 15px 10px;
            font-size: 12px;
        }
        tbody td {
            padding: 12px 10px;
            text-align: center;
            vertical-align: middle;
        }
        tbody tr:hover {
            background: #f8f9fa;
        }
        .badge {
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge.pendiente {
            background: #fff3cd;
            color: #856404;
        }
        .badge.en-proceso {
            background: #cce5ff;
            color: #004085;
        }
        .badge.resuelta {
            background: #d4edda;
            color: #155724;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 11px;
            margin: 2px;
        }
    </style>
</head>
<body>
    <h1>📋 Gestión de Reclamaciones</h1>
    
    <div class="table-wrapper">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Documento</th>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Detalle</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($datos)): ?>
                    <tr><td colspan="8" class="text-center">No hay reclamaciones registradas</td></tr>
                <?php else: ?>
                    <?php foreach ($datos as $fila): ?>
                        <tr>
                            <td><strong>#<?= $fila['id'] ?></strong></td>
                            <td><?= date('d/m/Y', strtotime($fila['fecha_registro'])) ?></td>
                            <td><?= htmlspecialchars($fila['tipo_documento']) ?>: <?= htmlspecialchars($fila['numero_documento']) ?></td>
                            <td><?= htmlspecialchars($fila['nombres'] . ' ' . $fila['apellidos']) ?></td>
                            <td><span class="badge bg-warning"><?= htmlspecialchars($fila['tipo_reclamo']) ?></span></td>
                            <td style="max-width: 300px; text-align: left;">
                                <small><?= htmlspecialchars(substr($fila['detalle'], 0, 100)) ?>...</small>
                            </td>
                            <td>
                                <span class="badge <?= strtolower(str_replace(' ', '-', $fila['estado'])) ?>">
                                    <?= htmlspecialchars($fila['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($fila['estado'] == 'Pendiente'): ?>
                                    <a href="index.php?entidad=reclamacion&accion=cambiarEstado&id=<?= $fila['id'] ?>&estado=En Proceso" 
                                       class="btn btn-sm btn-primary" title="Marcar en proceso">⏳</a>
                                    <a href="index.php?entidad=reclamacion&accion=cambiarEstado&id=<?= $fila['id'] ?>&estado=Resuelta" 
                                       class="btn btn-sm btn-success" title="Marcar como resuelta">✓</a>
                                <?php elseif ($fila['estado'] == 'En Proceso'): ?>
                                    <a href="index.php?entidad=reclamacion&accion=cambiarEstado&id=<?= $fila['id'] ?>&estado=Resuelta" 
                                       class="btn btn-sm btn-success" title="Marcar como resuelta">✓</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
