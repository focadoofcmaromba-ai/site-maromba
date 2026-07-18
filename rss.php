<?php

declare(strict_types=1);

require_once __DIR__ . '/config/configuracao.php';
require_once __DIR__ . '/config/conexao.php';

header('Content-Type: application/rss+xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;

$urlBase = rtrim($configuracao_site['url_base'], '/');

$consulta = $conexao->query("
    SELECT
        a.titulo,
        a.slug,
        a.resumo,
        a.conteudo,
        a.imagem_destacada,
        a.data_publicacao,
        aut.nome AS autor
    FROM artigos a
    INNER JOIN autores aut
        ON aut.id = a.autor_id
    WHERE a.status = 'publicado'
    ORDER BY a.data_publicacao DESC
    LIMIT 20
");

?>
<rss version="2.0"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:dc="http://purl.org/dc/elements/1.1/">

<channel>

<title><?= htmlspecialchars($configuracao_site['nome_site']) ?></title>

<link><?= $urlBase ?></link>

<description><?= htmlspecialchars($configuracao_site['descricao_geral']) ?></description>

<language>pt-BR</language>

<generator>Sistema de Artigos</generator>

<lastBuildDate><?= date(DATE_RSS) ?></lastBuildDate>

<atom:link
    href="<?= $urlBase ?>/rss.php"
    rel="self"
    type="application/rss+xml" />

<?php if ($consulta): ?>

<?php while ($artigo = $consulta->fetch_assoc()): ?>

<?php

$linkArtigo = $urlBase . '/artigos/' . $artigo['slug'];

$descricao = !empty($artigo['resumo'])
    ? $artigo['resumo']
    : mb_substr(strip_tags($artigo['conteudo']), 0, 300);

?>

<item>

<title><?= htmlspecialchars($artigo['titulo']) ?></title>

<link><?= $linkArtigo ?></link>

<guid isPermaLink="true"><?= $linkArtigo ?></guid>

<pubDate><?= date(DATE_RSS, strtotime($artigo['data_publicacao'])) ?></pubDate>

<dc:creator><?= htmlspecialchars($artigo['autor']) ?></dc:creator>

<description><![CDATA[<?= $descricao ?>]]></description>

<?php if (!empty($artigo['imagem_destacada'])): ?>

<enclosure
    url="<?= $urlBase . $artigo['imagem_destacada'] ?>"
    type="image/jpeg" />

<?php endif; ?>

</item>

<?php endwhile; ?>

<?php endif; ?>

</channel>

</rss>