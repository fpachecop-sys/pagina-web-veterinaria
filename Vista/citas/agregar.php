<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agendar Cita - Veterinaria Ralah Pets</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card {
      max-width: 700px;
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

    input, select, textarea {
      padding: 14px 16px;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      font-size: 15px;
      transition: all 0.3s ease;
    }

    input:focus, select:focus, textarea:focus {
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

    .row {
      margin-bottom: 0;
    }
  </style>
</head>
<body>

<div class="card">
  <h2>📅 Agendar Nueva Cita</h2>
  <form action="index.php?entidad=cita&accion=guardarNuevo" method="POST">
    <div class="row">
      <div class="col-md-6 mb-3">
        <label for="fecha" class="form-label">Fecha</label>
        <input type="date" name="fecha" id="fecha" class="form-control" 
               min="<?= date('Y-m-d') ?>" required>
      </div>

      <div class="col-md-6 mb-3">
        <label for="hora" class="form-label">Hora</label>
        <input type="time" name="hora" id="hora" class="form-control" required>
      </div>
    </div>

    <div class="mb-3">
      <label for="id_mascota" class="form-label">Mascota</label>
      <select name="id_mascota" id="id_mascota" class="form-control" required>
        <option value="">--Seleccione una mascota--</option>
        <?php foreach ($mascotas as $mascota): ?>
          <option value="<?= htmlspecialchars($mascota['id']) ?>">
            <?= htmlspecialchars($mascota['nombre']) ?> 
            (Dueño: <?= htmlspecialchars($mascota['nombre_dueno']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label for="id_veterinario" class="form-label">Veterinario</label>
      <select name="id_veterinario" id="id_veterinario" class="form-control" required>
        <option value="">--Seleccione un veterinario--</option>
        <?php foreach ($veterinarios as $vet): ?>
          <option value="<?= htmlspecialchars($vet['id']) ?>">
            <?= htmlspecialchars($vet['nombre']) ?> 
            (<?= htmlspecialchars($vet['especialidad']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label for="motivo" class="form-label">Motivo de la Consulta</label>
      <textarea name="motivo" id="motivo" class="form-control" rows="3" 
                placeholder="Describa el motivo de la cita..." required></textarea>
    </div>

    <button type="submit" class="btn btn-success">Agendar Cita</button>
    <a href="index.php?entidad=cita&accion=listar" class="btn btn-secondary">Cancelar</a>
  </form>
</div>

</body>
</html>