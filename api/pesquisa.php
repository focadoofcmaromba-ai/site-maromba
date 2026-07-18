<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../config/conexao.php";
require_once "../includes/funcoes.php";

$termo_pesquisa = isset($_GET['q']) ? sanitizar_entrada($_GET['q']) : '';
if (strlen(trim($termo_pesquisa)) < 2) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'O termo de pesquisa precisa ter pelo menos 2 caracteres'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$resultados = [];

// Busca artigos correspondentes
$consulta_artigos = $conexao->prepare("SELECT id, titulo, slug, imagem_destacada FROM artigos WHERE status = 'publicado' AND (titulo LIKE ? OR resumo LIKE ?) LIMIT 10");
$termo_busca = "%$termo_pesquisa%";
$consulta_artigos->bind_param("ss", $termo_busca, $termo_busca);
$consulta_artigos->execute();
$resultados['artigos'] = $consulta_artigos->get_result()->fetch_all(MYSQLI_ASSOC);

// Busca categorias correspondentes
$consulta_categorias = $conexao->prepare("SELECT id, nome, slug FROM categorias WHERE nome LIKE ? LIMIT 5");
$consulta_categorias->bind_param("s", $termo_busca);
$consulta_categorias->execute();
$resultados['categorias'] = $consulta_categorias->get_result()->fetch_all(MYSQLI_ASSOC);

// Busca tags correspondentes
$consulta_tags = $conexao->prepare("SELECT id, nome, slug FROM tags WHERE nome LIKE ? LIMIT 5");
$consulta_tags->bind_param("s", $termo_busca);
$consulta_tags->execute();
$resultados['tags'] = $consulta_tags->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode(['status' => 'sucesso', 'dados' => $resultados], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
?>
