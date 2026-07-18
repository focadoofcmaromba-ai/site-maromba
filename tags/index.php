<?php
require_once __DIR__ . '/../config/configuracao.php';
require_once __DIR__ . '/../includes/cache.php';

$identificador_cache = 'pagina-lista-tags';
if(verificar_cache_valido($identificador_cache)){
    echo obter_conteudo_cache($identificador_cache);
    exit;
}
ob_start();

$titulo_pagina = 'Todas as Tags de Conteúdo | ' . $configuracao_site['nome_site'];
$descricao_pagina = 'Navegue por temas específicos de musculação através das tags de conteúdo.';
$itens_breadcrumb = [['nome' => 'Tags']];

require_once __DIR__ . '/../includes/cabecalho.php';
?>
<main class="pagina-nuvem-tags">
    <div class="container">
        <?php echo gerar_breadcrumb($itens_breadcrumb); ?>
        <header class="cabecalho-lista">
            <h1>Nuvem de Tags</h1>
            <p>Encontre conteúdos por temas específicos</p>
        </header>
        <div class="nuvem-tags">
            <?php
            $consulta = $conexao->query("SELECT t.nome, t.slug, COUNT(atg.artigo_id) as total_artigos
                                        FROM tags t
                                        LEFT JOIN artigos_tags atg ON t.id = atg.tag_id
                                        GROUP BY t.id
                                        ORDER BY total_artigos DESC, t.nome ASC");
            while($tag = $consulta->fetch_assoc()){
                // Tamanho dinâmico da tag baseado na quantidade de artigos
                $tamanho_fonte = 14 + ($tag['total_artigos'] * 2);
                echo '<a href="/tags/'.htmlspecialchars($tag['slug']).'" style="font-size: '.$tamanho_fonte.'px;">'.htmlspecialchars($tag['nome']).' ('.$tag['total_artigos'].')</a>';
            }
            ?>
        </div>
    </div>
</main>
<?php
require_once __DIR__ . '/../includes/rodape.php';
$conteudo_buffer = ob_get_clean();
salvar_conteudo_cache($identificador_cache, $conteudo_buffer);
echo $conteudo_buffer;
?>
