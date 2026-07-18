<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Paginação
|--------------------------------------------------------------------------
|
| Responsável pela navegação entre páginas.
|
*/

function gerar_paginacao(
    int $paginaAtual,
    int $totalRegistros,
    int $porPagina,
    string $parametros = ''
): string
{
    $totalPaginas = (int) ceil($totalRegistros / $porPagina);

    if ($totalPaginas <= 1) {
        return '';
    }

    $urlBase = strtok($_SERVER['REQUEST_URI'], '?');

    $html = [];

    $html[] = '<nav class="area-paginacao" aria-label="Paginação">';

    /*
    |--------------------------------------------------------------------------
    | Primeira página
    |--------------------------------------------------------------------------
    */

    if ($paginaAtual > 1) {

        $html[] =
            '<a class="link-paginacao" href="' .
            $urlBase .
            '?' .
            $parametros .
            ($parametros !== '' ? '&' : '') .
            'pagina=1">&laquo; Primeira</a>';

    }

    /*
    |--------------------------------------------------------------------------
    | Página anterior
    |--------------------------------------------------------------------------
    */

    if ($paginaAtual > 1) {

        $html[] =
            '<a class="link-paginacao" href="' .
            $urlBase .
            '?' .
            $parametros .
            ($parametros !== '' ? '&' : '') .
            'pagina=' .
            ($paginaAtual - 1) .
            '">&lsaquo;</a>';

    }

    /*
    |--------------------------------------------------------------------------
    | Faixa de páginas
    |--------------------------------------------------------------------------
    */

    $inicio = max(1, $paginaAtual - 2);

    $fim = min($totalPaginas, $paginaAtual + 2);

    if ($inicio > 1) {

        $html[] = '<span class="reticencias">...</span>';

    }

    for ($i = $inicio; $i <= $fim; $i++) {

        if ($i === $paginaAtual) {

            $html[] =
                '<span class="link-paginacao pagina-atual">' .
                $i .
                '</span>';

            continue;
        }

        $html[] =
            '<a class="link-paginacao" href="' .
            $urlBase .
            '?' .
            $parametros .
            ($parametros !== '' ? '&' : '') .
            'pagina=' .
            $i .
            '">' .
            $i .
            '</a>';
    }

    if ($fim < $totalPaginas) {

        $html[] = '<span class="reticencias">...</span>';

    }

    /*
    |--------------------------------------------------------------------------
    | Próxima página
    |--------------------------------------------------------------------------
    */

    if ($paginaAtual < $totalPaginas) {

        $html[] =
            '<a class="link-paginacao" href="' .
            $urlBase .
            '?' .
            $parametros .
            ($parametros !== '' ? '&' : '') .
            'pagina=' .
            ($paginaAtual + 1) .
            '">&rsaquo;</a>';

    }

    /*
    |--------------------------------------------------------------------------
    | Última página
    |--------------------------------------------------------------------------
    */

    if ($paginaAtual < $totalPaginas) {

        $html[] =
            '<a class="link-paginacao" href="' .
            $urlBase .
            '?' .
            $parametros .
            ($parametros !== '' ? '&' : '') .
            'pagina=' .
            $totalPaginas .
            '">Última &raquo;</a>';

    }

    $html[] = '</nav>';

    return implode(
        PHP_EOL,
        $html
    );
}