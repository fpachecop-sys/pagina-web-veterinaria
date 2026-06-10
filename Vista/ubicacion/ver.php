<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ubicación - Veterinaria Ralah Pets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .ubicacion-header {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        .ubicacion-header h1 {
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .info-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .contact-card {
            background: linear-gradient(135deg, #16a085 0%, #0e7664 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .contact-card:hover {
            transform: translateY(-5px);
        }
        .contact-card i {
            font-size: 2.5em;
            margin-bottom: 15px;
        }
        .contact-card h3 {
            font-size: 1.2em;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .contact-card p {
            margin: 5px 0;
            font-size: 1em;
        }
        .map-container {
            width: 100%;
            height: 500px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        .schedule-table {
            margin: 20px 0;
        }
        .schedule-table table {
            width: 100%;
        }
        .schedule-table th {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 12px;
            text-align: center;
        }
        .schedule-table td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="ubicacion-header">
        <h1>📍 NUESTRA UBICACIÓN</h1>
        <p>Encuéntranos fácilmente y ven a visitarnos</p>
    </div>

    <div class="contact-grid">
        <div class="contact-card">
            <div>📍</div>
            <h3>Dirección</h3>
            <p>Los Olivos Huandoy</p>
            <p>San Martin de Porres, Lima - Perú</p>
        </div>
        <div class="contact-card">
            <div>📞</div>
            <h3>Teléfonos</h3>
            
            <p>943 626 841</p>
        </div>
        <div class="contact-card">
            <div>📧</div>
            <h3>Email</h3>
            <p>info@veterinariaRalah.com</p>
            <p>contacto@veterinariaRalah.com</p>
        </div>
        <div class="contact-card">
            <div>🕐</div>
            <h3>Emergencias 24/7</h3>
            <p>Línea Directa:</p>
            <p>943 626 841</p>
        </div>
    </div>

    <div class="info-section">
        <h2 style="color: #16a085; text-align: center; margin-bottom: 20px;">⏰ Horario de Atención</h2>
        <div class="schedule-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Día</th>
                        <th>Horario</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Lunes - Viernes</strong></td>
                        <td>9:00 AM - 11:00 PM</td>
                    </tr>
                    <tr>
                        <td><strong>Sábados</strong></td>
                        <td>9:00 AM - 10:00 PM</td>
                    </tr>
                    <tr>
                        <td><strong>Domingos y Feriados</strong></td>
                        <td>10:00 AM - 3:00 PM</td>
                    </tr>
                    <tr style="background: #fff3cd;">
                        <td colspan="2"><strong>🚨 Emergencias: Disponible 24/7</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="info-section">
        <h2 style="color: #16a085; text-align: center; margin-bottom: 20px;">🗺️ Mapa de Ubicación</h2>
        <div class="map-container">
            <!-- Google Maps - Reemplaza con tu ubicación real -->
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3902.97884415627!2d-77.08846302405198!3d-11.97596564058836!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9105ce13526c2bb3%3A0x54a86e3f9d04d0b2!2sRalah!5e0!3m2!1ses!2spe!4v1763828957505!5m2!1ses!2spe" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <p><strong>🚗 Cómo llegar:</strong></p>
            <p>A 2 cuadras del Ovalo Huandoy</p>
            <p>Estacionamiento disponible para clientes</p>
        </div>
    </div>

    <div class="info-section" style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); border: 2px solid #4caf50;">
        <h3 style="color: #2e7d32; text-align: center; margin-bottom: 15px;">🚀 Servicios Especiales</h3>
        <div class="row text-center">
            <div class="col-md-4 mb-3">
                <h5>🏠 Atención a Domicilio</h5>
                <p>Servicio disponible en Lima Metropolitana</p>
            </div>
            <div class="col-md-4 mb-3">
                <h5>🚑 Ambulancia Veterinaria</h5>
                <p>Para emergencias y traslados</p>
            </div>
            <div class="col-md-4 mb-3">
                <h5>📱 Telemedicina</h5>
                <p>Consultas virtuales disponibles</p>
            </div>
        </div>
    </div>
</body>
</html>