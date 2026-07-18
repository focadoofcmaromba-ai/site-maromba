<?php
require_once __DIR__ . '/../config/configuracao.php';
require_once __DIR__ . '/../includes/cache.php';

$slug_tag = basename($_SERVER['REQUEST_URI']);
$identificador_cache = 'tag-' . $slug_tag;
if(verificar_cache_valido($identificador_cache)){
    echo obter_conteudo_cache($identificador_cache);
    exit;
}
ob_start();

// Busca dados da tag
$consulta_tag = $conexao->prepare("SELECT * FROM tags WHERE slug = ?");
$consulta_tag->bind_param("s", $slug_tag);
$consulta_tag->execute();
$tag = $consulta_tag->get_result()->fetch_assoc();

if(!$tag){
    header("HTTP/1.0 404 Not Found");
    require_once __DIR__ . '/../404.php';
    exit;
}

$titulo_pagina = 'Conteúdos sobre ' . $tag['nome'] . ' | ' . $configuracao_site['nome_site'];
$descricao_pagina = 'Confira todos os artigos sobre ' . $tag['nome'] . ' para você evoluir na musculação.';
$itens_breadcrumb = [
    ['nome' => 'Tags', 'url' => '/tags/'],
    ['nome' => $tag['nome']]
];

// Trata paginação
$pagina_atual = isset($_GET['pagina']) ? max(1, (int)sanitizar_entrada($_GET['pagina'])) : 1;
$offset = ($pagina_atual - 1) * $configuracao_site['quantidade_artigos_por_pagina'];

// Busca artigos vinculados a tag
$consulta_artigos = $conexao->prepare("SELECT a.id, a.titulo, a.slug, a.resumo, a.imagem_destacada, a.data_publicacao, a.tempo_leitura
                                      FROM artigos a
                                      INNER JOIN artigos_tags atg ON a.id = atg.artigo_id
                                      WHERE atg.tag_id = ? AND a.status = 'publicado'
                                      ORDER BY a.data_publicacao DESC
                                      LIMIT ? OFFSET ?");
$consulta_artigos->bind_param("iii", $tag['id'], $configuracao_site['quantidade_artigos_por_pagina'], $offset);
$consulta_artigos->execute();
$lista_artigos = $consulta_artigos->get_result();

// Total de registros para paginação
$total_artigos_consulta = $conexao->prepare("SELECT COUNT(DISTINCT a.id) as total
                                            FROM artigos a
                                            INNER JOIN artigos_tags atg ON a.id = atg.artigo_id
                                            WHERE atg.tag_id = ? AND a.status = 'publicado'");
$total_artigos_consulta->bind_param("i", $tag['id']);
$total_artigos_consulta->execute();
$total_registros = $total_artigos_consulta->get_result()->fetch_assoc()['total'];

require_once __DIR__ . '/../includes/cabecalho.php';
?>
<main class="pagina-tag">
    <div class="container">
        <?php echo gerar_breadcrumb($itens_breadcrumb); ?>
        <header class="cabecalho-categoria">
            <h1>Conteúdos sobre <?php echo htmlspecialchars($tag['nome']); ?></h1>
            <?php if(!empty($tag['descricao'])): ?>
                <p><?php echo htmlspecialchars($tag['descricao']); ?></p>
            <?php endif; ?>
        </header>

        <div class="grid-cards-artigos">
            <?php while($artigo = $lista_artigos->fetch_assoc()): ?>
            <article class="card-artigo">
                <a href="/artigos/<?php echo htmlspecialchars($artigo['slug']); ?>">
                    <img src="<?php echo htmlspecialchars($artigo['imagem_destacada']); ?>" alt="<?php echo htmlspecialchars($artigo['titulo']); ?>" loading="lazy" class="imagem-card-artigo">
                    <div class="conteudo-card-artigo">
                        <h2><?php echo htmlspecialchars($artigo['titulo']); ?></h2>
                        <p><?php echo htmlspecialchars($artigo['resumo']); ?></p>
                        <div class="dados-card-artigo">
                            <span><?php echo date('d/m/Y', strtotime($artigo['data_publicacao'])); ?></span>
                            <span><?php echo $artigo['tempo_leitura']; ?> min de leitura</span>
                        </div>
                    </div>
                </a>
            </article>
            <?php endwhile; ?>
        </div>

        <?php echo gerar_paginacao($pagina_atual, $total_registros, $configuracao_site['quantidade_artigos_por_pagina']); ?>
    </div>
</main>
<?php
require_once __DIR__ . '/../includes/rodape.php';
$conteudo_buffer = ob_get_clean();
salvar_conteudo_cache($identificador_cache, $conteudo_buffer);
echo $conteudo_buffer;
?>
