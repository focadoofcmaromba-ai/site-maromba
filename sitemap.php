<?php

declare(strict_types=1);

require_once __DIR__ . '/config/configuracao.php';
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/config/funcoes.php';

$titulo_pagina = 'Mapa do Site | ' . $configuracao_site['nome_site'];
$descricao_pagina = 'Mapa completo do site com artigos, categorias, tags e autores.';

require_once __DIR__ . '/includes/cabecalho.php';
?>

<main class="pagina-sitemap">

    <div class="container">

        <h1>Mapa do Site</h1>

        <section>

            <h2>Artigos</h2>

            <ul>

            <?php

            $consulta = $conexao->query("
                SELECT titulo, slug
                FROM artigos
                WHERE status = 'publicado'
                ORDER BY titulo
            ");

            while ($artigo = $consulta->fetch_assoc()):

            ?>

                <li>
                    <a href="/artigos/<?= htmlspecialchars($artigo['slug']) ?>">
                        <?= htmlspecialchars($artigo['titulo']) ?>
                    </a>
                </li>

            <?php endwhile; ?>

            </ul>

        </section>

        <section>

            <h2>Categorias</h2>

            <ul>

            <?php

            $consulta = $conexao->query("
                SELECT nome, slug
                FROM categorias
                ORDER BY nome
            ");

            while ($categoria = $consulta->fetch_assoc()):

            ?>

                <li>
                    <a href="/categorias/<?= htmlspecialchars($categoria['slug']) ?>">
                        <?= htmlspecialchars($categoria['nome']) ?>
                    </a>
                </li>

            <?php endwhile; ?>

            </ul>

        </section>

        <section>

            <h2>Tags</h2>

            <ul>

            <?php

            $consulta = $conexao->query("
                SELECT nome, slug
                FROM tags
                ORDER BY nome
            ");

            while ($tag = $consulta->fetch_assoc()):

            ?>

                <li>
                    <a href="/tags/<?= htmlspecialchars($tag['slug']) ?>">
                        <?= htmlspecialchars($tag['nome']) ?>
                    </a>
                </li>

            <?php endwhile; ?>

            </ul>

        </section>

        <section>

            <h2>Autores</h2>

            <ul>

            <?php

            $consulta = $conexao->query("
                SELECT id, nome
                FROM autores
                ORDER BY nome
            ");

            while ($autor = $consulta->fetch_assoc()):

                $slugAutor = gerar_slug($autor['nome']) . '-' . $autor['id'];

            ?>

                <li>
                    <a href="/autores/<?= htmlspecialchars($slugAutor) ?>">
                        <?= htmlspecialchars($autor['nome']) ?>
                    </a>
                </li>

            <?php endwhile; ?>

            </ul>

        </section>

    </div>

</main>

<?php require_once __DIR__ . '/includes/rodape.php'; ?>