<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Breadcrumb
|--------------------------------------------------------------------------
|
| Gera a navegação estrutural (breadcrumb) do site.
|
*/

function gerar_breadcrumb(array $itens = []): string
{
    global $configuracao_site;

    $html = [];

    $html[] = '<nav class="breadcrumb" aria-label="Breadcrumb">';

    $html[] = '<ol>';

    /*
    |--------------------------------------------------------------------------
    | Página inicial
    |--------------------------------------------------------------------------
    */

    $html[] =
        '<li>' .
        '<a href="' .
        escapar($configuracao_site['url_base']) .
        '/">Início</a>' .
        '</li>';

    /*
    |--------------------------------------------------------------------------
    | Demais itens
    |--------------------------------------------------------------------------
    */

    $total = count($itens);

    foreach ($itens as $indice => $item) {

        $ultimo = ($indice === ($total - 1));

        $nome = escapar(
            $item['nome'] ?? ''
        );

        if (
            !$ultimo &&
            !empty($item['url'])
        ) {

            $url = escapar(
                $item['url']
            );

            $html[] =
                '<li>' .
                '<a href="' .
                $url .
                '">' .
                $nome .
                '</a>' .
                '</li>';

        } else {

            $html[] =
                '<li aria-current="page">' .
                $nome .
                '</li>';

        }

    }

    $html[] = '</ol>';

    $html[] = '</nav>';

    return implode(
        PHP_EOL,
        $html
    );
}