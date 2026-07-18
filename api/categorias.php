<?php
// Endpoint da API para manipulação de categorias
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../php/configuracao.php";

$metodo_requisicao = $_SERVER['REQUEST_METHOD'];

switch ($metodo_requisicao) {
    case 'GET':
        $consulta = $conexao->query("SELECT * FROM categorias ORDER BY nome ASC");
        $lista_categorias = $consulta->get_result()->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['status' => 'sucesso', 'dados' => $lista_categorias]);
        break;
    
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
