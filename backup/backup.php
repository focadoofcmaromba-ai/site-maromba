<?php

/**
 * ==========================================================
 * Sistema de Backup do Banco de Dados
 * ==========================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/configuracao.php';
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../logs/logs.php';

/*
|--------------------------------------------------------------------------
| Configurações
|--------------------------------------------------------------------------
*/

const BACKUP_DIAS_RETENCAO = 7;

/*
|--------------------------------------------------------------------------
| Autenticação
|--------------------------------------------------------------------------
*/

if (
    !isset($_GET['token']) ||
    !hash_equals(BACKUP_TOKEN, $_GET['token'])
) {
    http_response_code(403);
    exit('Acesso negado.');
}

/*
|--------------------------------------------------------------------------
| Diretório
|--------------------------------------------------------------------------
*/

$diretorioBackup = __DIR__;

if (!is_dir($diretorioBackup)) {
    mkdir($diretorioBackup, 0755, true);
}

/*
|--------------------------------------------------------------------------
| Nome do arquivo
|--------------------------------------------------------------------------
*/

$nomeArquivo = sprintf(
    'backup_%s.sql',
    date('Y-m-d_H-i-s')
);

$caminhoArquivo = $diretorioBackup .
    DIRECTORY_SEPARATOR .
    $nomeArquivo;

/*
|--------------------------------------------------------------------------
| Início do backup
|--------------------------------------------------------------------------
*/

$dump = '';

$dump .= "-- =======================================" . PHP_EOL;
$dump .= "-- Backup automático" . PHP_EOL;
$dump .= "-- Data: " . date('d/m/Y H:i:s') . PHP_EOL;
$dump .= "-- =======================================" . PHP_EOL;
$dump .= PHP_EOL;
$dump .= "SET FOREIGN_KEY_CHECKS=0;" . PHP_EOL;
$dump .= PHP_EOL;

$consultaTabelas = $conexao->query("SHOW TABLES");

if (!$consultaTabelas) {

    registrar_erro('Falha ao obter lista de tabelas.');

    exit('Erro ao gerar backup.');

}

while ($tabela = $consultaTabelas->fetch_array()) {

    $nomeTabela = $tabela[0];

    /*
    |--------------------------------------------------------------------------
    | Estrutura da tabela
    |--------------------------------------------------------------------------
    */

    $estrutura = $conexao->query(
        "SHOW CREATE TABLE `$nomeTabela`"
    );

    if (!$estrutura) {

        registrar_erro(
            "Erro ao obter estrutura da tabela {$nomeTabela}"
        );

        continue;
    }

    $dadosEstrutura = $estrutura->fetch_assoc();

    $dump .= PHP_EOL;
    $dump .= "-- ---------------------------------------" . PHP_EOL;
    $dump .= "-- Tabela: {$nomeTabela}" . PHP_EOL;
    $dump .= "-- ---------------------------------------" . PHP_EOL;
    $dump .= PHP_EOL;

    $dump .= "DROP TABLE IF EXISTS `{$nomeTabela}`;" . PHP_EOL;
    $dump .= PHP_EOL;

    $dump .= $dadosEstrutura['Create Table'] . ";" . PHP_EOL;
    $dump .= PHP_EOL;

    /*
    |--------------------------------------------------------------------------
    | Dados da tabela
    |--------------------------------------------------------------------------
    */

    $consultaDados = $conexao->query(
        "SELECT * FROM `{$nomeTabela}`"
    );

    if (!$consultaDados) {

        registrar_erro(
            "Erro ao exportar dados da tabela {$nomeTabela}"
        );

        continue;
    }

    while ($linha = $consultaDados->fetch_assoc()) {

        $campos = [];
        $valores = [];

        foreach ($linha as $campo => $valor) {

            $campos[] = "`{$campo}`";

            if (is_null($valor)) {

                $valores[] = "NULL";

            } elseif (is_numeric($valor)) {

                $valores[] = $valor;

            } else {

                $valores[] = "'" .
                    $conexao->real_escape_string($valor) .
                    "'";

            }
        }

        $dump .= sprintf(
            "INSERT INTO `%s` (%s) VALUES (%s);",
            $nomeTabela,
            implode(', ', $campos),
            implode(', ', $valores)
        );

        $dump .= PHP_EOL;
    }

    $dump .= PHP_EOL;
}

$dump .= PHP_EOL;
$dump .= "SET FOREIGN_KEY_CHECKS=1;" . PHP_EOL;

/*
|--------------------------------------------------------------------------
| Salva o arquivo de backup
|--------------------------------------------------------------------------
*/

if (file_put_contents($caminhoArquivo, $dump, LOCK_EX) === false) {

    registrar_erro(
        "Falha ao salvar o arquivo {$nomeArquivo}"
    );

    exit('Não foi possível salvar o backup.');
}

/*
|--------------------------------------------------------------------------
| Remove backups antigos
|--------------------------------------------------------------------------
*/

$arquivosBackup = glob(
    $diretorioBackup . DIRECTORY_SEPARATOR . 'backup_*.sql'
);

if ($arquivosBackup !== false) {

    foreach ($arquivosBackup as $arquivo) {

        if (
            filemtime($arquivo) <
            strtotime('-' . BACKUP_DIAS_RETENCAO . ' days')
        ) {

            @unlink($arquivo);

        }
    }
}

/*
|--------------------------------------------------------------------------
| Registra o evento
|--------------------------------------------------------------------------
*/

registrar_evento(
    'BACKUP',
    "Backup criado com sucesso: {$nomeArquivo}"
);

/*
|--------------------------------------------------------------------------
| Resposta
|--------------------------------------------------------------------------
*/

header('Content-Type: application/json; charset=utf-8');

echo json_encode(
    [
        'status' => 'sucesso',
        'arquivo' => $nomeArquivo,
        'caminho' => $caminhoArquivo,
        'data' => date('Y-m-d H:i:s'),
        'tamanho' => filesize($caminhoArquivo)
    ],
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
);

exit;