<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| SEO
|--------------------------------------------------------------------------
|
| Responsável por gerar todas as metatags do site.
|
*/

if (!defined('SEO_PHP')) {
    define('SEO_PHP', true);
}

/*
|--------------------------------------------------------------------------
| Valores padrão
|--------------------------------------------------------------------------
*/

function seo_valor(array $dados, string $indice, mixed $padrao = ''): mixed
{
    return $dados[$indice] ?? $padrao;
}

/*
|--------------------------------------------------------------------------
| Canonical
|--------------------------------------------------------------------------
*/

function seo_url_canonica(): string
{
    global $configuracao_site;

    return rtrim(
        $configuracao_site['url_base'],
        '/'
    ) . $_SERVER['REQUEST_URI'];
}

/*
|--------------------------------------------------------------------------
| Robots
|--------------------------------------------------------------------------
*/

function seo_meta_robots(
    bool $indexar = true,
    bool $seguir = true
): string
{
    return sprintf(
        '%s, %s',
        $indexar ? 'index' : 'noindex',
        $seguir ? 'follow' : 'nofollow'
    );
}

/*
|--------------------------------------------------------------------------
| Título
|--------------------------------------------------------------------------
*/

function seo_titulo(
    string $titulo
): string
{
    global $configuracao_site;

    return trim(
        $titulo .
        ' | ' .
        $configuracao_site['nome_site']
    );
}

/*
|--------------------------------------------------------------------------
| Description
|--------------------------------------------------------------------------
*/

function seo_descricao(
    string $descricao,
    int $limite = 160
): string
{
    $descricao = strip_tags($descricao);

    if (
        mb_strlen($descricao) <= $limite
    ) {
        return $descricao;
    }

    return mb_substr(
        $descricao,
        0,
        $limite
    ) . '...';
}

/*
|--------------------------------------------------------------------------
| Open Graph
|--------------------------------------------------------------------------
*/

function seo_open_graph(array $dados = []): string
{
    global $configuracao_site;

    $titulo = seo_valor(
        $dados,
        'titulo',
        $configuracao_site['nome_site']
    );

    $descricao = seo_valor(
        $dados,
        'descricao',
        $configuracao_site['descricao_geral']
    );

    $url = seo_valor(
        $dados,
        'url',
        seo_url_canonica()
    );

    $imagem = seo_valor(
        $dados,
        'imagem',
        $configuracao_site['url_base'] .
        $configuracao_site['imagem_compartilhamento']
    );

    $tipo = seo_valor(
        $dados,
        'tipo',
        'website'
    );

    $html = [];

    $html[] = '<meta property="og:type" content="' . escapar($tipo) . '">';
    $html[] = '<meta property="og:title" content="' . escapar($titulo) . '">';
    $html[] = '<meta property="og:description" content="' . escapar($descricao) . '">';
    $html[] = '<meta property="og:url" content="' . escapar($url) . '">';
    $html[] = '<meta property="og:image" content="' . escapar($imagem) . '">';
    $html[] = '<meta property="og:site_name" content="' . escapar($configuracao_site['nome_site']) . '">';
    $html[] = '<meta property="og:locale" content="pt_BR">';

    return implode(PHP_EOL, $html);
}

/*
|--------------------------------------------------------------------------
| Twitter Cards
|--------------------------------------------------------------------------
*/

function seo_twitter(array $dados = []): string
{
    global $configuracao_site;

    $titulo = seo_valor(
        $dados,
        'titulo',
        $configuracao_site['nome_site']
    );

    $descricao = seo_valor(
        $dados,
        'descricao',
        $configuracao_site['descricao_geral']
    );

    $imagem = seo_valor(
        $dados,
        'imagem',
        $configuracao_site['url_base'] .
        $configuracao_site['imagem_compartilhamento']
    );

    $html = [];

    $html[] = '<meta name="twitter:card" content="summary_large_image">';

    if (!empty($configuracao_site['twitter_site'])) {
        $html[] = '<meta name="twitter:site" content="' .
            escapar($configuracao_site['twitter_site']) .
            '">';
    }

    $html[] = '<meta name="twitter:title" content="' . escapar($titulo) . '">';
    $html[] = '<meta name="twitter:description" content="' . escapar($descricao) . '">';
    $html[] = '<meta name="twitter:image" content="' . escapar($imagem) . '">';

    return implode(PHP_EOL, $html);
}

/*
|--------------------------------------------------------------------------
| JSON-LD - Organization
|--------------------------------------------------------------------------
*/

function seo_schema_organization(): string
{
    global $configuracao_site;

    $schema = [

        '@context' => 'https://schema.org',

        '@type' => 'Organization',

        'name' => $configuracao_site['nome_site'],

        'url' => $configuracao_site['url_base'],

        'logo' => $configuracao_site['url_base'] . '/favicon.svg'

    ];

    return '<script type="application/ld+json">' .
        json_encode(
            $schema,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        ) .
        '</script>';
}

/*
|--------------------------------------------------------------------------
| JSON-LD - Website
|--------------------------------------------------------------------------
*/

function seo_schema_website(): string
{
    global $configuracao_site;

    $schema = [

        '@context' => 'https://schema.org',

        '@type' => 'WebSite',

        'name' => $configuracao_site['nome_site'],

        'url' => $configuracao_site['url_base'],

        'description' => $configuracao_site['descricao_geral']

    ];

    return '<script type="application/ld+json">' .
        json_encode(
            $schema,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        ) .
        '</script>';
}

/*
|--------------------------------------------------------------------------
| JSON-LD - Article
|--------------------------------------------------------------------------
*/

function seo_schema_artigo(array $dados): string
{
    global $configuracao_site;

    $schema = [

        '@context' => 'https://schema.org',

        '@type' => 'Article',

        'headline' => seo_valor($dados, 'titulo'),

        'description' => seo_valor($dados, 'descricao'),

        'image' => seo_valor($dados, 'imagem'),

        'datePublished' => seo_valor($dados, 'publicado'),

        'dateModified' => seo_valor(
            $dados,
            'atualizado',
            seo_valor($dados, 'publicado')
        ),

        'author' => [

            '@type' => 'Person',

            'name' => seo_valor(
                $dados,
                'autor',
                'Equipe'
            )

        ],

        'publisher' => [

            '@type' => 'Organization',

            'name' => $configuracao_site['nome_site'],

            'logo' => [

                '@type' => 'ImageObject',

                'url' => $configuracao_site['url_base'] . '/favicon.svg'

            ]

        ],

        'mainEntityOfPage' => [

            '@type' => 'WebPage',

            '@id' => seo_valor(
                $dados,
                'url',
                seo_url_canonica()
            )

        ]

    ];

    return '<script type="application/ld+json">' .
        json_encode(
            $schema,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        ) .
        '</script>';
}

/*
|--------------------------------------------------------------------------
| JSON-LD - BreadcrumbList
|--------------------------------------------------------------------------
*/

function seo_schema_breadcrumb(array $itens): string
{
    global $configuracao_site;

    $lista = [];

    $lista[] = [
        '@type' => 'ListItem',
        'position' => 1,
        'name' => 'Início',
        'item' => rtrim($configuracao_site['url_base'], '/')
    ];

    $posicao = 2;

    foreach ($itens as $item) {

        $lista[] = [
            '@type' => 'ListItem',
            'position' => $posicao++,
            'name' => $item['nome'],
            'item' => !empty($item['url'])
                ? rtrim($configuracao_site['url_base'], '/') . $item['url']
                : seo_url_canonica()
        ];
    }

    return '<script type="application/ld+json">' .
        json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $lista
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        . '</script>';
}

/*
|--------------------------------------------------------------------------
| JSON-LD - FAQ
|--------------------------------------------------------------------------
*/

function seo_schema_faq(array $faq): string
{
    if (empty($faq)) {
        return '';
    }

    $perguntas = [];

    foreach ($faq as $item) {

        $perguntas[] = [

            '@type' => 'Question',

            'name' => $item['pergunta'],

            'acceptedAnswer' => [

                '@type' => 'Answer',

                'text' => $item['resposta']

            ]

        ];
    }

    return '<script type="application/ld+json">' .
        json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $perguntas
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        . '</script>';
}

/*
|--------------------------------------------------------------------------
| SEO Completo
|--------------------------------------------------------------------------
*/

function gerar_seo_completo(array $dados = []): string
{
    $html = [];

    $html[] = seo_open_graph($dados);

    $html[] = seo_twitter($dados);

    $html[] = seo_schema_organization();

    $html[] = seo_schema_website();

    if (
        ($dados['tipo'] ?? '') === 'article'
    ) {

        $html[] = seo_schema_artigo($dados);

    }

    if (!empty($dados['breadcrumb'])) {

        $html[] = seo_schema_breadcrumb(
            $dados['breadcrumb']
        );

    }

    if (!empty($dados['faq'])) {

        $html[] = seo_schema_faq(
            $dados['faq']
        );

    }

    return implode(
        PHP_EOL,
        array_filter($html)
    );
}

/*
|--------------------------------------------------------------------------
| Hreflang
|--------------------------------------------------------------------------
*/

function seo_hreflang(): string
{
    global $configuracao_site;

    return sprintf(
        '<link rel="alternate" hreflang="%s" href="%s">',
        escapar($configuracao_site['idioma']),
        escapar(seo_url_canonica())
    );
}

/*
|--------------------------------------------------------------------------
| Meta Theme Color
|--------------------------------------------------------------------------
*/

function seo_theme_color(): string
{
    return '<meta name="theme-color" content="#2563eb">';
}

/*
|--------------------------------------------------------------------------
| Manifest
|--------------------------------------------------------------------------
*/

function seo_manifest(): string
{
    return '<link rel="manifest" href="/manifest.webmanifest">';
}

/*
|--------------------------------------------------------------------------
| Favicons
|--------------------------------------------------------------------------
*/

function seo_favicons(): string
{
    return implode(PHP_EOL, [

        '<link rel="icon" href="/favicon.svg" type="image/svg+xml">',

        '<link rel="apple-touch-icon" href="/apple-touch-icon.png">'

    ]);
}

/*
|--------------------------------------------------------------------------
| Renderização Completa
|--------------------------------------------------------------------------
*/

function renderizar_seo(array $dados = []): string
{
    $html = [];

    $html[] = seo_hreflang();

    $html[] = seo_theme_color();

    $html[] = seo_manifest();

    $html[] = seo_favicons();

    $html[] = gerar_seo_completo($dados);

    return implode(
        PHP_EOL,
        array_filter($html)
    );
}

/*
|--------------------------------------------------------------------------
| Fim do Arquivo
|--------------------------------------------------------------------------
*/