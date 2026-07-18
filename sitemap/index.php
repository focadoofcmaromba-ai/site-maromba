<?php

declare(strict_types=1);

require_once __DIR__ . '/config/configuracao.php';

header('Content-Type: application/xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;

$urlBase = rtrim($configuracao_site['url_base'], '/');

?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <sitemap>
        <loc><?= $urlBase ?>/sitemap-artigos.php</loc>
        <lastmod><?= date('Y-m-d') ?></lastmod>
    </sitemap>

    <sitemap>
        <loc><?= $urlBase ?>/sitemap-categorias.php</loc>
        <lastmod><?= date('Y-m-d') ?></lastmod>
    </sitemap>

    <sitemap>
        <loc><?= $urlBase ?>/sitemap-tags.php</loc>
        <lastmod><?= date('Y-m-d') ?></lastmod>
    </sitemap>

    <sitemap>
        <loc><?= $urlBase ?>/sitemap-autores.php</loc>
        <lastmod><?= date('Y-m-d') ?></lastmod>
    </sitemap>

</sitemapindex>