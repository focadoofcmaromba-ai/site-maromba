<?php
// Página individual de exibição de artigo
require_once __DIR__ . '/../php/configuracao.php';
require_once __DIR__ . '/../php/sistema-cache.php';

// Pega o slug do artigo pela URL
$slug_artigo = basename($_SERVER['REQUEST_URI']);
$identificador_cache = 'artigo-' . $slug_artigo;

// Verifica cache válido
if(verificar_cache_valido($identificador_cache)){
    echo obter_conteudo_cache($identificador_cache);
    exit;
}
ob_start();

// Busca dados do artigo no banco
$consulta = $conexao->prepare("SELECT a.*, c.nome as categoria_nome, c.slug as categoria_slug, s.nome as subcategoria_nome, s.slug as subcategoria_slug, aut.nome as autor_nome, aut.bio as autor_bio
                               FROM artigos a
                               INNER JOIN categorias c ON a.categoria_id = c.id
                               INNER JOIN autores aut ON a.autor_id = aut.id
                               LEFT JOIN subcategorias s ON a.subcategoria_id = s.id
                               WHERE a.slug = ? AND a.status = 'publicado'");
$consulta->bind_param("s", $slug_artigo);
$consulta->execute();
$artigo = $consulta->get_result()->fetch_assoc();

if(!$artigo){
    header("HTTP/1.0 404 Not Found");
    echo "Artigo não encontrado";
    exit;
}

// Registra visualização do usuário
registrar_visualizacao_artigo($artigo['id']);
$total_visualizacoes = obter_total_visualizacoes($artigo['id']);

// Dados para SEO
$titulo_pagina = !empty($artigo['seo_titulo']) ? $artigo['seo_titulo'] : $artigo['titulo'];
$descricao_pagina = !empty($artigo['seo_descricao']) ? $artigo['seo_descricao'] : $artigo['resumo'];
$url_canonica = $configuracao_site['url_base'] . '/artigos/' . $artigo['slug'];
$imagem_pagina = $artigo['imagem_destacada'];
$tipo_pagina = 'article';

require_once __DIR__ . '/../php/cabecalho_seo.php';
?>
<main class="pagina-artigo">
    <div class="container-artigo">
        <!-- Breadcrumb -->
        <nav class="navegacao-breadcrumb">
            <a href="/">Início</a> >
            <a href="/categorias/<?php echo $artigo['categoria_slug']; ?>"><?php echo $artigo['categoria_nome']; ?></a> >
            <span><?php echo $artigo['titulo']; ?></span>
        </nav>

        <!-- Cabeçalho do artigo -->
        <header class="cabecalho-artigo">
            <h1><?php echo $artigo['titulo']; ?></h1>
            <p class="subtitulo-artigo"><?php echo $artigo['subtitulo']; ?></p>
            <div class="dados-artigo">
                <span>Por <?php echo $artigo['autor_nome']; ?></span>
                <span><?php echo date('d/m/Y', strtotime($artigo['data_publicacao'])); ?></span>
                <span><?php echo $artigo['tempo_leitura']; ?> min de leitura</span>
                <span><?php echo $total_visualizacoes; ?> visualizações</span>
            </div>
            <img src="<?php echo $artigo['imagem_destacada']; ?>" alt="<?php echo $artigo['titulo']; ?>" class="imagem-destaque-artigo">
        </header>

        <!-- Conteúdo completo do artigo -->
        <div class="conteudo-artigo">
            <?php echo $artigo['conteudo']; ?>
        </div>

        <!-- Seção de FAQ -->
        <section class="secao-faq">
            <h2>Perguntas frequentes</h2>
            <?php
            $consulta_faq = $conexao->prepare("SELECT * FROM faq_artigos WHERE artigo_id = ? ORDER BY ordem ASC");
            $consulta_faq->bind_param("i", $artigo['id']);
            $consulta_faq->execute();
            $lista_faq = $consulta_faq->get_result();
            while($faq = $lista_faq->fetch_assoc()){
            ?>
            <div class="item-faq">
                <h3><?php echo $faq['pergunta']; ?></h3>
                <p><?php echo $faq['resposta']; ?></p>
            </div>
            <?php } ?>
        </section>

<section class="artigos-relacionados">
    <h2>Artigos que você também vai gostar</h2>
    <div class="grid-relacionados">
        <?php
        $relacionados = buscar_artigos_relacionados($artigo['id'], $artigo['categoria_id'], $configuracao_site['quantidade_artigos_relacionados']);
        foreach($relacionados as $item){
        ?>
        <div class="card-relacionado">
            <a href="/artigos/<?php echo $item['slug']; ?>">
                <img src="<?php echo $item['imagem_destacada']; ?>" alt="<?php echo $item['titulo']; ?>" loading="lazy">
                <h3><?php echo $item['titulo']; ?></h3>
            </a>
        </div>
        <?php } ?>
    </div>
</section>


        <!-- Botões de compartilhamento -->
        <div class="compartilhamento-artigo">
            <p>Compartilhe esse artigo:</p>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($url_canonica); ?>" target="_blank">Facebook</a>
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($url_canonica); ?>&text=<?php echo urlencode($titulo_pagina); ?>" target="_blank">Twitter</a>
            <a href="https://wa.me/?text=<?php echo urlencode($titulo_pagina . ' ' . $url_canonica); ?>" target="_blank">WhatsApp</a>
        </div>
    </div>

    <!-- Schema Article para SEO -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": "<?php echo $titulo_pagina; ?>",
        "description": "<?php echo $descricao_pagina; ?>",
        "author": {"@type": "Person", "name": "<?php echo $artigo['autor_nome']; ?>"},
        "datePublished": "<?php echo date('Y-m-d', strtotime($artigo['data_publicacao'])); ?>",
        "dateModified": "<?php echo date('Y-m-d', strtotime($artigo['data_ultima_atualizacao'])); ?>",
        "image": "<?php echo $imagem_pagina; ?>"
    }
    </script>
</main>
<?php
require_once __DIR__ . '/../php/rodape.php';
$conteudo_buffer = ob_get_clean();
salvar_conteudo_cache($identificador_cache, $conteudo_buffer);
echo $conteudo_buffer;
?>
