<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Video - Veterinaria Ralah Pets</title>
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
            color: #e74c3c;
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
            border-color: #e74c3c;
            box-shadow: 0 0 0 4px rgba(231, 76, 60, 0.1);
        }
        .btn-success {
            width: 100%;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
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
        .alert-info {
            background: #d1ecf1;
            border-left: 4px solid #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>🎥 Agregar Nuevo Video</h2>
        
        <div class="alert-info">
            <strong>💡 Nota:</strong> Para videos de YouTube, usa el formato embed: 
            <code>https://www.youtube.com/embed/VIDEO_ID</code>
        </div>

        <form action="index.php?entidad=video&accion=guardarNuevo" method="POST">
            <div class="mb-3">
                <label for="titulo" class="form-label">Título del Video</label>
                <input type="text" name="titulo" id="titulo" class="form-control" 
                       placeholder="Ej: Cuidados básicos para perros" required>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea name="descripcion" id="descripcion" class="form-control" rows="3"
                          placeholder="Describe brevemente el contenido del video..." required></textarea>
            </div>

            <div class="mb-3">
                <label for="url_video" class="form-label">URL del Video (Embed)</label>
                <input type="url" name="url_video" id="url_video" class="form-control" 
                       placeholder="https://www.youtube.com/embed/..." required>
            </div>

            <div class="mb-3">
                <label for="categoria" class="form-label">Categoría</label>
                <select name="categoria" id="categoria" class="form-control" required>
                    <option value="">--Seleccione--</option>
                    <option value="Servicios">Servicios</option>
                    <option value="Productos">Productos</option>
                    <option value="Cuidados">Cuidados y Tips</option>
                    <option value="Testimonios">Testimonios</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Guardar Video</button>
            <a href="index.php?entidad=video&accion=listar" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>