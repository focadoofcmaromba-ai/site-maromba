<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Configuração Geral do Sistema
|--------------------------------------------------------------------------
*/

date_default_timezone_set('America/Sao_Paulo');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Carrega arquivos essenciais
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

/*
|--------------------------------------------------------------------------
| Configurações Gerais do Site
|--------------------------------------------------------------------------
*/

$configuracao_site = [

    /*
    |--------------------------------------------------------------------------
    | Informações do Site
    |--------------------------------------------------------------------------
    */

    'nome_site' => 'Sistema de Artigos',

    'descricao_geral' => 'Portal de conteúdos sobre musculação, hipertrofia, treinos, alimentação e saúde.',

    'url_base' => 'https://seudominio.com',

    'idioma' => 'pt-BR',

    'charset' => 'UTF-8',

    'timezone' => 'America/Sao_Paulo',

    /*
    |--------------------------------------------------------------------------
    | Artigos
    |--------------------------------------------------------------------------
    */

    'quantidade_artigos_por_pagina' => 12,

    'quantidade_artigos_relacionados' => 6,

    'tempo_cache_artigos' => 3600,

    /*
    |--------------------------------------------------------------------------
    | Uploads
    |--------------------------------------------------------------------------
    */

    'tamanho_maximo_upload_imagem' => 5 * 1024 * 1024,

    'formatos_permitidos_upload' => [

        'jpg',
        'jpeg',
        'png',
        'gif',
        'webp',
        'svg'

    ],

    'diretorio_upload' => __DIR__ . '/../assets/artigos/',

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    */

    'cache_ativo' => true,

    'tempo_cache_padrao' => 3600,

    'diretorio_cache' => __DIR__ . '/../cache/',

    /*
    |--------------------------------------------------------------------------
    | Logs
    |--------------------------------------------------------------------------
    */

    'logs_ativos' => true,

    'diretorio_logs' => __DIR__ . '/../logs/',

    /*
    |--------------------------------------------------------------------------
    | SEO
    |--------------------------------------------------------------------------
    */

    'imagem_compartilhamento' => '/assets/imagem-compartilhamento.jpg',

    'twitter_site' => '',

    'facebook_app_id' => '',

    /*
    |--------------------------------------------------------------------------
    | Segurança
    |--------------------------------------------------------------------------
    */

    'csrf_token_expiracao' => 7200,

    'max_tentativas_login' => 5,

    'tempo_bloqueio_login' => 900

];

/*
|--------------------------------------------------------------------------
| Configuração de Erros
|--------------------------------------------------------------------------
*/

error_reporting(E_ALL);

ini_set('display_errors', '0');

ini_set('log_errors', '1');

ini_set(
    'error_log',
    __DIR__ . '/../logs/php_errors.log'
);