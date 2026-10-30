<div class="container" style="max-width:1200px;margin:2rem auto;padding:0 1rem;">
    <div class="card" style="background:#fff;border-radius:12px;padding:2rem;box-shadow:0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="color:#1e293b;margin-bottom:0.5rem;"><i class="fas fa-user-md" style="color:#667eea;"></i> Nuestro Equipo Médico Veterinario</h2>
        <p style="color:#64748b;margin-bottom:2rem;">Profesionales comprometidos con la salud y el bienestar de tus mascotas.</p>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1.5rem;">
            <?php foreach ($veterinarios as $vet): ?>
                <div style="border:1px solid #e2e8f0;border-radius:10px;padding:1.5rem;text-align:center;">
                    <div style="width:64px;height:64px;border-radius:50%;background:#eef2ff;color:#4f46e5;display:flex;align-items:center;justify-content:center;font-size:1.75rem;margin:0 auto 1rem;">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <h3 style="margin:0 0 0.5rem;color:#1e293b;"><?= \App\Support\Helper::e($vet['nombre']) ?></h3>
                    <span style="display:inline-block;background:#e0e7ff;color:#3730a3;padding:0.25rem 0.75rem;border-radius:999px;font-size:0.85rem;font-weight:600;margin-bottom:1rem;">
                        <?= \App\Support\Helper::e($vet['especialidad']) ?>
                    </span>
                    <p style="margin:0.25rem 0;color:#475569;font-size:0.9rem;">
                        <i class="fas fa-envelope"></i> <?= \App\Support\Helper::e($vet['correo']) ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
