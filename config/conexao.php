<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Configuração da Conexão com o Banco de Dados
|--------------------------------------------------------------------------
|
| Altere apenas estes dados conforme seu servidor.
|
*/

define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_NAME', 'banco_de_dados');
define('DB_USER', 'usuario');
define('DB_PASS', 'senha');

/*
|--------------------------------------------------------------------------
| Configuração do MySQLi
|--------------------------------------------------------------------------
*/

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {

    $conexao = new mysqli(
        DB_HOST,
        DB_USER,
        DB_PASS,
        DB_NAME,
        DB_PORT
    );

    $conexao->set_charset('utf8mb4');

} catch (mysqli_sql_exception $erro) {

    http_response_code(500);

    error_log(
        '[' . date('Y-m-d H:i:s') . '] Erro de conexão: ' .
        $erro->getMessage() .
        PHP_EOL,
        3,
        __DIR__ . '/../logs/php_errors.log'
    );

    exit('Erro interno ao conectar ao banco de dados.');

}

/*
|--------------------------------------------------------------------------
| Configurações da Sessão MySQL
|--------------------------------------------------------------------------
*/

$conexao->query("SET time_zone = '-03:00'");

$conexao->query("SET sql_mode = ''");

/*
|--------------------------------------------------------------------------
| Funções auxiliares
|--------------------------------------------------------------------------
*/

function fechar_conexao(): void
{
    global $conexao;

    if ($conexao instanceof mysqli) {
        $conexao->close();
    }
}

register_shutdown_function('fechar_conexao');
