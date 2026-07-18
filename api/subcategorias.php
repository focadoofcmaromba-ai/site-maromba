<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../config/conexao.php";

$metodo_requisicao = $_SERVER['REQUEST_METHOD'];

switch ($metodo_requisicao) {
    case 'GET':
        $categoria_filtrar = isset($_GET['categoria_id']) ? (int)$_GET['categoria_id'] : 0;
        if ($categoria_filtrar > 0) {
            $consulta = $conexao->prepare("SELECT * FROM subcategorias WHERE categoria_id = ? ORDER BY nome ASC");
            $consulta->bind_param("i", $categoria_filtrar);
        } else {
            $consulta = $conexao->query("SELECT s.*, c.nome as nome_categoria FROM subcategorias s INNER JOIN categorias c ON s.categoria_id = c.id ORDER BY s.nome ASC");
        }
        $consulta->execute();
        $lista_subcategorias = $consulta->get_result()->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['status' => 'sucesso', 'dados' => $lista_subcategorias], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        break;

    default:
        echo json_encode(['status' => 'erro', 'mensagem' => 'Método de requisição inválido'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
exit;
?>
