<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/configuracao.php';
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/funcoes.php';

$titulo_pagina = 'Artigos Recentes | ' . $configuracao_site['nome_site'];
$descricao_pagina = 'Confira os artigos publicados mais recentemente em nosso site.';

require_once __DIR__ . '/../includes/cabecalho.php';

$consulta = $conexao->query("
    SELECT
        titulo,
        slug,
        resumo,
        imagem_destacada,
        data_publicacao,
        tempo_leitura
    FROM artigos
    WHERE status = 'publicado'
    ORDER BY data_publicacao DESC
    LIMIT 20
");

?>

<main class="pagina-artigos-recentes">

    <div class="container">

        <h1>Artigos Recentes</h1>

        <div class="grid-cards-artigos">

        <?php while ($artigo = $consulta->fetch_assoc()): ?>

            <article class="card-artigo">

                <a href="/artigos/<?= htmlspecialchars($artigo['slug']) ?>">

                    <?php if (!empty($artigo['imagem_destacada'])): ?>

                        <img
                            src="<?= htmlspecialchars($artigo['imagem_destacada']) ?>"
                            alt="<?= htmlspecialchars($artigo['titulo']) ?>"
                            loading="lazy"
                            class="imagem-card-artigo">

                    <?php endif; ?>

                    <div class="conteudo-card-artigo">

                        <h2><?= htmlspecialchars($artigo['titulo']) ?></h2>

                        <p><?= htmlspecialchars($artigo['resumo']) ?></p>

                        <div class="dados-card-artigo">

                            <span>
                                <?= date('d/m/Y', strtotime($artigo['data_publicacao'])) ?>
                            </span>

                            <span>
                                <?= (int)$artigo['tempo_leitura'] ?> min de leitura
                            </span>

                        </div>

                    </div>

                </a>

            </article>

        <?php endwhile; ?>

        </div>

    </div>

</main>

<?php require_once __DIR__ . '/../includes/rodape.php'; ?>