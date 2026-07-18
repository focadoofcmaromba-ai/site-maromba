<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../config/conexao.php";

$dados_estatisticos = [];

// Total de artigos publicados
$total_artigos = $conexao->query("SELECT COUNT(*) as total FROM artigos WHERE status = 'publicado'")->fetch_assoc()['total'];
$dados_estatisticos['total_artigos'] = $total_artigos;

// Total de autores
$total_autores = $conexao->query("SELECT COUNT(*) as total FROM autores")->fetch_assoc()['total'];
$dados_estatisticos['total_autores'] = $total_autores;

// Total de categorias
$total_categorias = $conexao->query("SELECT COUNT(*) as total FROM categorias")->fetch_assoc()['total'];
$dados_estatisticos['total_categorias'] = $total_categorias;

// Total de visualizações de todos os artigos
$total_visualizacoes = $conexao->query("SELECT COUNT(*) as total FROM visualizacoes")->fetch_assoc()['total'];
$dados_estatisticos['total_visualizacoes'] = $total_visualizacoes;

// Top 10 artigos mais lidos
$artigos_mais_lidos = $conexao->query("SELECT a.id, a.titulo, a.slug, COUNT(v.id) as total_visitas
                                       FROM artigos a
                                       INNER JOIN visualizacoes v ON a.id = v.artigo_id
                                       WHERE a.status = 'publicado'
                                       GROUP BY a.id
                                       ORDER BY total_visitas DESC
                                       LIMIT 10")->fetch_all(MYSQLI_ASSOC);
$dados_estatisticos['artigos_mais_lidos'] = $artigos_mais_lidos;

echo json_encode(['status' => 'sucesso', 'dados' => $dados_estatisticos], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
?>
