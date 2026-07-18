<?php
require_once __DIR__ . '/../config/configuracao.php';
require_once __DIR__ . '/../includes/cache.php';

$slug_categoria = basename($_SERVER['REQUEST_URI']);
$identificador_cache = 'categoria-' . $slug_categoria;
if(verificar_cache_valido($identificador_cache)){
    echo obter_conteudo_cache($identificador_cache);
    exit;
}
ob_start();

// Busca dados da categoria
$consulta_categoria = $conexao->prepare("SELECT * FROM categorias WHERE slug = ?");
$consulta_categoria->bind_param("s", $slug_categoria);
$consulta_categoria->execute();
$categoria = $consulta_categoria->get_result()->fetch_assoc();

if(!$categoria){
    header("HTTP/1.0 404 Not Found");
    require_once __DIR__ . '/../404.php';
    exit;
}

// Configura dados de SEO
$titulo_pagina = !empty($categoria['seo_titulo']) ? $categoria['seo_titulo'] : $categoria['nome'] . ' | ' . $configuracao_site['nome_site'];
$descricao_pagina = !empty($categoria['seo_descricao']) ? $categoria['seo_descricao'] : $categoria['descricao'];
$itens_breadcrumb = [
    ['nome' => 'Categorias', 'url' => '/categorias/'],
    ['nome' => $categoria['nome']]
];

// Trata paginação
$pagina_atual = isset($_GET['pagina']) ? max(1, (int)sanitizar_entrada($_GET['pagina'])) : 1;
$offset = ($pagina_atual - 1) * $configuracao_site['quantidade_artigos_por_pagina'];

// Busca artigos da categoria
$consulta_artigos = $conexao->prepare("SELECT a.id, a.titulo, a.slug, a.resumo, a.imagem_destacada, a.data_publicacao, a.tempo_leitura
                                      FROM artigos a
                                      WHERE a.categoria_id = ? AND a.status = 'publicado'
                                      ORDER BY a.data_publicacao DESC
                                      LIMIT ? OFFSET ?");
$consulta_artigos->bind_param("iii", $categoria['id'], $configuracao_site['quantidade_artigos_por_pagina'], $offset);
$consulta_artigos->execute();
$lista_artigos = $consulta_artigos->get_result();

// Total de registros para paginação
$total_artigos_consulta = $conexao->prepare("SELECT COUNT(*) as total FROM artigos WHERE categoria_id = ? AND status = 'publicado'");
$total_artigos_consulta->bind_param("i", $categoria['id']);
$total_artigos_consulta->execute();
$total_registros = $total_artigos_consulta->get_result()->fetch_assoc()['total'];

// Busca subcategorias da categoria
$consulta_subcategorias = $conexao->prepare("SELECT nome, slug FROM subcategorias WHERE categoria_id = ? ORDER BY nome ASC");
$consulta_subcategorias->bind_param("i", $categoria['id']);
$consulta_subcategorias->execute();
$lista_subcategorias = $consulta_subcategorias->get_result();

require_once __DIR__ . '/../includes/cabecalho.php';
?>
<main class="pagina-categoria">
    <div class="container">
        <?php echo gerar_breadcrumb($itens_breadcrumb); ?>
        <header class="cabecalho-categoria">
            <h1><?php echo htmlspecialchars($categoria['nome']); ?></h1>
            <p><?php echo htmlspecialchars($categoria['descricao']); ?></p>
        </header>

        <?php if($lista_subcategorias->num_rows > 0): ?>
        <div class="lista-subcategorias">
            <h3>Subcategorias</h3>
            <ul>
                <?php while($sub = $lista_subcategorias->fetch_assoc()): ?>
                <li><a href="/categorias/<?php echo htmlspecialchars($categoria['slug']); ?>/<?php echo htmlspecialchars($sub['slug']); ?>"><?php echo htmlspecialchars($sub['nome']); ?></a></li>
                <?php endwhile; ?>
            </ul>
        </div>
        <?php endif; ?>

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
