<?php

declare(strict_types=1);

require_once __DIR__ . '/config/configuracao.php';
require_once __DIR__ . '/config/conexao.php';

header('Content-Type: application/xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;

$urlBase = rtrim($configuracao_site['url_base'], '/');

$consulta = $conexao->query("
    SELECT
        slug,
        data_ultima_atualizacao
    FROM artigos
    WHERE status = 'publicado'
    ORDER BY data_publicacao DESC
");

?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

<?php if ($consulta): ?>

<?php while ($artigo = $consulta->fetch_assoc()): ?>

    <url>
        <loc><?= $urlBase ?>/artigos/<?= htmlspecialchars($artigo['slug'], ENT_XML1, 'UTF-8') ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($artigo['data_ultima_atualizacao'])) ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.80</priority>
    </url>

<?php endwhile; ?>

<?php endif; ?>

</urlset>