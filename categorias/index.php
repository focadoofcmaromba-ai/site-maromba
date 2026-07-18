<?php
require_once __DIR__ . '/../config/configuracao.php';
require_once __DIR__ . '/../includes/cache.php';

$identificador_cache = 'pagina-lista-categorias';
if(verificar_cache_valido($identificador_cache)){
    echo obter_conteudo_cache($identificador_cache);
    exit;
}
ob_start();

$titulo_pagina = 'Todas as Categorias de Artigos | ' . $configuracao_site['nome_site'];
$descricao_pagina = 'Navegue por todas as categorias de conteúdo sobre musculação, treinos, hipertrofia e saúde.';
$itens_breadcrumb = [['nome' => 'Categorias']];

require_once __DIR__ . '/../includes/cabecalho.php';
?>
<main class="pagina-lista-categorias">
    <div class="container">
        <?php echo gerar_breadcrumb($itens_breadcrumb); ?>
        <header class="cabecalho-lista">
            <h1>Todas as Categorias</h1>
            <p>Escolha um tema para explorar conteúdos específicos da musculação</p>
        </header>
        <div class="grid-cards-categorias">
            <?php
            $consulta = $conexao->query("SELECT c.id, c.nome, c.slug, c.descricao, COUNT(a.id) as total_artigos
                                        FROM categorias c
                                        LEFT JOIN artigos a ON c.id = a.categoria_id AND a.status = 'publicado'
                                        GROUP BY c.id
                                        ORDER BY c.nome ASC");
            while($categoria = $consulta->fetch_assoc()){
            ?>
            <div class="card-categoria">
                <a href="/categorias/<?php echo htmlspecialchars($categoria['slug']); ?>">
                    <h3><?php echo htmlspecialchars($categoria['nome']); ?></h3>
                    <p><?php echo htmlspecialchars($categoria['descricao']); ?></p>
                    <span class="qtd-artigos"><?php echo $categoria['total_artigos']; ?> artigos</span>
                </a>
            </div>
            <?php } ?>
        </div>
    </div>
</main>
<?php
require_once __DIR__ . '/../includes/rodape.php';
$conteudo_buffer = ob_get_clean();
salvar_conteudo_cache($identificador_cache, $conteudo_buffer);
echo $conteudo_buffer;
?>
