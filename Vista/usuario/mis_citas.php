<style>
        .citas-timeline {
            max-width: 900px;
            margin: 30px auto;
        }
        
        .cita-item {
            background: white;
            border-left: 4px solid var(--primary);
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .cita-item:hover {
            transform: translateX(5px);
        }
        
        .cita-item.pendiente {
            border-left-color: #f39c12;
            background: linear-gradient(to right, rgba(243, 156, 18, 0.05) 0%, white 20%);
        }
        
        .cita-item.atendida {
            border-left-color: #27ae60;
            background: linear-gradient(to right, rgba(39, 174, 96, 0.05) 0%, white 20%);
        }
        
        .cita-item.cancelada {
            border-left-color: #e74c3c;
            background: linear-gradient(to right, rgba(231, 76, 60, 0.05) 0%, white 20%);
            opacity: 0.7;
        }
        
        .cita-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .cita-fecha {
            font-size: 1.3em;
            font-weight: 700;
            color: var(--primary);
        }
        
        .cita-hora {
            font-size: 1.1em;
            color: var(--gray);
        }
        
        .cita-info {
            margin: 10px 0;
            display: flex;
            gap: 10px;
        }
        
        .cita-label {
            font-weight: 600;
            color: var(--dark);
            min-width: 120px;
        }
        
        .cita-value {
            color: var(--gray);
        }
        
        .no-citas {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, rgba(22, 160, 133, 0.05) 0%, rgba(14, 118, 100, 0.05) 100%);
            border-radius: 15px;
        }
    </style>


    <!-- Header -->
    

    <!-- Main Container -->
    <div class="main-container">
        <h2 style="color: var(--primary); margin-bottom: 30px;">📅 Mis Citas</h2>
        
        <?php if (empty($citas)): ?>
            <div class="no-citas">
                <div style="font-size: 5em; margin-bottom: 20px;">📅</div>
                <h3 style="color: var(--primary); margin-bottom: 15px;">No tienes citas registradas</h3>
                <p style="color: var(--gray); font-size: 1.1em; margin-bottom: 20px;">
                    Contacta con nosotros para agendar una cita para tu mascota
                </p>
                <a href="index.php?entidad=ubicacion&accion=ver" class="btn btn-primary">
                    Contactar Ahora
                </a>
            </div>
        <?php else: ?>
            <div class="citas-timeline">
                <?php foreach ($citas as $cita): ?>
                    <div class="cita-item <?= strtolower($cita['estado']) ?>">
                        <div class="cita-header">
                            <div>
                                <div class="cita-fecha">
                                    📅 <?= date('d/m/Y', strtotime($cita['fecha'])) ?>
                                </div>
                                <div class="cita-hora">
                                    🕐 <?= date('H:i', strtotime($cita['hora'])) ?>
                                </div>
                            </div>
                            <span class="badge badge-<?= strtolower($cita['estado']) == 'pendiente' ? 'warning' : (strtolower($cita['estado']) == 'atendida' ? 'success' : 'danger') ?>">
                                <?= htmlspecialchars($cita['estado']) ?>
                            </span>
                        </div>
                        
                        <div class="cita-info">
                            <span class="cita-label">🐾 Mascota:</span>
                            <span class="cita-value"><strong><?= htmlspecialchars($cita['nombre_mascota']) ?></strong></span>
                        </div>
                        
                        <div class="cita-info">
                            <span class="cita-label">⚕️ Veterinario:</span>
                            <span class="cita-value"><?= htmlspecialchars($cita['nombre_veterinario']) ?></span>
                        </div>
                        
                        <div class="cita-info">
                            <span class="cita-label">📋 Motivo:</span>
                            <span class="cita-value"><?= htmlspecialchars($cita['motivo']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="card" style="margin-top: 40px; text-align: center; background: linear-gradient(135deg, rgba(22, 160, 133, 0.05) 0%, rgba(14, 118, 100, 0.05) 100%);">
                <p style="font-size: 1.1em; color: var(--dark); margin-bottom: 15px;">
                    ¿Necesitas agendar una nueva cita? Contáctanos
                </p>
                <a href="index.php?entidad=ubicacion&accion=ver" class="btn btn-primary">
                    📞 Contactar
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
