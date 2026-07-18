<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Upload de Imagens
|--------------------------------------------------------------------------
|
| Responsável por validar, mover e retornar o resultado
| do upload de imagens do sistema.
|
*/

require_once __DIR__ . '/../config/configuracao.php';
require_once __DIR__ . '/../config/funcoes.php';

/*
|--------------------------------------------------------------------------
| Upload Principal
|--------------------------------------------------------------------------
*/

function processar_upload_imagem(array $arquivo): array
{
    global $configuracao_site;

    if (!isset($arquivo['error'])) {

        return [
            'status' => 'erro',
            'mensagem' => 'Arquivo inválido.'
        ];

    }

    if ($arquivo['error'] !== UPLOAD_ERR_OK) {

        return [
            'status' => 'erro',
            'mensagem' => 'Falha no envio do arquivo.'
        ];

    }

    if (
        $arquivo['size'] <= 0 ||
        $arquivo['size'] >
        $configuracao_site['tamanho_maximo_upload_imagem']
    ) {

        return [
            'status' => 'erro',
            'mensagem' => 'Tamanho do arquivo inválido.'
        ];

    }

    $extensao = strtolower(
        pathinfo(
            $arquivo['name'],
            PATHINFO_EXTENSION
        )
    );

    if (
        !in_array(
            $extensao,
            $configuracao_site['formatos_permitidos_upload'],
            true
        )
    ) {

        return [
            'status' => 'erro',
            'mensagem' => 'Formato de imagem não permitido.'
        ];

    }

    /*
    |--------------------------------------------------------------------------
    | Validação do MIME Type
    |--------------------------------------------------------------------------
    */

    $mimePermitidos = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml'
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);

    if ($finfo === false) {

        return [
            'status' => 'erro',
            'mensagem' => 'Não foi possível validar o arquivo.'
        ];

    }

    $mime = finfo_file(
        $finfo,
        $arquivo['tmp_name']
    );

    finfo_close($finfo);

    if (
        !in_array(
            $mime,
            $mimePermitidos,
            true
        )
    ) {

        return [
            'status' => 'erro',
            'mensagem' => 'O arquivo enviado não é uma imagem válida.'
        ];

    }

    /*
    |--------------------------------------------------------------------------
    | Garante que a imagem é realmente uma imagem
    |--------------------------------------------------------------------------
    */

    if (
        $mime !== 'image/svg+xml'
    ) {

        if (
            @getimagesize($arquivo['tmp_name']) === false
        ) {

            return [
                'status' => 'erro',
                'mensagem' => 'Imagem inválida.'
            ];

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Cria o diretório caso não exista
    |--------------------------------------------------------------------------
    */

    $diretorio = rtrim(
        $configuracao_site['diretorio_upload'],
        '/'
    ) . '/';

    if (!is_dir($diretorio)) {

        if (
            !mkdir(
                $diretorio,
                0755,
                true
            )
        ) {

            return [
                'status' => 'erro',
                'mensagem' => 'Não foi possível criar o diretório de upload.'
            ];

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Nome seguro do arquivo
    |--------------------------------------------------------------------------
    */

    $novoNome = gerar_nome_imagem(
        $arquivo['name']
    );

    $destino = $diretorio . $novoNome;

    /*
    |--------------------------------------------------------------------------
    | Move o arquivo para o destino final
    |--------------------------------------------------------------------------
    */

    if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {

        if (function_exists('registrar_upload')) {

            registrar_upload(
                $arquivo['name'],
                'ERRO'
            );

        }

        return [
            'status' => 'erro',
            'mensagem' => 'Não foi possível salvar a imagem.'
        ];

    }

    /*
    |--------------------------------------------------------------------------
    | Permissões
    |--------------------------------------------------------------------------
    */

    @chmod(
        $destino,
        0644
    );

    /*
    |--------------------------------------------------------------------------
    | Registro em Log
    |--------------------------------------------------------------------------
    */

    if (function_exists('registrar_upload')) {

        registrar_upload(
            $novoNome,
            'SUCESSO'
        );

    }

    /*
    |--------------------------------------------------------------------------
    | Caminho relativo
    |--------------------------------------------------------------------------
    */

    $caminhoRelativo = '/assets/artigos/' . $novoNome;

    return [

        'status' => 'sucesso',

        'mensagem' => 'Upload realizado com sucesso.',

        'arquivo' => $novoNome,

        'caminho' => $caminhoRelativo,

        'url' => $caminhoRelativo,

        'mime' => $mime,

        'tamanho' => filesize($destino)

    ];
}

/*
|--------------------------------------------------------------------------
| Remover imagem
|--------------------------------------------------------------------------
*/

function remover_imagem(string $caminho): bool
{
    $arquivo = __DIR__ . '/../' . ltrim($caminho, '/');

    if (!is_file($arquivo)) {
        return false;
    }

    return unlink($arquivo);
}

/*
|--------------------------------------------------------------------------
| Verificar se imagem existe
|--------------------------------------------------------------------------
*/

function imagem_existe(string $caminho): bool
{
    return is_file(
        __DIR__ . '/../' . ltrim($caminho, '/')
    );
}

/*
|--------------------------------------------------------------------------
| Informações da imagem
|--------------------------------------------------------------------------
*/

function obter_informacoes_imagem(string $caminho): ?array
{
    $arquivo = __DIR__ . '/../' . ltrim($caminho, '/');

    if (!is_file($arquivo)) {
        return null;
    }

    $dados = @getimagesize($arquivo);

    if ($dados === false) {
        return null;
    }

    return [

        'largura' => $dados[0],

        'altura' => $dados[1],

        'mime' => $dados['mime'] ?? '',

        'tamanho' => filesize($arquivo)

    ];
}

/*
|--------------------------------------------------------------------------
| Fim do arquivo
|--------------------------------------------------------------------------
*/