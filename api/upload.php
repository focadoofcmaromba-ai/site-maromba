<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../config/conexao.php";
require_once "../config/configuracao.php";
require_once "../includes/upload-imagens.php";

if (!isset($_FILES['arquivo'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Nenhum arquivo enviado'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$resultado_upload = processar_upload_imagem($_FILES['arquivo']);
echo json_encode($resultado_upload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
?>
