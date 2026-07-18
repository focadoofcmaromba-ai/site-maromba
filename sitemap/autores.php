<?php

declare(strict_types=1);

require_once __DIR__ . '/config/configuracao.php';
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/config/funcoes.php';

header('Content-Type: application/xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;

$urlBase = rtrim($configuracao_site['url_base'], '/');

$consulta = $conexao->query("
    SELECT
        id,
        nome,
        data_cadastro
    FROM autores
    ORDER BY nome ASC
");

?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

<?php if ($consulta): ?>

<?php while ($autor = $consulta->fetch_assoc()): ?>

<?php
$slugAutor = gerar_slug($autor['nome']) . '-' . $autor['id'];
?>

    <url>
        <loc><?= $urlBase ?>/autores/<?= htmlspecialchars($slugAutor, ENT_XML1, 'UTF-8') ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($autor['data_cadastro'])) ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.50</priority>
    </url>

<?php endwhile; ?>

<?php endif; ?>

</urlset>