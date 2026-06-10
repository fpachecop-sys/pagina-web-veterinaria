<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Mascota - Veterinaria Ralah Pets</title>
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

    input, select {
      padding: 14px 16px;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      font-size: 15px;
      transition: all 0.3s ease;
    }

    input:focus, select:focus {
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
  <h2>✏️ Editar Mascota</h2>
  <form action="index.php?entidad=mascota&accion=actualizar" method="POST">
    <input type="hidden" name="id" value="<?= htmlspecialchars($mascota['id']) ?>">

    <div class="mb-3">
      <label for="nombre" class="form-label">Nombre de la Mascota</label>
      <input type="text" name="nombre" id="nombre" class="form-control" 
             value="<?= htmlspecialchars($mascota['nombre']) ?>" required>
    </div>

    <div class="mb-3">
      <label for="especie" class="form-label">Especie</label>
      <select name="especie" id="especie" class="form-control" required>
        <option value="Perro" <?= $mascota['especie']=='Perro'?'selected':'' ?>>Perro</option>
        <option value="Gato" <?= $mascota['especie']=='Gato'?'selected':'' ?>>Gato</option>
        <option value="Ave" <?= $mascota['especie']=='Ave'?'selected':'' ?>>Ave</option>
        <option value="Otro" <?= $mascota['especie']=='Otro'?'selected':'' ?>>Otro</option>
      </select>
    </div>

    <div class="mb-3">
      <label for="raza" class="form-label">Raza</label>
      <input type="text" name="raza" id="raza" class="form-control" 
             value="<?= htmlspecialchars($mascota['raza']) ?>" required>
    </div>

    <div class="mb-3">
      <label for="edad" class="form-label">Edad (años)</label>
      <input type="number" name="edad" id="edad" class="form-control" 
             min="0" max="30" value="<?= htmlspecialchars($mascota['edad']) ?>" required>
    </div>

    <div class="mb-3">
      <label for="dni_dueno" class="form-label">Dueño</label>
      <select name="dni_dueno" id="dni_dueno" class="form-control" required>
        <?php foreach ($duenos as $dueno): ?>
          <option value="<?= htmlspecialchars($dueno['dni']) ?>" 
                  <?= $mascota['dni_dueno']==$dueno['dni']?'selected':'' ?>>
            <?= htmlspecialchars($dueno['nombre']) ?> (DNI: <?= htmlspecialchars($dueno['dni']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Guardar Cambios</button>
    <a href="index.php?entidad=mascota&accion=listar" class="btn btn-secondary">Cancelar</a>
  </form>
</div>

</body>
</html>