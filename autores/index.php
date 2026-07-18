<?php
require_once __DIR__ . '/../config/configuracao.php';
require_once __DIR__ . '/../includes/cache.php';

$identificador_cache = 'pagina-lista-autores';
if(verificar_cache_valido($identificador_cache)){
    echo obter_conteudo_cache($identificador_cache);
    exit;
}
ob_start();

$titulo_pagina = 'Nossos Autores | ' . $configuracao_site['nome_site'];
$descricao_pagina = 'Conheça os especialistas que produzem os conteúdos sobre musculação, treinos e saúde do nosso site.';
$itens_breadcrumb = [['nome' => 'Autores']];

require_once __DIR__ . '/../includes/cabecalho.php';
?>
<main class="pagina-lista-autores">
    <div class="container">
        <?php echo gerar_breadcrumb($itens_breadcrumb); ?>
        <header class="cabecalho-lista">
            <h1>Nossos Autores</h1>
            <p>Especialistas qualificados para trazer conteúdo verificado e seguro para você</p>
        </header>
        <div class="grid-cards-autores">
            <?php
            $consulta = $conexao->query("SELECT aut.*, COUNT(a.id) as total_artigos
                                        FROM autores aut
                                        LEFT JOIN artigos a ON aut.id = a.autor_id AND a.status = 'publicado'
                                        GROUP BY aut.id
                                        ORDER BY total_artigos DESC, aut.nome ASC");
            while($autor = $consulta->fetch_assoc()){
            ?>
            <div class="card-autor">
                <a href="/autores/<?php echo gerar_slug($autor['nome']); ?>-<?php echo $autor['id']; ?>">
                    <?php if(!empty($autor['foto'])): ?>
                        <img src="<?php echo htmlspecialchars($autor['foto']); ?>" alt="<?php echo htmlspecialchars($autor['nome']); ?>" loading="lazy">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($autor['nome']); ?></h3>
                    <p><?php echo htmlspecialchars($autor['bio']); ?></p>
                    <span class="qtd-artigos"><?php echo $autor['total_artigos']; ?> artigos publicados</span>
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
