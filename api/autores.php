<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../config/conexao.php";

$metodo_requisicao = $_SERVER['REQUEST_METHOD'];
$caminho_requisicao = $_SERVER['PATH_INFO'] ?? '';

switch ($metodo_requisicao) {
    case 'GET':
        if (!empty($caminho_requisicao) && is_numeric(trim($caminho_requisicao, '/'))) {
            $id_autor = trim($caminho_requisicao, '/');
            $consulta = $conexao->prepare("SELECT * FROM autores WHERE id = ?");
            $consulta->bind_param("i", $id_autor);
            $consulta->execute();
            $resultado = $consulta->get_result()->fetch_assoc();

            if (!$resultado) {
                echo json_encode(['status' => 'erro', 'mensagem' => 'Autor não encontrado'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $qtd_artigos = $conexao->prepare("SELECT COUNT(*) as total FROM artigos WHERE autor_id = ? AND status = 'publicado'");
            $qtd_artigos->bind_param("i", $id_autor);
            $qtd_artigos->execute();
            $resultado['quantidade_artigos'] = $qtd_artigos->get_result()->fetch_assoc()['total'];

            echo json_encode(['status' => 'sucesso', 'dados' => $resultado], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } else {
            $consulta = $conexao->query("SELECT * FROM autores ORDER BY nome ASC");
            $lista_autores = $consulta->get_result()->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['status' => 'sucesso', 'dados' => $lista_autores], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        break;

    default:
        echo json_encode(['status' => 'erro', 'mensagem' => 'Método de requisição inválido'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
exit;
?>
