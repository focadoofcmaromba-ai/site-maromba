<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Sanitização
|--------------------------------------------------------------------------
*/

function sanitizar_entrada(?string $valor): string
{
    $valor = trim((string)$valor);
    $valor = strip_tags($valor);

    return htmlspecialchars(
        $valor,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

/*
|--------------------------------------------------------------------------
| Escape para saída HTML
|--------------------------------------------------------------------------
*/

function escapar(string $texto): string
{
    return htmlspecialchars(
        $texto,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

/*
|--------------------------------------------------------------------------
| Gerador de Slug
|--------------------------------------------------------------------------
*/

function gerar_slug(string $texto): string
{
    $texto = mb_strtolower($texto, 'UTF-8');

    $texto = iconv(
        'UTF-8',
        'ASCII//TRANSLIT//IGNORE',
        $texto
    );

    $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);

    $texto = trim($texto, '-');

    return $texto;
}

/*
|--------------------------------------------------------------------------
| Limita caracteres
|--------------------------------------------------------------------------
*/

function limitar_texto(
    string $texto,
    int $limite = 150
): string
{
    $texto = trim(strip_tags($texto));

    if (mb_strlen($texto) <= $limite) {
        return $texto;
    }

    return mb_substr($texto, 0, $limite) . '...';
}

/*
|--------------------------------------------------------------------------
| Formatação de Datas
|--------------------------------------------------------------------------
*/

function formatar_data(
    string $data,
    string $formato = 'd/m/Y'
): string
{
    return date(
        $formato,
        strtotime($data)
    );
}

/*
|--------------------------------------------------------------------------
| Tempo de leitura
|--------------------------------------------------------------------------
*/

function calcular_tempo_leitura(
    string $conteudo
): int
{
    $palavras = str_word_count(
        strip_tags($conteudo)
    );

    return max(
        1,
        (int)ceil($palavras / 200)
    );
}

/*
|--------------------------------------------------------------------------
| Registrar Visualização de Artigo
|--------------------------------------------------------------------------
*/

function registrar_visualizacao_artigo(int $artigoId): void
{
    global $conexao;

    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $consulta = $conexao->prepare("
        INSERT INTO visualizacoes
        (
            artigo_id,
            ip,
            user_agent,
            data_visualizacao
        )
        VALUES
        (?, ?, ?, NOW())
    ");

    $consulta->bind_param(
        'iss',
        $artigoId,
        $ip,
        $userAgent
    );

    $consulta->execute();
}

/*
|--------------------------------------------------------------------------
| Total de Visualizações
|--------------------------------------------------------------------------
*/

function obter_total_visualizacoes(int $artigoId): int
{
    global $conexao;

    $consulta = $conexao->prepare("
        SELECT COUNT(*) AS total
        FROM visualizacoes
        WHERE artigo_id = ?
    ");

    $consulta->bind_param(
        'i',
        $artigoId
    );

    $consulta->execute();

    $resultado = $consulta
        ->get_result()
        ->fetch_assoc();

    return (int)$resultado['total'];
}

/*
|--------------------------------------------------------------------------
| Artigos Relacionados
|--------------------------------------------------------------------------
*/

function buscar_artigos_relacionados(
    int $artigoId,
    int $categoriaId,
    int $limite = 6
): array
{
    global $conexao;

    $consulta = $conexao->prepare("
        SELECT
            id,
            titulo,
            slug,
            resumo,
            imagem_destacada,
            data_publicacao,
            tempo_leitura
        FROM artigos
        WHERE
            categoria_id = ?
            AND id <> ?
            AND status = 'publicado'
        ORDER BY RAND()
        LIMIT ?
    ");

    $consulta->bind_param(
        'iii',
        $categoriaId,
        $artigoId,
        $limite
    );

    $consulta->execute();

    return $consulta
        ->get_result()
        ->fetch_all(MYSQLI_ASSOC);
}

/*
|--------------------------------------------------------------------------
| Últimos Artigos
|--------------------------------------------------------------------------
*/

function obter_ultimos_artigos(
    int $limite = 5
): array
{
    global $conexao;

    $consulta = $conexao->prepare("
        SELECT
            id,
            titulo,
            slug,
            resumo,
            imagem_destacada,
            data_publicacao,
            tempo_leitura
        FROM artigos
        WHERE status = 'publicado'
        ORDER BY data_publicacao DESC
        LIMIT ?
    ");

    $consulta->bind_param(
        'i',
        $limite
    );

    $consulta->execute();

    return $consulta
        ->get_result()
        ->fetch_all(MYSQLI_ASSOC);
}

/*
|--------------------------------------------------------------------------
| Categorias
|--------------------------------------------------------------------------
*/

function obter_categorias(): array
{
    global $conexao;

    $consulta = $conexao->query("
        SELECT
            c.*,
            COUNT(a.id) AS total_artigos
        FROM categorias c
        LEFT JOIN artigos a
            ON a.categoria_id = c.id
            AND a.status = 'publicado'
        GROUP BY c.id
        ORDER BY c.nome ASC
    ");

    return $consulta->fetch_all(MYSQLI_ASSOC);
}

function obter_categoria_por_slug(string $slug): ?array
{
    global $conexao;

    $consulta = $conexao->prepare("
        SELECT *
        FROM categorias
        WHERE slug = ?
        LIMIT 1
    ");

    $consulta->bind_param(
        's',
        $slug
    );

    $consulta->execute();

    $resultado = $consulta
        ->get_result()
        ->fetch_assoc();

    return $resultado ?: null;
}

/*
|--------------------------------------------------------------------------
| Tags
|--------------------------------------------------------------------------
*/

function obter_tags(): array
{
    global $conexao;

    $consulta = $conexao->query("
        SELECT
            t.*,
            COUNT(at.tag_id) AS total_artigos
        FROM tags t
        LEFT JOIN artigos_tags at
            ON at.tag_id = t.id
        GROUP BY t.id
        ORDER BY t.nome ASC
    ");

    return $consulta->fetch_all(MYSQLI_ASSOC);
}

function obter_tag_por_slug(string $slug): ?array
{
    global $conexao;

    $consulta = $conexao->prepare("
        SELECT *
        FROM tags
        WHERE slug = ?
        LIMIT 1
    ");

    $consulta->bind_param(
        's',
        $slug
    );

    $consulta->execute();

    $resultado = $consulta
        ->get_result()
        ->fetch_assoc();

    return $resultado ?: null;
}

/*
|--------------------------------------------------------------------------
| Autores
|--------------------------------------------------------------------------
*/

function obter_autores(): array
{
    global $conexao;

    $consulta = $conexao->query("
        SELECT
            aut.*,
            COUNT(a.id) AS total_artigos
        FROM autores aut
        LEFT JOIN artigos a
            ON a.autor_id = aut.id
            AND a.status = 'publicado'
        GROUP BY aut.id
        ORDER BY aut.nome ASC
    ");

    return $consulta->fetch_all(MYSQLI_ASSOC);
}

function obter_autor(int $id): ?array
{
    global $conexao;

    $consulta = $conexao->prepare("
        SELECT *
        FROM autores
        WHERE id = ?
        LIMIT 1
    ");

    $consulta->bind_param(
        'i',
        $id
    );

    $consulta->execute();

    $resultado = $consulta
        ->get_result()
        ->fetch_assoc();

    return $resultado ?: null;
}

/*
|--------------------------------------------------------------------------
| Pesquisa
|--------------------------------------------------------------------------
*/

function pesquisar_artigos(
    string $termo,
    int $limite = 10
): array
{
    global $conexao;

    $busca = "%{$termo}%";

    $consulta = $conexao->prepare("
        SELECT
            id,
            titulo,
            slug,
            resumo,
            imagem_destacada,
            data_publicacao,
            tempo_leitura
        FROM artigos
        WHERE
            status = 'publicado'
            AND (
                titulo LIKE ?
                OR resumo LIKE ?
                OR conteudo LIKE ?
            )
        ORDER BY data_publicacao DESC
        LIMIT ?
    ");

    $consulta->bind_param(
        'sssi',
        $busca,
        $busca,
        $busca,
        $limite
    );

    $consulta->execute();

    return $consulta
        ->get_result()
        ->fetch_all(MYSQLI_ASSOC);
}

/*
|--------------------------------------------------------------------------
| Breadcrumb
|--------------------------------------------------------------------------
*/

function gerar_breadcrumb(array $itens): string
{
    $html = '<nav class="navegacao-breadcrumb">';
    $html .= '<a href="/">Início</a>';

    foreach ($itens as $item) {

        $html .= ' <span>&rsaquo;</span> ';

        if (!empty($item['url'])) {

            $html .= '<a href="' .
                escapar($item['url']) .
                '">' .
                escapar($item['nome']) .
                '</a>';

        } else {

            $html .= '<span>' .
                escapar($item['nome']) .
                '</span>';

        }
    }

    $html .= '</nav>';

    return $html;
}

/*
|--------------------------------------------------------------------------
| Paginação
|--------------------------------------------------------------------------
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

    $html = '<div class="area-paginacao">';

    if ($paginaAtual > 1) {

        $html .= '<a class="link-paginacao" href="' .
            $urlBase .
            '?' .
            $parametros .
            ($parametros !== '' ? '&' : '') .
            'pagina=' .
            ($paginaAtual - 1) .
            '">&laquo;</a>';

    }

    for ($i = 1; $i <= $totalPaginas; $i++) {

        $classe = $i === $paginaAtual
            ? 'pagina-atual'
            : '';

        $html .= '<a class="link-paginacao ' .
            $classe .
            '" href="' .
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

    if ($paginaAtual < $totalPaginas) {

        $html .= '<a class="link-paginacao" href="' .
            $urlBase .
            '?' .
            $parametros .
            ($parametros !== '' ? '&' : '') .
            'pagina=' .
            ($paginaAtual + 1) .
            '">&raquo;</a>';

    }

    $html .= '</div>';

    return $html;
}

/*
|--------------------------------------------------------------------------
| SEO
|--------------------------------------------------------------------------
*/

function gerar_titulo_seo(
    string $titulo
): string
{
    global $configuracao_site;

    return $titulo .
        ' | ' .
        $configuracao_site['nome_site'];
}

function gerar_descricao_seo(
    string $descricao,
    int $limite = 160
): string
{
    return limitar_texto(
        strip_tags($descricao),
        $limite
    );
}

function gerar_url_canonica(): string
{
    global $configuracao_site;

    return rtrim(
        $configuracao_site['url_base'],
        '/'
    ) . $_SERVER['REQUEST_URI'];
}

/*
|--------------------------------------------------------------------------
| Meta Robots
|--------------------------------------------------------------------------
*/

function gerar_meta_robots(
    bool $indexar = true,
    bool $seguir = true
): string
{
    $robots = [];

    $robots[] = $indexar
        ? 'index'
        : 'noindex';

    $robots[] = $seguir
        ? 'follow'
        : 'nofollow';

    return implode(', ', $robots);
}

/*
|--------------------------------------------------------------------------
| Open Graph
|--------------------------------------------------------------------------
*/

function obter_imagem_compartilhamento(
    ?string $imagem = null
): string
{
    global $configuracao_site;

    if (
        !empty($imagem)
    ) {
        return $imagem;
    }

    return $configuracao_site['imagem_compartilhamento'];
}

/*
|--------------------------------------------------------------------------
| Resposta JSON
|--------------------------------------------------------------------------
*/

function responder_json(
    array $dados,
    int $status = 200
): never
{
    http_response_code($status);

    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode(
        $dados,
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| Redirecionamento
|--------------------------------------------------------------------------
*/

function redirecionar(
    string $url,
    int $status = 302
): never
{
    header(
        'Location: ' . $url,
        true,
        $status
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| Upload de Imagens
|--------------------------------------------------------------------------
*/

function validar_imagem_upload(
    array $arquivo
): bool
{
    global $configuracao_site;

    if (
        !isset($arquivo['error']) ||
        $arquivo['error'] !== UPLOAD_ERR_OK
    ) {
        return false;
    }

    if (
        $arquivo['size'] >
        $configuracao_site['tamanho_maximo_upload_imagem']
    ) {
        return false;
    }

    $extensao = strtolower(
        pathinfo(
            $arquivo['name'],
            PATHINFO_EXTENSION
        )
    );

    return in_array(
        $extensao,
        $configuracao_site['formatos_permitidos_upload'],
        true
    );
}

function gerar_nome_imagem(
    string $nomeOriginal
): string
{
    $extensao = strtolower(
        pathinfo(
            $nomeOriginal,
            PATHINFO_EXTENSION
        )
    );

    return uniqid(
        'img_',
        true
    ) . '.' . $extensao;
}

/*
|--------------------------------------------------------------------------
| Verificações
|--------------------------------------------------------------------------
*/

function artigo_existe(
    int $id
): bool
{
    global $conexao;

    $consulta = $conexao->prepare("
        SELECT id
        FROM artigos
        WHERE id = ?
        LIMIT 1
    ");

    $consulta->bind_param(
        'i',
        $id
    );

    $consulta->execute();

    return $consulta
        ->get_result()
        ->num_rows > 0;
}

function categoria_existe(
    int $id
): bool
{
    global $conexao;

    $consulta = $conexao->prepare("
        SELECT id
        FROM categorias
        WHERE id = ?
        LIMIT 1
    ");

    $consulta->bind_param(
        'i',
        $id
    );

    $consulta->execute();

    return $consulta
        ->get_result()
        ->num_rows > 0;
}

function autor_existe(
    int $id
): bool
{
    global $conexao;

    $consulta = $conexao->prepare("
        SELECT id
        FROM autores
        WHERE id = ?
        LIMIT 1
    ");

    $consulta->bind_param(
        'i',
        $id
    );

    $consulta->execute();

    return $consulta
        ->get_result()
        ->num_rows > 0;
}

function tag_existe(
    int $id
): bool
{
    global $conexao;

    $consulta = $conexao->prepare("
        SELECT id
        FROM tags
        WHERE id = ?
        LIMIT 1
    ");

    $consulta->bind_param(
        'i',
        $id
    );

    $consulta->execute();

    return $consulta
        ->get_result()
        ->num_rows > 0;
}

/*
|--------------------------------------------------------------------------
| Utilidades
|--------------------------------------------------------------------------
*/

function gerar_uuid(): string
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),

        mt_rand(0, 0xffff),

        mt_rand(0, 0x0fff) | 0x4000,

        mt_rand(0, 0x3fff) | 0x8000,

        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

function cliente_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function usuario_logado(): bool
{
    return isset($_SESSION['usuario_id']);
}

/*
|--------------------------------------------------------------------------
| Cache
|--------------------------------------------------------------------------
*/

function limpar_cache(): bool
{
    global $configuracao_site;

    $diretorio = $configuracao_site['diretorio_cache'];

    if (!is_dir($diretorio)) {
        return false;
    }

    $arquivos = glob($diretorio . '*.cache');

    if ($arquivos === false) {
        return false;
    }

    foreach ($arquivos as $arquivo) {

        if (is_file($arquivo)) {
            @unlink($arquivo);
        }

    }

    return true;
}

function tamanho_cache(): int
{
    global $configuracao_site;

    $diretorio = $configuracao_site['diretorio_cache'];

    if (!is_dir($diretorio)) {
        return 0;
    }

    $total = 0;

    foreach (glob($diretorio . '*') as $arquivo) {

        if (is_file($arquivo)) {
            $total += filesize($arquivo);
        }

    }

    return $total;
}

/*
|--------------------------------------------------------------------------
| Logs
|--------------------------------------------------------------------------
*/

function escrever_log(
    string $arquivo,
    string $mensagem
): void
{
    global $configuracao_site;

    if (
        empty($configuracao_site['logs_ativos'])
    ) {
        return;
    }

    $caminho = rtrim(
        $configuracao_site['diretorio_logs'],
        '/'
    ) . '/' . $arquivo;

    $linha = sprintf(
        "[%s] %s%s",
        date('Y-m-d H:i:s'),
        $mensagem,
        PHP_EOL
    );

    file_put_contents(
        $caminho,
        $linha,
        FILE_APPEND | LOCK_EX
    );
}

function registrar_erro(
    string $mensagem
): void
{
    escrever_log(
        'erros.log',
        $mensagem
    );
}

function registrar_evento(
    string $tipo,
    string $descricao
): void
{
    escrever_log(
        'eventos.log',
        '[' . $tipo . '] ' . $descricao
    );
}

function registrar_api(
    string $metodo,
    string $rota,
    int $status
): void
{
    escrever_log(
        'api.log',
        sprintf(
            '%s %s (%d)',
            $metodo,
            $rota,
            $status
        )
    );
}

function registrar_cache(
    string $acao,
    string $chave
): void
{
    escrever_log(
        'cache.log',
        sprintf(
            '%s - %s',
            $acao,
            $chave
        )
    );
}

function registrar_upload(
    string $arquivo,
    string $status
): void
{
    escrever_log(
        'uploads.log',
        sprintf(
            '%s (%s)',
            $arquivo,
            $status
        )
    );
}

/*
|--------------------------------------------------------------------------
| Estatísticas
|--------------------------------------------------------------------------
*/

function contar_artigos_publicados(): int
{
    global $conexao;

    $resultado = $conexao->query("
        SELECT COUNT(*) AS total
        FROM artigos
        WHERE status='publicado'
    ");

    return (int)
        $resultado
            ->fetch_assoc()['total'];
}

function contar_categorias(): int
{
    global $conexao;

    return (int)
        $conexao
            ->query("SELECT COUNT(*) total FROM categorias")
            ->fetch_assoc()['total'];
}

function contar_tags(): int
{
    global $conexao;

    return (int)
        $conexao
            ->query("SELECT COUNT(*) total FROM tags")
            ->fetch_assoc()['total'];
}

function contar_autores(): int
{
    global $conexao;

    return (int)
        $conexao
            ->query("SELECT COUNT(*) total FROM autores")
            ->fetch_assoc()['total'];
}

/*
|--------------------------------------------------------------------------
| CSRF
|--------------------------------------------------------------------------
*/

function gerar_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {

        $_SESSION['csrf_token'] = bin2hex(
            random_bytes(32)
        );

        $_SESSION['csrf_token_time'] = time();

    }

    return $_SESSION['csrf_token'];
}

function validar_csrf_token(
    ?string $token
): bool
{
    global $configuracao_site;

    if (
        empty($_SESSION['csrf_token']) ||
        empty($_SESSION['csrf_token_time'])
    ) {
        return false;
    }

    if (
        time() - $_SESSION['csrf_token_time'] >
        $configuracao_site['csrf_token_expiracao']
    ) {
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);

        return false;
    }

    return hash_equals(
        $_SESSION['csrf_token'],
        (string)$token
    );
}

/*
|--------------------------------------------------------------------------
| Token Aleatório
|--------------------------------------------------------------------------
*/

function gerar_token(
    int $tamanho = 32
): string
{
    return bin2hex(
        random_bytes($tamanho)
    );
}

/*
|--------------------------------------------------------------------------
| Senhas
|--------------------------------------------------------------------------
*/

function gerar_hash_senha(
    string $senha
): string
{
    return password_hash(
        $senha,
        PASSWORD_DEFAULT
    );
}

function verificar_senha(
    string $senha,
    string $hash
): bool
{
    return password_verify(
        $senha,
        $hash
    );
}

/*
|--------------------------------------------------------------------------
| Requisições HTTP
|--------------------------------------------------------------------------
*/

function metodo_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function metodo_get(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

function requisicao_ajax(): bool
{
    return (
        $_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''
    ) === 'XMLHttpRequest';
}

/*
|--------------------------------------------------------------------------
| URL Atual
|--------------------------------------------------------------------------
*/

function url_atual(): string
{
    global $configuracao_site;

    return rtrim(
        $configuracao_site['url_base'],
        '/'
    ) . $_SERVER['REQUEST_URI'];
}

function dominio_site(): string
{
    global $configuracao_site;

    return rtrim(
        $configuracao_site['url_base'],
        '/'
    );
}

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function ativo_menu(
    string $trecho
): bool
{
    return str_contains(
        $_SERVER['REQUEST_URI'],
        $trecho
    );
}

function valor_array(
    array $dados,
    string $indice,
    mixed $padrao = null
): mixed
{
    return $dados[$indice] ?? $padrao;
}

function converter_bytes(
    int $bytes
): string
{
    $unidades = [
        'B',
        'KB',
        'MB',
        'GB',
        'TB'
    ];

    $i = 0;

    while (
        $bytes >= 1024 &&
        $i < count($unidades) - 1
    ) {
        $bytes /= 1024;
        $i++;
    }

    return round(
        $bytes,
        2
    ) . ' ' . $unidades[$i];
}

/*
|--------------------------------------------------------------------------
| Ambiente
|--------------------------------------------------------------------------
*/

function ambiente_producao(): bool
{
    return !filter_var(
        ini_get('display_errors'),
        FILTER_VALIDATE_BOOLEAN
    );
}

/*
|--------------------------------------------------------------------------
| HTTPS
|--------------------------------------------------------------------------
*/

function conexao_segura(): bool
{
    if (
        !empty($_SERVER['HTTPS']) &&
        $_SERVER['HTTPS'] !== 'off'
    ) {
        return true;
    }

    if (
        ($_SERVER['SERVER_PORT'] ?? 80) == 443
    ) {
        return true;
    }

    return false;
}

/*
|--------------------------------------------------------------------------
| Número aleatório
|--------------------------------------------------------------------------
*/

function numero_aleatorio(
    int $minimo,
    int $maximo
): int
{
    return random_int(
        $minimo,
        $maximo
    );
}

/*
|--------------------------------------------------------------------------
| Data/Hora Atual
|--------------------------------------------------------------------------
*/

function agora(): string
{
    return date('Y-m-d H:i:s');
}

/*
|--------------------------------------------------------------------------
| Versão do Sistema
|--------------------------------------------------------------------------
*/

function versao_sistema(): string
{
    return '1.0.0';
}

/*
|--------------------------------------------------------------------------
| Memória Utilizada
|--------------------------------------------------------------------------
*/

function memoria_utilizada(): string
{
    return converter_bytes(
        memory_get_usage(true)
    );
}

/*
|--------------------------------------------------------------------------
| Tempo de Execução
|--------------------------------------------------------------------------
*/

function tempo_execucao(
    float $inicio
): float
{
    return round(
        microtime(true) - $inicio,
        4
    );
}

/*
|--------------------------------------------------------------------------
| Verifica se uma string é JSON válido
|--------------------------------------------------------------------------
*/

function json_valido(
    string $json
): bool
{
    json_decode($json);

    return json_last_error() === JSON_ERROR_NONE;
}

/*
|--------------------------------------------------------------------------
| Gera resposta 404
|--------------------------------------------------------------------------
*/

function erro404(): never
{
    http_response_code(404);

    require_once __DIR__ . '/../404.php';

    exit;
}

/*
|--------------------------------------------------------------------------
| Gera resposta 403
|--------------------------------------------------------------------------
*/

function erro403(): never
{
    http_response_code(403);

    exit('Acesso negado.');
}

/*
|--------------------------------------------------------------------------
| Gera resposta 500
|--------------------------------------------------------------------------
*/

function erro500(): never
{
    http_response_code(500);

    exit('Erro interno do servidor.');
}

/*
|--------------------------------------------------------------------------
| Final do arquivo
|--------------------------------------------------------------------------
*/