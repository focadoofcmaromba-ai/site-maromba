<?php
require_once __DIR__ . '/../config/configuracao.php';
require_once __DIR__ . '/../includes/cache.php';

$slug_autor_completo = basename($_SERVER['REQUEST_URI']);
$id_autor = (int)substr(strrchr($slug_autor_completo, '-'), 1);

$identificador_cache = 'autor-' . $id_autor;
if(verificar_cache_valido($identificador_cache)){
    echo obter_conteudo_cache($identificador_cache);
    exit;
}
ob_start();

// Busca dados do autor
$consulta_autor = $conexao->prepare("SELECT * FROM autores WHERE id = ?");
$consulta_autor->bind_param("i", $id_autor);
$consulta_autor->execute();
$autor = $consulta_autor->get_result()->fetch_assoc();

if(!$autor){
    header("HTTP/1.0 404 Not Found");
    require_once __DIR__ . '/../404.php';
    exit;
}

$titulo_pagina = 'Artigos de ' . $autor['nome'] . ' | ' . $configuracao_site['nome_site'];
$descricao_pagina = 'Confira todos os conteúdos publicados por ' . $autor['nome'] . ' sobre musculação e saúde.';
$itens_breadcrumb = [
    ['nome' => 'Autores', 'url' => '/autores/'],
    ['nome' => $autor['nome']]
];

// Trata paginação
$pagina_atual = isset($_GET['pagina']) ? max(1, (int)sanitizar_entrada($_GET['pagina'])) : 1;
$offset = ($pagina_atual - 1) * $configuracao_site['quantidade_artigos_por_pagina'];

// Busca artigos do autor
$consulta_artigos = $conexao->prepare("SELECT a.id, a.titulo, a.slug, a.resumo, a.imagem_destacada, a.data_publicacao, a.tempo_leitura
                                      FROM artigos a
                                      WHERE a.autor_id = ? AND a.status = 'publicado'
                                      ORDER BY a.data_publicacao DESC
                                      LIMIT ? OFFSET ?");
$consulta_artigos->bind_param("iii", $autor['id'], $configuracao_site['quantidade_artigos_por_pagina'], $offset);
$consulta_artigos->execute();
$lista_artigos = $consulta_artigos->get_result();

// Total de registros para paginação
$total_artigos_consulta = $conexao->prepare("SELECT COUNT(*) as total FROM artigos WHERE autor_id = ? AND status = 'publicado'");
$total_artigos_consulta->bind_param("i", $autor['id']);
$total_artigos_consulta->execute();
$total_registros = $total_artigos_consulta->get_result()->fetch_assoc()['total'];

require_once __DIR__ . '/../includes/cabecalho.php';
?>
<main class="pagina-perfil-autor">
    <div class="container">
        <?php echo gerar_breadcrumb($itens_breadcrumb); ?>
        <div class="dados-perfil-autor">
            <?php if(!empty($autor['foto'])): ?>
                <img src="<?php echo htmlspecialchars($autor['foto']); ?>" alt="<?php echo htmlspecialchars($autor['nome']); ?>" class="foto-autor">
            <?php endif; ?>
            <div class="texto-perfil-autor">
                <h1><?php echo htmlspecialchars($autor['nome']); ?></h1>
                <p><?php echo htmlspecialchars($autor['bio']); ?></p>
            </div>
        </div>

        <h2>Artigos publicados</h2>
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
