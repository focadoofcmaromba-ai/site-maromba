<?php
require_once __DIR__ . '/../config/configuracao.php';
require_once __DIR__ . '/../includes/cache.php';

$termo_busca = isset($_GET['q']) ? sanitizar_entrada($_GET['q']) : '';
$identificador_cache = 'pesquisa-' . md5($termo_busca . $_SERVER['REQUEST_URI']);
if(!empty($termo_busca) && verificar_cache_valido($identificador_cache)){
    echo obter_conteudo_cache($identificador_cache);
    exit;
}
ob_start();

$titulo_pagina = 'Resultados da pesquisa: ' . htmlspecialchars($termo_busca) . ' | ' . $configuracao_site['nome_site'];
$descricao_pagina = 'Confira os conteúdos correspondentes a sua busca por ' . htmlspecialchars($termo_busca) . '.';
$itens_breadcrumb = [['nome' => 'Pesquisa']];

$pagina_atual = isset($_GET['pagina']) ? max(1, (int)sanitizar_entrada($_GET['pagina'])) : 1;
$offset = ($pagina_atual - 1) * $configuracao_site['quantidade_artigos_por_pagina'];
$termo_busca_sql = "%$termo_busca%";

// Busca artigos correspondentes
$consulta_artigos = $conexao->prepare("SELECT a.id, a.titulo, a.slug, a.resumo, a.imagem_destacada, a.data_publicacao, a.tempo_leitura
                                      FROM artigos a
                                      WHERE a.status = 'publicado' AND (a.titulo LIKE ? OR a.resumo LIKE ? OR a.conteudo LIKE ?)
                                      ORDER BY a.data_publicacao DESC
                                      LIMIT ? OFFSET ?");
$consulta_artigos->bind_param("sssii", $termo_busca_sql, $termo_busca_sql, $termo_busca_sql, $configuracao_site['quantidade_artigos_por_pagina'], $offset);
$consulta_artigos->execute();
$lista_artigos = $consulta_artigos->get_result();

// Total de registros para paginação
$total_artigos_consulta = $conexao->prepare("SELECT COUNT(*) as total
                                            FROM artigos a
                                            WHERE a.status = 'publicado' AND (a.titulo LIKE ? OR a.resumo LIKE ? OR a.conteudo LIKE ?)");
$total_artigos_consulta->bind_param("sss", $termo_busca_sql, $termo_busca_sql, $termo_busca_sql);
$total_artigos_consulta->execute();
$total_registros = $total_artigos_consulta->get_result()->fetch_assoc()['total'];

require_once __DIR__ . '/../includes/cabecalho.php';
?>
<main class="pagina-resultados-pesquisa">
    <div class="container">
        <?php echo gerar_breadcrumb($itens_breadcrumb); ?>
        <header class="cabecalho-pesquisa">
            <h1>Resultados para: "<?php echo htmlspecialchars($termo_busca); ?>"</h1>
            <p>Encontramos <?php echo $total_registros; ?> conteúdos correspondentes a sua busca</p>
        </header>

        <div class="grid-cards-artigos">
            <?php if($lista_artigos->num_rows > 0): ?>
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
            <?php else: ?>
                <p class="mensagem-sem-resultados">Nenhum conteúdo foi encontrado para o termo buscado. Tente usar palavras chave diferentes.</p>
            <?php endif; ?>
        </div>

        <?php echo gerar_paginacao($pagina_atual, $total_registros, $configuracao_site['quantidade_artigos_por_pagina']); ?>
    </div>
</main>
<?php
require_once __DIR__ . '/../includes/rodape.php';
if(!empty($termo_busca)){
    $conteudo_buffer = ob_get_clean();
    salvar_conteudo_cache($identificador_cache, $conteudo_buffer);
    echo $conteudo_buffer;
}
?>
