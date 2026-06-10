<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Sugerencias - Veterinaria Ralah Pets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        h1 {
            color: #f39c12;
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
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
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
        .btn-sm {
            padding: 6px 12px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <h1>💡 Gestión de Sugerencias</h1>
    
    <div class="table-wrapper">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Categoría</th>
                    <th>Sugerencia</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($datos)): ?>
                    <tr><td colspan="7" class="text-center">No hay sugerencias registradas</td></tr>
                <?php else: ?>
                    <?php foreach ($datos as $fila): ?>
                        <tr>
                            <td><strong>#<?= $fila['id'] ?></strong></td>
                            <td><?= date('d/m/Y', strtotime($fila['fecha_registro'])) ?></td>
                            <td><?= htmlspecialchars($fila['nombre']) ?></td>
                            <td><?= htmlspecialchars($fila['email']) ?></td>
                            <td><span class="badge bg-warning"><?= htmlspecialchars($fila['categoria']) ?></span></td>
                            <td style="max-width: 400px; text-align: left;">
                                <small><?= htmlspecialchars($fila['sugerencia']) ?></small>
                            </td>
                            <td>
                                <a href="index.php?entidad=sugerencia&accion=eliminar&id=<?= $fila['id'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('¿Eliminar esta sugerencia?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>