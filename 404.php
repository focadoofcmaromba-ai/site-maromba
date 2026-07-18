<?php
require_once __DIR__ . '/config/configuracao.php';
header("HTTP/1.0 404 Not Found");

$titulo_pagina = 'Página não encontrada | ' . $configuracao_site['nome_site'];
$descricao_pagina = 'A página que você tentou acessar não existe mais no nosso site. Confira nossos conteúdos mais recentes.';

require_once __DIR__ . '/includes/cabecalho.php';
?>
<main class="pagina-erro-404">
    <div class="container" style="text-align: center; padding: 80px 20px;">
        <h1 style="font-size: 6rem; color: #e5e7eb;">404</h1>
        <h2 style="font-size: 1.8rem; margin-bottom: 15px;">Página não encontrada</h2>
        <p style="color: #666; margin-bottom: 30px;">O conteúdo que você tentou acessar não existe ou foi removido. Acesse a página inicial ou confira nossos artigos mais recentes:</p>
        <a href="/" style="padding: 12px 25px; background-color: #2563eb; color: white; border-radius: 8px; text-decoration: none;">Voltar para a página inicial</a>

        <div class="grid-cards-artigos" style="margin-top: 60px;">
            <?php
            $consulta_ultimos_artigos = $conexao->query("SELECT titulo, slug, imagem_destacada, resumo FROM artigos WHERE status = 'publicado' ORDER BY data_publicacao DESC LIMIT 4");
            while($artigo = $consulta_ultimos_artigos->fetch_assoc()){
            ?>
            <article class="card-artigo">
                <a href="/artigos/<?php echo htmlspecialchars($artigo['slug']); ?>">
                    <img src="<?php echo htmlspecialchars($artigo['imagem_destacada']); ?>" alt="<?php echo htmlspecialchars($artigo['titulo']); ?>" loading="lazy" class="imagem-card-artigo">
                    <div class="conteudo-card-artigo">
                        <h2><?php echo htmlspecialchars($artigo['titulo']); ?></h2>
                        <p><?php echo htmlspecialchars($artigo['resumo']); ?></p>
                    </div>
                </a>
            </article>
            <?php } ?>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/includes/rodape.php'; ?>
