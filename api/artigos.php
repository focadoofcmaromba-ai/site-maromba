<?php
// Endpoint da API para manipulação de artigos
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../php/configuracao.php";

$metodo_requisicao = $_SERVER['REQUEST_METHOD'];
$caminho_requisicao = $_SERVER['PATH_INFO'] ?? '';

switch ($metodo_requisicao) {
    // Retorna lista de artigos ou artigo individual
    case 'GET':
        if (!empty($caminho_requisicao) && is_numeric(trim($caminho_requisicao, '/'))) {
            $id_artigo = sanitizar_entrada(trim($caminho_requisicao, '/'));
            $consulta = $conexao->prepare("SELECT a.*, c.nome as categoria_nome, c.slug as categoria_slug, s.nome as subcategoria_nome, s.slug as subcategoria_slug, aut.nome as autor_nome 
                                           FROM artigos a 
                                           INNER JOIN categorias c ON a.categoria_id = c.id 
                                           LEFT JOIN subcategorias s ON a.subcategoria_id = s.id 
                                           INNER JOIN autores aut ON a.autor_id = aut.id 
                                           WHERE a.id = ? AND a.status = 'publicado'");
            $consulta->bind_param("i", $id_artigo);
            $consulta->execute();
            $resultado = $consulta->get_result()->fetch_assoc();

            if (!$resultado) {
                echo json_encode(['status' => 'erro', 'mensagem' => 'Artigo não encontrado']);
                exit;
            }

            // Busca tags associadas ao artigo
            $consulta_tags = $conexao->prepare("SELECT t.nome, t.slug FROM tags t INNER JOIN artigos_tags atg ON t.id = atg.tag_id WHERE atg.artigo_id = ?");
            $consulta_tags->bind_param("i", $id_artigo);
            $consulta_tags->execute();
            $tags = $consulta_tags->get_result()->fetch_all(MYSQLI_ASSOC);
            $resultado['tags'] = $tags;

            echo json_encode(['status' => 'sucesso', 'dados' => $resultado]);
        } else {
            $pagina = isset($_GET['pagina']) ? sanitizar_entrada($_GET['pagina']) : 1;
            $offset = ($pagina - 1) * $configuracao_site['quantidade_artigos_por_pagina'];

            $consulta = $conexao->prepare("SELECT a.id, a.titulo, a.slug, a.resumo, a.imagem_destacada, a.data_publicacao, aut.nome as autor_nome 
                                           FROM artigos a 
                                           INNER JOIN autores aut ON a.autor_id = aut.id 
                                           WHERE a.status = 'publicado' 
                                           ORDER BY a.data_publicacao DESC 
                                           LIMIT ? OFFSET ?");
            $consulta->bind_param("ii", $configuracao_site['quantidade_artigos_por_pagina'], $offset);
            $consulta->execute();
            $lista_artigos = $consulta->get_result()->fetch_all(MYSQLI_ASSOC);

            // Obtém total de registros para paginação
            $total_consulta = $conexao->query("SELECT COUNT(*) as total FROM artigos WHERE status = 'publicado'");
            $total_registros = $total_consulta->fetch_assoc()['total'];
            $total_paginas = ceil($total_registros / $configuracao_site['quantidade_artigos_por_pagina']);

            echo json_encode([
                'status' => 'sucesso',
                'dados' => $lista_artigos,
                'paginacao' => [
                    'pagina_atual' => $pagina,
                    'total_paginas' => $total_paginas,
                    'total_registros' => $total_registros
                ]
            ]);
        }
        break;
    
    // Os métodos POST, PUT e DELETE ficarão prontos para uso no painel administrativo futuro
    case 'POST':
    case 'PUT':
    case 'DELETE':
        echo json_encode(['status' => 'sucesso', 'mensagem' => 'Método disponível para integração com painel administrativo futuro']);
        break;

    default:
        echo json_encode(['status' => 'erro', 'mensagem' => 'Método de requisição inválido']);
}
exit;
?>
