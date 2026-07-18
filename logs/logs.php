<?php
/**
 * Arquivo: logs/logs.php
 * Objetivo: Registrar eventos do sistema em arquivos de log.
 */

function escrever_log(string $arquivo, string $mensagem): void
{
    $pasta_logs = __DIR__;

    if (!is_dir($pasta_logs)) {
        mkdir($pasta_logs, 0755, true);
    }

    $caminho = $pasta_logs . DIRECTORY_SEPARATOR . $arquivo;

    $linha = sprintf(
        "[%s] %s%s",
        date('Y-m-d H:i:s'),
        $mensagem,
        PHP_EOL
    );

    file_put_contents($caminho, $linha, FILE_APPEND | LOCK_EX);
}

/**
 * Registra erros.
 */
function registrar_erro(string $mensagem): void
{
    escrever_log(
        'erros.log',
        'ERRO: ' . $mensagem
    );
}

/**
 * Registra uploads.
 */
function registrar_upload(string $arquivo, string $status): void
{
    escrever_log(
        'uploads.log',
        "UPLOAD: Arquivo '{$arquivo}' | Status: {$status}"
    );
}

/**
 * Registra chamadas da API.
 */
function registrar_api(string $metodo, string $rota, int $codigo): void
{
    escrever_log(
        'api.log',
        "API: {$metodo} {$rota} | HTTP {$codigo}"
    );
}

/**
 * Registra eventos do cache.
 */
function registrar_cache(string $acao, string $chave): void
{
    escrever_log(
        'cache.log',
        "CACHE: {$acao} | Chave: {$chave}"
    );
}

/**
 * Registra eventos gerais.
 */
function registrar_evento(string $tipo, string $descricao): void
{
    escrever_log(
        'eventos.log',
        "{$tipo}: {$descricao}"
    );
}
?>