<div class="main-container media-public-page">
    <header class="media-public-heading">
        <p class="eyebrow">Ralah Pets · Consejos y novedades</p>
        <h1><i class="fas fa-photo-film" aria-hidden="true"></i> Videos y Promociones</h1>
        <p>Conoce nuestros servicios, mira recomendaciones para el cuidado de tu mascota y descubre nuestras ofertas.</p>
    </header>

    <section class="media-section" aria-labelledby="promotions-title">
        <div class="media-section-heading">
            <div>
                <p class="eyebrow">Ofertas para consentirlos</p>
                <h2 id="promotions-title">Promociones</h2>
            </div>
            <i class="fas fa-tag" aria-hidden="true"></i>
        </div>
        <?php if (empty($promociones)): ?>
            <p class="media-empty-state">Pronto publicaremos nuevas promociones.</p>
        <?php else: ?>
            <div class="promotion-grid">
                <?php foreach ($promociones as $promocion): ?>
                    <article class="promotion-card">
                        <img src="<?= htmlspecialchars((string) $promocion['imagen'], ENT_QUOTES, 'UTF-8') ?>"
                             alt="<?= htmlspecialchars((string) $promocion['titulo'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                        <div class="promotion-card-copy">
                            <span class="content-category">Promoción</span>
                            <h3><?= htmlspecialchars((string) $promocion['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p><?= htmlspecialchars((string) $promocion['descripcion'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="media-section" aria-labelledby="videos-title">
        <div class="media-section-heading">
            <div>
                <p class="eyebrow">Aprende con Ralah Pets</p>
                <h2 id="videos-title">Videos</h2>
            </div>
            <i class="fas fa-circle-play" aria-hidden="true"></i>
        </div>
        <?php if (empty($videosPublicos)): ?>
            <p class="media-empty-state">Pronto agregaremos nuevos videos para ti.</p>
        <?php else: ?>
            <div class="videos-grid">
                <?php foreach ($videosPublicos as $video): ?>
                    <?php
                    $url = (string) ($video['url_video'] ?? '');
                    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
                    $urlWeb = in_array($scheme, ['http', 'https'], true);
                    $urlEmbed = Video::urlParaEmbed($url);
                    ?>
                    <article class="video-card">
                        <div class="video-container">
                            <?php if (Video::esVideoDirecto($video)): ?>
                                <video controls preload="metadata" playsinline>
                                    <source src="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>">
                                    Tu navegador no puede reproducir este video.
                                </video>
                            <?php elseif ($urlWeb): ?>
                                <iframe src="<?= htmlspecialchars($urlEmbed, ENT_QUOTES, 'UTF-8') ?>"
                                        title="<?= htmlspecialchars((string) $video['titulo'], ENT_QUOTES, 'UTF-8') ?>"
                                        loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen></iframe>
                            <?php else: ?>
                                <div class="content-preview-placeholder"><i class="fas fa-circle-play" aria-hidden="true"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="video-info">
                            <span class="content-category"><?= htmlspecialchars((string) $video['categoria'], ENT_QUOTES, 'UTF-8') ?></span>
                            <h3><?= htmlspecialchars((string) $video['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p><?= htmlspecialchars((string) ($video['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>
