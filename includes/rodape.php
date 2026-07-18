<?php

declare(strict_types=1);

?>

    </main>

    <footer class="rodape-site">

        <div class="container">

            <div class="grid-rodape">

                <section class="coluna-rodape">

                    <h2><?= escapar($configuracao_site['nome_site']) ?></h2>

                    <p>

                        <?= escapar($configuracao_site['descricao_geral']) ?>

                    </p>

                </section>

                <section class="coluna-rodape">

                    <h2>Conteúdo</h2>

                    <ul>

                        <li>
                            <a href="/">
                                Início
                            </a>
                        </li>

                        <li>
                            <a href="/categorias/">
                                Categorias
                            </a>
                        </li>

                        <li>
                            <a href="/tags/">
                                Tags
                            </a>
                        </li>

                        <li>
                            <a href="/autores/">
                                Autores
                            </a>
                        </li>

                        <li>
                            <a href="/mais-lidos/">
                                Mais lidos
                            </a>
                        </li>

                        <li>
                            <a href="/recentes/">
                                Recentes
                            </a>
                        </li>

                    </ul>

                </section>

                <section class="coluna-rodape">

                    <h2>Ferramentas</h2>

                    <ul>

                        <li>
                            <a href="/pesquisa/">
                                Pesquisa
                            </a>
                        </li>

                        <li>
                            <a href="/rss/">
                                RSS
                            </a>
                        </li>

                        <li>
                            <a href="/sitemap/">
                                Sitemap
                            </a>
                        </li>

                    </ul>

                </section>

                <section class="coluna-rodape">

                    <h2>Categorias</h2>

                    <ul>

<?php

$consultaCategoriasRodape = $conexao->query("
    SELECT
        nome,
        slug
    FROM categorias
    ORDER BY nome ASC
    LIMIT 8
");

while ($categoria = $consultaCategoriasRodape->fetch_assoc()):

?>

                        <li>

                            <a href="/categorias/<?= escapar($categoria['slug']) ?>">

                                <?= escapar($categoria['nome']) ?>

                            </a>

                        </li>

<?php endwhile; ?>

                    </ul>

                </section>

            </div>

            <hr class="divisor-rodape">

            <div class="informacoes-rodape">

                <div class="copyright">

                    &copy; <?= date('Y') ?>

                    <?= escapar($configuracao_site['nome_site']) ?>

                    — Todos os direitos reservados.

                </div>

                <nav
                    class="menu-rodape"
                    aria-label="Links institucionais">

                    <a href="/">
                        Início
                    </a>

                    <a href="/sitemap/">
                        Sitemap
                    </a>

                    <a href="/rss/">
                        RSS
                    </a>

                    <a href="/robots.txt">
                        Robots
                    </a>

                </nav>

            </div>

        </div>

    </footer>

    <script>

    document.addEventListener('DOMContentLoaded', () => {

        /*
        |--------------------------------------------------------------------------
        | Fecha automaticamente mensagens do sistema
        |--------------------------------------------------------------------------
        */

        document
            .querySelectorAll('.alerta')
            .forEach(alerta => {

                setTimeout(() => {

                    alerta.style.opacity = '0';

                    setTimeout(() => {

                        alerta.remove();

                    }, 300);

                }, 5000);

            });

        /*
        |--------------------------------------------------------------------------
        | Compartilhamento nativo
        |--------------------------------------------------------------------------
        */

        document
            .querySelectorAll('.botao-compartilhar-nativo')
            .forEach(botao => {

                botao.addEventListener('click', async e => {

                    e.preventDefault();

                    if (navigator.share) {

                        try {

                            await navigator.share({

                                title: document.title,

                                text: document
                                    .querySelector(
                                        'meta[name="description"]'
                                    )?.content ?? '',

                                url: location.href

                            });

                        } catch (erro) {

                            /* Compartilhamento cancelado */

                        }

                    }

                });

            });

    });

    </script>

    <?php

    /*
    |--------------------------------------------------------------------------
    | Scripts adicionais
    |--------------------------------------------------------------------------
    */

    if (!empty($scripts_adicionais)) {

        foreach ($scripts_adicionais as $script) {

            echo $script . PHP_EOL;

        }

    }

    ?>

    <script src="/js/lazy-load.js" defer></script>

    <script src="/js/autocomplete.js" defer></script>

    <script src="/js/compartilhamento.js" defer></script>

    <script src="/js/paginacao-ajax.js" defer></script>

<?php
/*
|--------------------------------------------------------------------------
| Scripts adicionais
|--------------------------------------------------------------------------
|
| Permite que páginas específicas adicionem seus próprios scripts.
|
*/

if (!empty($scripts_adicionais) && is_array($scripts_adicionais)) {

    foreach ($scripts_adicionais as $script) {

        echo $script . PHP_EOL;

    }

}
?>

</body>

</html>