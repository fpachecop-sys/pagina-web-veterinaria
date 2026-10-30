
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Libro de Reclamaciones - Veterinaria Ralah Pets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .reclamaciones-header {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        .reclamaciones-header h1 {
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .form-card {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .section-title {
            color: #16a085;
            font-weight: 600;
            font-size: 1.3em;
            margin: 30px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #16a085;
        }
        label {
            font-weight: 500;
            color: #2c3e50;
            font-size: 14px;
            margin-bottom: 8px;
        }
        input, select, textarea {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #16a085;
            box-shadow: 0 0 0 3px rgba(22, 160, 133, 0.1);
        }
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 20px;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
        }
        .info-box {
            background: #fff3cd;
            border-left: 4px solid #f39c12;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="reclamaciones-header">
        <h1>📝 LIBRO DE RECLAMACIONES</h1>
        <p>Conforme a lo establecido en el Código de Protección y Defensa del Consumidor</p>
    </div>

    <div class="form-card">
        <div class="info-box">
            <strong>⚠️ Información Importante:</strong> La formulación de un reclamo no impide acudir a otras vías de solución de controversias ni es requisito previo para interponer una denuncia ante el INDECOPI.
        </div>

        <form action="index.php?entidad=reclamacion&accion=guardar" method="POST">
            <div class="section-title">📋 Datos del Consumidor</div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tipo_documento">Tipo de Documento</label>
                    <select name="tipo_documento" id="tipo_documento" class="form-control" required>
                        <option value="DNI">DNI</option>
                        <option value="CE">Carnet de Extranjería</option>
                        <option value="Pasaporte">Pasaporte</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="numero_documento">Número de Documento</label>
                    <input type="text" name="numero_documento" id="numero_documento" class="form-control" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nombres">Nombres</label>
                    <input type="text" name="nombres" id="nombres" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" name="apellidos" id="apellidos" class="form-control" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" class="form-control" required>
            </div>

            <div class="section-title">📝 Detalle de la Reclamación</div>

            <div class="mb-3">
                <label for="tipo_reclamo">Tipo</label>
                <select name="tipo_reclamo" id="tipo_reclamo" class="form-control" required>
                    <option value="Reclamo">Reclamo - Disconformidad relacionada al servicio</option>
                    <option value="Queja">Queja - Disconformidad no relacionada al servicio</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="detalle">Detalle del Reclamo/Queja</label>
                <textarea name="detalle" id="detalle" class="form-control" rows="5" 
                          placeholder="Describa detalladamente su reclamo o queja..." required></textarea>
            </div>

            <div class="mb-3">
                <label for="pedido">Pedido (Opcional)</label>
                <textarea name="pedido" id="pedido" class="form-control" rows="3" 
                          placeholder="Indique qué solicita para resolver su reclamo..."></textarea>
            </div>

            <button type="submit" class="btn btn-submit">Enviar Reclamación</button>
            <a href="index.php?entidad=reclamacion&accion=listar" class="btn btn-secondary w-100 mt-2">Ver Reclamaciones Registradas (Admin)</a>
        </form>
    </div>
</body>
</html>