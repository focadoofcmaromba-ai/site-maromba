<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Arquivos Essenciais
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../config/configuracao.php';
require_once __DIR__ . '/../config/funcoes.php';

/*
|--------------------------------------------------------------------------
| Valores padrão
|--------------------------------------------------------------------------
*/

$titulo_pagina ??= $configuracao_site['nome_site'];

$descricao_pagina ??=
    $configuracao_site['descricao_geral'];

$url_canonica ??=
    gerar_url_canonica();

$imagem_pagina ??=
    obter_imagem_compartilhamento();

$tipo_pagina ??= 'website';

$idioma_site =
    $configuracao_site['idioma'];

$charset_site =
    $configuracao_site['charset'];

?>
<!DOCTYPE html>

<html lang="<?= escapar($idioma_site) ?>">

<head>

<meta charset="<?= escapar($charset_site) ?>">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1">

<title><?= escapar($titulo_pagina) ?></title>

<meta
    name="description"
    content="<?= escapar($descricao_pagina) ?>">

<meta
    name="robots"
    content="<?= gerar_meta_robots() ?>">

<link
    rel="canonical"
    href="<?= escapar($url_canonica) ?>">

<meta
    property="og:type"
    content="<?= escapar($tipo_pagina) ?>">

<meta
    property="og:title"
    content="<?= escapar($titulo_pagina) ?>">

<meta
    property="og:description"
    content="<?= escapar($descricao_pagina) ?>">

<meta
    property="og:url"
    content="<?= escapar($url_canonica) ?>">

<meta
    property="og:image"
    content="<?= escapar($imagem_pagina) ?>">

<meta
    property="og:locale"
    content="pt_BR">

<meta
    property="og:site_name"
    content="<?= escapar($configuracao_site['nome_site']) ?>">

<meta
    name="twitter:card"
    content="summary_large_image">

<meta
    name="twitter:title"
    content="<?= escapar($titulo_pagina) ?>">

<meta
    name="twitter:description"
    content="<?= escapar($descricao_pagina) ?>">

<meta
    name="twitter:image"
    content="<?= escapar($imagem_pagina) ?>">

<link
    rel="icon"
    href="/favicon.svg"
    type="image/svg+xml">

<link
    rel="apple-touch-icon"
    href="/apple-touch-icon.png">

<link
    rel="manifest"
    href="/manifest.webmanifest">

<meta
    name="theme-color"
    content="#2563eb">

<?php if (!empty($configuracao_site['facebook_app_id'])): ?>

<meta
    property="fb:app_id"
    content="<?= escapar($configuracao_site['facebook_app_id']) ?>">

<?php endif; ?>

<?php if (!empty($configuracao_site['twitter_site'])): ?>

<meta
    name="twitter:site"
    content="<?= escapar($configuracao_site['twitter_site']) ?>">

<?php endif; ?>

<?= gerar_seo_completo([
    'tipo' => $tipo_pagina,
    'titulo' => $titulo_pagina,
    'descricao' => $descricao_pagina,
    'url' => $url_canonica,
    'imagem' => $imagem_pagina
]); ?>

<link
    rel="stylesheet"
    href="/css/style.css?v=1.0.0">

<link
    rel="stylesheet"
    href="/css/responsivo.css?v=1.0.0">

<script
    src="/js/lazy-load.js"
    defer></script>

<script
    src="/js/autocomplete.js"
    defer></script>

<script
    src="/js/compartilhamento.js"
    defer></script>

<script
    src="/js/paginacao-ajax.js"
    defer></script>

</head>

<body>

<header class="cabecalho-site">

    <div class="container">

        <div class="topo-site">

            <a
                href="/"
                class="logo-site">

                <?= escapar($configuracao_site['nome_site']) ?>

            </a>

        <nav
            class="menu-principal"
            aria-label="Menu principal">

            <ul>

                <li>
                    <a
                        href="/"
                        class="<?= ativo_menu('/') ? 'ativo' : '' ?>">
                        Início
                    </a>
                </li>

                <li>
                    <a
                        href="/categorias/"
                        class="<?= ativo_menu('/categorias') ? 'ativo' : '' ?>">
                        Categorias
                    </a>
                </li>

                <li>
                    <a
                        href="/tags/"
                        class="<?= ativo_menu('/tags') ? 'ativo' : '' ?>">
                        Tags
                    </a>
                </li>

                <li>
                    <a
                        href="/autores/"
                        class="<?= ativo_menu('/autores') ? 'ativo' : '' ?>">
                        Autores
                    </a>
                </li>

                <li>
                    <a
                        href="/mais-lidos/"
                        class="<?= ativo_menu('/mais-lidos') ? 'ativo' : '' ?>">
                        Mais Lidos
                    </a>
                </li>

                <li>
                    <a
                        href="/recentes/"
                        class="<?= ativo_menu('/recentes') ? 'ativo' : '' ?>">
                        Recentes
                    </a>
                </li>

            </ul>

        </nav>

        <form
            action="/pesquisa/"
            method="get"
            class="form-pesquisa"
            role="search"
            autocomplete="off">

            <input
                type="search"
                id="campo-pesquisa-autocomplete"
                name="q"
                placeholder="Pesquisar artigos..."
                value="<?= escapar($_GET['q'] ?? '') ?>"
                minlength="2"
                maxlength="100"
                required>

            <button
                type="submit">

                Pesquisar

            </button>

        </form>

        <button
            class="botao-menu-mobile"
            type="button"
            aria-label="Abrir menu"
            aria-expanded="false">

            ☰

        </button>

    </div>

</header>

<?php if (!empty($_SESSION['mensagem_sucesso'])): ?>

<div class="alerta alerta-sucesso">

    <div class="container">

        <?= escapar($_SESSION['mensagem_sucesso']) ?>

    </div>

</div>

<?php unset($_SESSION['mensagem_sucesso']); ?>

<?php endif; ?>

<?php if (!empty($_SESSION['mensagem_erro'])): ?>

<div class="alerta alerta-erro">

    <div class="container">

        <?= escapar($_SESSION['mensagem_erro']) ?>

    </div>

</div>

<?php unset($_SESSION['mensagem_erro']); ?>

<?php endif; ?>

<?php if (!empty($_SESSION['mensagem_aviso'])): ?>

<div class="alerta alerta-aviso">

    <div class="container">

        <?= escapar($_SESSION['mensagem_aviso']) ?>

    </div>

</div>

<?php unset($_SESSION['mensagem_aviso']); ?>

<?php endif; ?>

<main class="conteudo-principal">

<?php

/*
|--------------------------------------------------------------------------
| Fim do Cabeçalho
|--------------------------------------------------------------------------
|
| O fechamento de <main>, <body> e <html> é realizado
| pelo arquivo includes/rodape.php.
|
*/
?>