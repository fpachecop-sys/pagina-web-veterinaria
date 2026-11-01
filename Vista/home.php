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


    <!-- Header -->


    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h2>Bienestar real para quienes hacen de tu casa un hogar.</h2>
            <p>El bienestar de tu mascota es nuestra prioridad. Ofrecemos atención veterinaria de calidad con profesionales dedicados y tecnología de punta.</p>
            <div class="hero-buttons">
                <a href="#servicios" class="btn-hero btn-hero-primary">
                    Ver Servicios
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
        <?php
        $servicios = [
            ['nombre' => 'Medicina General', 'imagenes' => ['assets/img/servicios/medicina-general', 'assets/img/medicina-general'], 'icono' => 'fa-stethoscope'],
            ['nombre' => 'Cirugía Especializada', 'imagenes' => ['assets/img/servicios/cirugia-especializada', 'assets/img/cirugia-especializada'], 'icono' => 'fa-kit-medical'],
            ['nombre' => 'Medicina Complementaria', 'imagenes' => ['assets/img/servicios/medicina-complementaria', 'assets/img/medicina-complementaria'], 'icono' => 'fa-heart-pulse'],
            ['nombre' => 'Diagnóstico por Imágenes', 'imagenes' => ['assets/img/servicios/diagnostico-imagenes', 'assets/img/diagnostico-imagenes'], 'icono' => 'fa-x-ray'],
            ['nombre' => 'Laboratorio', 'imagenes' => ['assets/img/servicios/laboratorio', 'assets/img/laboratorio'], 'icono' => 'fa-microscope'],
            ['nombre' => 'Otros Servicios', 'imagenes' => ['assets/img/servicios/otros-servicios', 'assets/img/otros-servicios'], 'fallback' => 'assets/img/Imagen_CorteCabelloPerro', 'icono' => 'fa-pump-soap'],
        ];
        ?>
        <section id="servicios" class="services-section" style="scroll-margin-top: 100px;">
            <header class="services-heading">
                <p class="eyebrow">Somos veterinarios especialistas</p>
                <h2>Nuestros Servicios</h2>
                <p>Atención cercana y especializada para cada etapa de la vida de tu mascota.</p>
            </header>
            <div class="services-grid">
                <?php foreach ($servicios as $servicio): ?>
                    <?php
                    $imagenServicio = '';
                    foreach ($servicio['imagenes'] as $baseServicio) {
                        foreach (['jpg', 'jpeg', 'png', 'webp'] as $extensionServicio) {
                            $candidataServicio = $baseServicio . '.' . $extensionServicio;
                            if (is_file(__DIR__ . '/../' . $candidataServicio)) {
                                $imagenServicio = $candidataServicio;
                                break 2;
                            }
                        }
                    }
                    if ($imagenServicio === '' && isset($servicio['fallback'])) {
                        foreach (['jpg', 'jpeg', 'png', 'webp'] as $extensionServicio) {
                            $candidataServicio = $servicio['fallback'] . '.' . $extensionServicio;
                            if (is_file(__DIR__ . '/../' . $candidataServicio)) {
                                $imagenServicio = $candidataServicio;
                                break;
                            }
                        }
                    }
                    ?>
                    <article class="service-tile">
                        <div class="service-image-frame">
                            <?php if ($imagenServicio !== ''): ?>
                                <img src="<?= htmlspecialchars($imagenServicio, ENT_QUOTES, 'UTF-8') ?>"
                                     alt="Servicio veterinario: <?= htmlspecialchars($servicio['nombre'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                            <?php else: ?>
                                <div class="service-image-placeholder" aria-hidden="true">
                                    <i class="fas <?= htmlspecialchars($servicio['icono'], ENT_QUOTES, 'UTF-8') ?>"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h3><?= htmlspecialchars($servicio['nombre'], ENT_QUOTES, 'UTF-8') ?></h3>
                    </article>
                <?php endforeach; ?>
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
                <a href="#servicios" class="btn-hero btn-hero-primary">
                    Ver Servicios
                </a>
                <a href="index.php?entidad=ubicacion&accion=ver" class="btn-hero btn-hero-secondary">
                    Ver Ubicación
                </a>
            </div>
        </section>
    </div>

    <!-- Footer -->


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

        const revealItems = document.querySelectorAll(
            '.main-container > section, .stats-grid .stat-card, .services-grid .service-tile, .testimonials-grid .testimonial-card'
        );
        if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12 });

            revealItems.forEach(item => {
                item.classList.add('scroll-reveal');
                revealObserver.observe(item);
            });
        }
    </script>
