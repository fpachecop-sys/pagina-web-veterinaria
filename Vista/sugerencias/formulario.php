<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buzón de Sugerencias - Veterinaria Ralah Pets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sugerencias-header {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        .sugerencias-header h1 {
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .form-card {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
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
            border-color: #f39c12;
            box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.1);
        }
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
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
            box-shadow: 0 5px 15px rgba(243, 156, 18, 0.3);
        }
    </style>
</head>
<body>
    <div class="sugerencias-header">
        <h1>💡 BUZÓN DE SUGERENCIAS</h1>
        <p>Tu opinión es importante para nosotros. ¡Ayúdanos a mejorar!</p>
    </div>

    <div class="form-card">
        <form action="index.php?entidad=sugerencia&accion=guardar" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nombre">Nombre Completo *</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email">Correo Electrónico *</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="telefono">Teléfono (Opcional)</label>
                    <input type="text" name="telefono" id="telefono" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="categoria">Categoría *</label>
                    <select name="categoria" id="categoria" class="form-control" required>
                        <option value="">Seleccione una categoría</option>
                        <option value="Atención al Cliente">Atención al Cliente</option>
                        <option value="Servicios Veterinarios">Servicios Veterinarios</option>
                        <option value="Instalaciones">Instalaciones</option>
                        <option value="Productos">Productos</option>
                        <option value="Precios">Precios</option>
                        <option value="Página Web">Página Web</option>
                        <option value="Otros">Otros</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="sugerencia">Tu Sugerencia *</label>
                <textarea name="sugerencia" id="sugerencia" class="form-control" rows="6" 
                          placeholder="Cuéntanos tu idea, sugerencia o comentario..." required></textarea>
            </div>

            <button type="submit" class="btn btn-submit">Enviar Sugerencia</button>
            <a href="index.php?entidad=sugerencia&accion=listar" class="btn btn-secondary w-100 mt-2">Ver Sugerencias (Admin)</a>
        </form>
    </div>
</body>
</html>