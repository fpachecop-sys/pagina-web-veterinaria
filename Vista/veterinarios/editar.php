<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Veterinario - Veterinaria Ralah Pets</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card {
      max-width: 600px;
      margin: 0 auto;
      box-shadow: 0 10px 40px rgba(0,0,0,0.15);
      border-radius: 20px;
      border: none;
      padding: 20px;
    }

    h2 {
      color: #16a085;
      text-align: center;
      margin-bottom: 30px;
      font-weight: 700;
    }

    label {
      font-weight: 500;
      color: #2c3e50;
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 8px;
    }

    input {
      padding: 14px 16px;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      font-size: 15px;
      transition: all 0.3s ease;
    }

    input:focus {
      outline: none;
      border-color: #16a085;
      box-shadow: 0 0 0 4px rgba(22, 160, 133, 0.1);
    }

    .btn-success {
      width: 100%;
      background: linear-gradient(135deg, #16a085 0%, #0e7664 100%);
      border: none;
      padding: 14px;
      border-radius: 10px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(22, 160, 133, 0.3);
    }

    .btn-success:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(22, 160, 133, 0.4);
    }

    .btn-secondary {
      width: 100%;
      margin-top: 10px;
      padding: 14px;
      border-radius: 10px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      transform: translateY(-2px);
    }
  </style>
</head>
<body>

<div class="card">
  <h2>✏️ Editar Veterinario</h2>
  <form action="index.php?entidad=veterinario&accion=actualizar" method="POST">
    <input type="hidden" name="id" value="<?= htmlspecialchars($veterinario['id']) ?>">

    <div class="mb-3">
      <label for="nombre" class="form-label">Nombre Completo</label>
      <input type="text" name="nombre" id="nombre" class="form-control" 
             value="<?= htmlspecialchars($veterinario['nombre']) ?>" required>
    </div>

    <div class="mb-3">
      <label for="especialidad" class="form-label">Especialidad</label>
      <input type="text" name="especialidad" id="especialidad" class="form-control" 
             value="<?= htmlspecialchars($veterinario['especialidad']) ?>" required>
    </div>

    <div class="mb-3">
      <label for="correo" class="form-label">Correo Electrónico</label>
      <input type="email" name="correo" id="correo" class="form-control" 
             value="<?= htmlspecialchars($veterinario['correo']) ?>" required>
    </div>

    <div class="mb-3">
      <label for="telefono" class="form-label">Teléfono</label>
      <input type="text" name="telefono" id="telefono" class="form-control" 
             pattern="[0-9]{9}" 
             value="<?= htmlspecialchars($veterinario['telefono']) ?>" required>
    </div>

    <button type="submit" class="btn btn-success">Guardar Cambios</button>
    <a href="index.php?entidad=veterinario&accion=listar" class="btn btn-secondary">Cancelar</a>
  </form>
</div>

</body>
</html>