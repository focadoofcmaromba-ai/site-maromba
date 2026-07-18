<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/configuracao.php';
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/funcoes.php';

$titulo_pagina = 'Artigos Mais Lidos | ' . $configuracao_site['nome_site'];
$descricao_pagina = 'Confira os artigos mais acessados do nosso site.';

require_once __DIR__ . '/../includes/cabecalho.php';

$consulta = $conexao->query("
    SELECT
        a.id,
        a.titulo,
        a.slug,
        a.resumo,
        a.imagem_destacada,
        COUNT(v.id) AS total_visualizacoes
    FROM artigos a
    LEFT JOIN visualizacoes v
        ON v.artigo_id = a.id
    WHERE a.status = 'publicado'
    GROUP BY a.id
    ORDER BY total_visualizacoes DESC
    LIMIT 20
");

?>

<main class="pagina-mais-lidos">

    <div class="container">

        <h1>Artigos Mais Lidos</h1>

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
                                <?= number_format($artigo['total_visualizacoes']) ?>
                                visualizações
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