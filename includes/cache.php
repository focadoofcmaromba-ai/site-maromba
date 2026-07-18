<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Sistema de Cache
|--------------------------------------------------------------------------
|
| Cache simples baseado em arquivos.
|
*/

if (!defined('CACHE_PHP')) {
    define('CACHE_PHP', true);
}

/*
|--------------------------------------------------------------------------
| Diretório do Cache
|--------------------------------------------------------------------------
*/

function diretorio_cache(): string
{
    global $configuracao_site;

    $diretorio = rtrim(
        $configuracao_site['diretorio_cache'],
        '/'
    ) . '/';

    if (!is_dir($diretorio)) {

        mkdir(
            $diretorio,
            0755,
            true
        );

    }

    return $diretorio;
}

/*
|--------------------------------------------------------------------------
| Caminho do arquivo
|--------------------------------------------------------------------------
*/

function arquivo_cache(
    string $identificador
): string
{
    return diretorio_cache() .
        md5($identificador) .
        '.cache';
}

/*
|--------------------------------------------------------------------------
| Cache existe
|--------------------------------------------------------------------------
*/

function cache_existe(
    string $identificador
): bool
{
    return is_file(
        arquivo_cache($identificador)
    );
}

/*
|--------------------------------------------------------------------------
| Cache válido
|--------------------------------------------------------------------------
*/

function verificar_cache_valido(
    string $identificador,
    ?int $tempo = null
): bool
{
    global $configuracao_site;

    if (
        !$configuracao_site['cache_ativo']
    ) {
        return false;
    }

    if (
        !cache_existe($identificador)
    ) {
        return false;
    }

    $tempo ??=
        $configuracao_site['tempo_cache_padrao'];

    return (
        filemtime(
            arquivo_cache($identificador)
        ) + $tempo
    ) > time();
}

/*
|--------------------------------------------------------------------------
| Salvar Cache
|--------------------------------------------------------------------------
*/

function salvar_conteudo_cache(
    string $identificador,
    string $conteudo
): bool
{
    global $configuracao_site;

    if (
        !$configuracao_site['cache_ativo']
    ) {
        return false;
    }

    $arquivo = arquivo_cache(
        $identificador
    );

    $resultado = file_put_contents(
        $arquivo,
        $conteudo,
        LOCK_EX
    );

    if ($resultado !== false) {

        if (function_exists('registrar_cache')) {
            registrar_cache(
                'SALVAR',
                $identificador
            );
        }

        return true;
    }

    return false;
}

/*
|--------------------------------------------------------------------------
| Obter Cache
|--------------------------------------------------------------------------
*/

function obter_conteudo_cache(
    string $identificador
): string
{
    $arquivo = arquivo_cache(
        $identificador
    );

    if (!is_file($arquivo)) {
        return '';
    }

    return (string) file_get_contents(
        $arquivo
    );
}

/*
|--------------------------------------------------------------------------
| Excluir Cache
|--------------------------------------------------------------------------
*/

function excluir_cache(
    string $identificador
): bool
{
    $arquivo = arquivo_cache(
        $identificador
    );

    if (!is_file($arquivo)) {
        return false;
    }

    if (unlink($arquivo)) {

        if (function_exists('registrar_cache')) {

            registrar_cache(
                'EXCLUIR',
                $identificador
            );

        }

        return true;
    }

    return false;
}

/*
|--------------------------------------------------------------------------
| Limpar Todo o Cache
|--------------------------------------------------------------------------
*/

function limpar_todo_cache(): int
{
    $arquivos = glob(
        diretorio_cache() . '*.cache'
    );

    if ($arquivos === false) {
        return 0;
    }

    $removidos = 0;

    foreach ($arquivos as $arquivo) {

        if (
            is_file($arquivo) &&
            unlink($arquivo)
        ) {
            $removidos++;
        }

    }

    if (
        function_exists('registrar_cache')
    ) {

        registrar_cache(
            'LIMPEZA_TOTAL',
            'TODOS'
        );

    }

    return $removidos;
}

/*
|--------------------------------------------------------------------------
| Estatísticas
|--------------------------------------------------------------------------
*/

function quantidade_cache(): int
{
    $arquivos = glob(
        diretorio_cache() . '*.cache'
    );

    return $arquivos === false
        ? 0
        : count($arquivos);
}

function tamanho_total_cache(): int
{
    $total = 0;

    $arquivos = glob(
        diretorio_cache() . '*.cache'
    );

    if ($arquivos === false) {
        return 0;
    }

    foreach ($arquivos as $arquivo) {

        if (is_file($arquivo)) {
            $total += filesize($arquivo);
        }

    }

    return $total;
}

/*
|--------------------------------------------------------------------------
| Fim do Arquivo
|--------------------------------------------------------------------------
*/