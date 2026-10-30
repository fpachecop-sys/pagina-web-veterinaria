<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reportes - Veterinaria Ralah Pets</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    h1 {
      color: #16a085;
      text-align: center;
      margin-bottom: 40px;
      font-weight: 700;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 40px;
    }

    .stat-card {
      padding: 25px;
      border-radius: 15px;
      color: white;
      box-shadow: 0 5px 20px rgba(0,0,0,0.15);
      transition: transform 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
    }

    .stat-card.primary {
      background: linear-gradient(135deg, #16a085 0%, #0e7664 100%);
    }

    .stat-card.secondary {
      background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    }

    .stat-card.warning {
      background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
    }

    .stat-card.success {
      background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
    }

    .stat-label {
      font-size: 14px;
      font-weight: 500;
      opacity: 0.9;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 10px;
    }

    .stat-value {
      font-size: 2.5em;
      font-weight: 700;
      margin-bottom: 5px;
    }

    .chart-container {
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }

    .chart-container h3 {
      color: #16a085;
      margin-bottom: 20px;
      font-weight: 600;
    }

    .table-wrapper {
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }

    .table-wrapper h3 {
      color: #16a085;
      margin-bottom: 20px;
      font-weight: 600;
    }

    table {
      width: 100%;
    }

    thead th {
      background: linear-gradient(135deg, #16a085 0%, #0e7664 100%);
      color: white;
      font-weight: 600;
      padding: 15px;
      text-align: center;
    }

    tbody td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #f0f0f0;
    }

    tbody tr:hover {
      background: #f8f9fa;
    }

    .badge {
      padding: 8px 12px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 600;
    }
  </style>
</head>
<body>

<h1>📊 Reportes y Estadísticas</h1>

<!-- Estadísticas Generales -->
<div class="stats-grid">
  <div class="stat-card primary">
    <div class="stat-label">Total Mascotas</div>
    <div class="stat-value"><?= $estadisticas['total_mascotas'] ?></div>
  </div>

  <div class="stat-card secondary">
    <div class="stat-label">Total Dueños</div>
    <div class="stat-value"><?= $estadisticas['total_duenos'] ?></div>
  </div>

  <div class="stat-card warning">
    <div class="stat-label">Total Veterinarios</div>
    <div class="stat-value"><?= $estadisticas['total_veterinarios'] ?></div>
  </div>

  <div class="stat-card success">
    <div class="stat-label">Total Citas</div>
    <div class="stat-value"><?= $estadisticas['total_citas'] ?></div>
  </div>
</div>

<!-- Estados de Citas -->
<div class="chart-container">
  <h3>📅 Estados de Citas</h3>
  <div class="row">
    <div class="col-md-6">
      <canvas id="citasChart"></canvas>
    </div>
    <div class="col-md-6">
      <table class="table">
        <thead>
          <tr>
            <th>Estado</th>
            <th>Cantidad</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><span class="badge" style="background: #fff3cd; color: #856404;">Pendientes</span></td>
            <td><strong><?= $estadisticas['citas_pendientes'] ?></strong></td>
          </tr>
          <tr>
            <td><span class="badge" style="background: #d4edda; color: #155724;">Atendidas</span></td>
            <td><strong><?= $estadisticas['citas_atendidas'] ?></strong></td>
          </tr>
          <tr>
            <td><span class="badge" style="background: #f8d7da; color: #721c24;">Canceladas</span></td>
            <td><strong><?= $estadisticas['citas_canceladas'] ?></strong></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Mascotas por Especie -->
<div class="chart-container">
  <h3>🐾 Mascotas por Especie</h3>
  <canvas id="especiesChart" height="80"></canvas>
</div>

<!-- Veterinarios Más Solicitados -->
<div class="table-wrapper">
  <h3>⚕️ Veterinarios Más Solicitados</h3>
  <table class="table">
    <thead>
      <tr>
        <th>Veterinario</th>
        <th>Especialidad</th>
        <th>Total Citas</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($veterinariosPopulares)): ?>
        <tr><td colspan="3">No hay datos disponibles</td></tr>
      <?php else: ?>
        <?php foreach ($veterinariosPopulares as $vet): ?>
        <tr>
          <td><strong><?= htmlspecialchars($vet['nombre']) ?></strong></td>
          <td><?= htmlspecialchars($vet['especialidad']) ?></td>
          <td><span class="badge" style="background: #3498db;"><?= $vet['total_citas'] ?></span></td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script>
// Gráfico de Estados de Citas
const ctxCitas = document.getElementById('citasChart').getContext('2d');
new Chart(ctxCitas, {
  type: 'doughnut',
  data: {
    labels: ['Pendientes', 'Atendidas', 'Canceladas'],
    datasets: [{
      data: [
        <?= $estadisticas['citas_pendientes'] ?>,
        <?= $estadisticas['citas_atendidas'] ?>,
        <?= $estadisticas['citas_canceladas'] ?>
      ],
      backgroundColor: ['#f39c12', '#27ae60', '#e74c3c'],
      borderWidth: 2
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        position: 'bottom'
      }
    }
  }
});

// Gráfico de Mascotas por Especie
const ctxEspecies = document.getElementById('especiesChart').getContext('2d');
new Chart(ctxEspecies, {
  type: 'bar',
  data: {
    labels: [<?php echo implode(',', array_map(function($e) { return "'".$e['especie']."'"; }, $mascotasPorEspecie)); ?>],
    datasets: [{
      label: 'Cantidad de Mascotas',
      data: [<?php echo implode(',', array_column($mascotasPorEspecie, 'cantidad')); ?>],
      backgroundColor: ['#16a085', '#3498db', '#f39c12', '#e74c3c'],
      borderWidth: 0
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          stepSize: 1
        }
      }
    },
    plugins: {
      legend: {
        display: false
      }
    }
  }
});
</script>

</body>
</html>