<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria Ralah Pets - Cuidamos a tu mejor amigo</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin: 50px 0;
        }
        
        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            font-size: 3.5em;
            margin-bottom: 15px;
        }
        
        .feature-card h3 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.4em;
        }
        
        .feature-card p {
            color: var(--gray);
            line-height: 1.6;
        }
        
        .testimonials {
            background: linear-gradient(135deg, rgba(22, 160, 133, 0.1) 0%, rgba(14, 118, 100, 0.1) 100%);
            padding: 60px 20px;
            border-radius: 20px;
            margin: 50px 0;
        }
        
        .testimonials h2 {
            text-align: center;
            color: var(--primary);
            font-size: 2.5em;
            margin-bottom: 40px;
        }
        
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .testimonial-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .testimonial-text {
            font-style: italic;
            color: var(--dark);
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .testimonial-author {
            font-weight: 600;
            color: var(--primary);
        }
        
        .cta-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 60px 20px;
            border-radius: 20px;
            text-align: center;
            margin: 50px 0;
        }
        
        .cta-section h2 {
            font-size: 2.5em;
            margin-bottom: 20px;
        }
        
        .cta-section p {
            font-size: 1.2em;
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin: 50px 0;
        }
        
        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 3em;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: var(--gray);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="header-container">
            <a href="index.php" class="logo">
                <span style="font-size: 2em;">🐾</span>
                <h1>Ralah Pets</h1>
            </a>
            <nav class="main-nav">
                <a href="#quienes-somos" class="nav-link">¿Quiénes Somos?</a>
                <a href="index.php?entidad=veterinario&accion=verPublico" class="nav-link">Veterinarios</a>
                <a href="index.php?entidad=ubicacion&accion=ver" class="nav-link">Ubicación</a>
                <a href="index.php?entidad=video&accion=verPublico" class="nav-link">Videos</a>
                <a href="index.php?entidad=auth&accion=mostrarLogin" class="btn-login">Iniciar Sesión</a>
                <a href="index.php?entidad=auth&accion=mostrarRegistro" class="btn-register">Registrarse</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h2>🐾 Bienvenido a Veterinaria Ralah Pets</h2>
            <p>El bienestar de tu mascota es nuestra prioridad. Ofrecemos atención veterinaria de calidad con profesionales dedicados y tecnología de punta.</p>
            <div class="hero-buttons">
                <a href="index.php?entidad=auth&accion=mostrarRegistro" class="btn-hero btn-hero-primary">
                    Registrarme Ahora
                </a>
                <a href="#quienes-somos" class="btn-hero btn-hero-secondary">
                    Conocer Más
                </a>
            </div>
        </div>
    </section>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Quiénes Somos -->
        <section id="quienes-somos" style="scroll-margin-top: 100px;">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">¿Quiénes Somos?</h2>
                </div>
                <p style="font-size: 1.1em; line-height: 1.8; color: var(--dark);">
                    <strong>Veterinaria Ralah Pets</strong> es un centro veterinario comprometido con la salud y el bienestar de tus mascotas. Contamos con más de 10 años de experiencia brindando servicios de calidad, atención personalizada y un equipo de profesionales altamente capacitados.
                </p>
                <p style="font-size: 1.1em; line-height: 1.8; color: var(--dark); margin-top: 15px;">
                    Nuestra misión es proporcionar atención veterinaria integral con calidez humana, utilizando tecnología moderna y tratamientos especializados para garantizar la mejor calidad de vida de tus compañeros peludos.
                </p>
            </div>
        </section>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">3+</div>
                <div class="stat-label">Años de Experiencia</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">500+</div>
                <div class="stat-label">Mascotas Atendidas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">3+</div>
                <div class="stat-label">Veterinarios Especialistas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Atención Emergencias</div>
            </div>
        </div>

        <!-- Nuestros Servicios -->
        <section>
            <h2 style="text-align: center; color: var(--primary); font-size: 2.5em; margin-bottom: 40px;">
                Nuestros Servicios
            </h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🏥</div>
                    <h3>Consultas Generales</h3>
                    <p>Evaluación completa del estado de salud de tu mascota con diagnóstico profesional.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💉</div>
                    <h3>Vacunación</h3>
                    <p>Programas completos de vacunación para proteger a tu mascota de enfermedades.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔬</div>
                    <h3>Laboratorio</h3>
                    <p>Análisis clínicos y pruebas diagnósticas con equipos de última generación.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">✂️</div>
                    <h3>Cirugías</h3>
                    <p>Procedimientos quirúrgicos especializados con anestesia segura y recuperación monitoreada.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🛁</div>
                    <h3>Baño y Peluquería</h3>
                    <p>Servicios de estética y grooming para mantener a tu mascota limpia y hermosa.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🏠</div>
                    <h3>Atención a Domicilio</h3>
                    <p>Llevamos nuestros servicios veterinarios hasta la comodidad de tu hogar.</p>
                </div>
            </div>
        </section>

        <!-- Testimonios -->
        <section class="testimonials">
            <h2>Lo que dicen nuestros clientes</h2>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <p class="testimonial-text">
                        "Excelente atención, el Dr. Vega salvó la vida de mi perrito. Muy profesionales y cariñosos con las mascotas."
                    </p>
                    <p class="testimonial-author">- María López</p>
                </div>
                <div class="testimonial-card">
                    <p class="testimonial-text">
                        "El mejor lugar para cuidar a tu mascota. Instalaciones modernas y personal altamente calificado."
                    </p>
                    <p class="testimonial-author">- Carlos Rodríguez</p>
                </div>
                <div class="testimonial-card">
                    <p class="testimonial-text">
                        "Llevo a mis tres gatos desde hace años. Siempre un trato excepcional y precios justos."
                    </p>
                    <p class="testimonial-author">- Ana Martínez</p>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="cta-section">
            <h2>¿Listo para cuidar de tu mascota?</h2>
            <p>Únete a nuestra comunidad y accede a todos nuestros servicios veterinarios de calidad.</p>
            <div class="hero-buttons">
                <a href="index.php?entidad=auth&accion=mostrarRegistro" class="btn-hero btn-hero-primary">
                    Crear mi Cuenta
                </a>
                <a href="index.php?entidad=ubicacion&accion=ver" class="btn-hero btn-hero-secondary">
                    Ver Ubicación
                </a>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <p><strong>© <?= date('Y') ?> Veterinaria Ralah Pets</strong></p>
            <p>Los Olivos Huandoy, San Martin de Porres - Lima, Perú</p>
            <p>📞 943 626 841 | 📧 info@veterinariaralah.com</p>
            <p style="margin-top: 15px;">
                <a href="index.php?entidad=reclamacion&accion=mostrarFormulario" style="color: var(--primary); text-decoration: none;">📝 Libro de Reclamaciones</a> | 
                <a href="index.php?entidad=sugerencia&accion=mostrarFormulario" style="color: var(--primary); text-decoration: none;">💡 Buzón de Sugerencias</a>
            </p>
        </div>
    </footer>

    <script>
        // Smooth scroll para los enlaces internos
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>