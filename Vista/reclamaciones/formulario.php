<style>
        .reclamaciones-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 36px 24px;
            border-radius: 15px;
            margin-bottom: 28px;
            text-align: center;
        }
        .reclamaciones-header h1 {
            font-size: clamp(1.7rem, 4vw, 2.4rem);
            font-weight: 700;
            margin-bottom: 10px;
        }
        .form-card {
            max-width: 960px;
            margin: 0 auto;
        }
        .section-title {
            color: var(--primary-dark);
            font-weight: 600;
            font-size: 1.3em;
            margin: 30px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary);
        }
        .reclamaciones-page label {
            display: block;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 7px;
        }
        .reclamaciones-page input,
        .reclamaciones-page select,
        .reclamaciones-page textarea {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid var(--light);
            border-radius: 8px;
            background: var(--white);
            font: inherit;
            transition: var(--transition);
        }
        .reclamaciones-page input:focus,
        .reclamaciones-page select:focus,
        .reclamaciones-page textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(22, 160, 133, 0.1);
        }
        .btn-submit {
            width: 100%;
            display: block;
            margin-top: 20px;
        }
        .info-box {
            background: rgba(243, 156, 18, 0.12);
            border-left: 4px solid var(--warning);
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .reclamacion-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0 20px;
        }
        @media (max-width: 640px) {
            .reclamacion-grid { grid-template-columns: 1fr; }
            .reclamaciones-header { padding: 28px 18px; }
        }
    </style>




    <div class="main-container reclamaciones-page">
<div class="reclamaciones-header">
        <h1>📝 LIBRO DE RECLAMACIONES</h1>
        <p>Conforme a lo establecido en el Código de Protección y Defensa del Consumidor</p>
    </div>

    <div class="form-card">
        <div class="info-box">
            <strong>⚠️ Información Importante:</strong> La formulación de un reclamo no impide acudir a otras vías de solución de controversias ni es requisito previo para interponer una denuncia ante el INDECOPI.
        </div>

        <form action="index.php?entidad=reclamacion&accion=guardar" method="POST">
            <?= \App\Support\Csrf::field() ?>
            <div class="section-title">📋 Datos del Consumidor</div>
            
            <div class="reclamacion-grid">
                <div class="form-group">
                    <label for="tipo_documento">Tipo de Documento</label>
                    <select name="tipo_documento" id="tipo_documento" class="form-control" required>
                        <option value="DNI">DNI</option>
                        <option value="CE">Carnet de Extranjería</option>
                        <option value="Pasaporte">Pasaporte</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="numero_documento">Número de Documento</label>
                    <input type="text" name="numero_documento" id="numero_documento" class="form-control" required>
                </div>
            </div>

            <div class="reclamacion-grid">
                <div class="form-group">
                    <label for="nombres">Nombres</label>
                    <input type="text" name="nombres" id="nombres" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" name="apellidos" id="apellidos" class="form-control" required>
                </div>
            </div>

            <div class="reclamacion-grid">
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" class="form-control" required>
            </div>

            <div class="section-title">📝 Detalle de la Reclamación</div>

            <div class="form-group">
                <label for="tipo_reclamo">Tipo</label>
                <select name="tipo_reclamo" id="tipo_reclamo" class="form-control" required>
                    <option value="Reclamo">Reclamo - Disconformidad relacionada al servicio</option>
                    <option value="Queja">Queja - Disconformidad no relacionada al servicio</option>
                </select>
            </div>

            <div class="form-group">
                <label for="detalle">Detalle del Reclamo/Queja</label>
                <textarea name="detalle" id="detalle" class="form-control" rows="5" 
                          placeholder="Describa detalladamente su reclamo o queja..." required></textarea>
            </div>

            <div class="mb-3">
                <label for="pedido">Pedido (Opcional)</label>
                <textarea name="pedido" id="pedido" class="form-control" rows="3" 
                          placeholder="Indique qué solicita para resolver su reclamo..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-submit">Enviar Reclamación</button>
        </form>
    </div>
</div>
