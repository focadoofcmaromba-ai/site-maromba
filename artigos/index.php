<?php
// Página de listagem de todos os artigos
require_once __DIR__ . '/../php/configuracao.php';
require_once __DIR__ . '/../php/sistema-cache.php';

// Identificador único do cache baseado nos parâmetros da URL
$identificador_cache = 'pagina-lista-artigos-' . md5($_SERVER['REQUEST_URI']);

// Verifica se cache válido existe
if(verificar_cache_valido($identificador_cache)){
    echo obter_conteudo_cache($identificador_cache);
    exit;
}

// Inicia buffer de saída para salvar todo o conteúdo gerado no cache
ob_start();

// Configura valores padrão da página
$titulo_pagina = 'Todos os Artigos sobre Musculação | ' . $configuracao_site['nome_site'];
$descricao_pagina = 'Confira todos os conteúdos completos sobre treinos, hipertrofia, nutrição, suplementação e saúde para você evoluir na musculação.';

// Sobrescreve valores padrão do cabeçalho de SEO
$titulo_pagina = $titulo_pagina;
$descricao_pagina = $descricao_pagina;

// Carrega o cabeçalho SEO
require_once __DIR__ . '/../php/cabecalho_seo.php';
?>

<main class="conteudo-principal-lista-artigos">
    <div class="container-lista-artigos">
        <header class="cabecalho-lista-artigos">
            <h1>Todos os Artigos</h1>
            <p>Conteúdo atualizado e verificado para você alcançar seus objetivos na musculação</p>
        </header>

        <!-- Área de filtros e pesquisa -->
        <section class="area-filtros">
            <form action="/artigos/" method="get" class="form-filtros">
                <div class="grupo-filtro">
                    <label>Pesquisar artigo</label>
                    <input type="text" name="pesquisa" placeholder="Digite o que você procura..." value="<?php echo isset($_GET['pesquisa']) ? sanitizar_entrada($_GET['pesquisa']) : ''; ?>">
                </div>

                <div class="grupo-filtro">
                    <label>Ordenar por</label>
                    <select name="ordenar">
                        <option value="mais-recentes">Mais recentes</option>
                        <option value="mais-antigos">Mais antigos</option>
                        <option value="mais-visualizados">Mais visualizados</option>
                    </select>
                </div>

                <button type="submit" class="botao-filtrar">Filtrar resultados</button>
            </form>
        </section>

        <!-- Grid de cards de artigos -->
        <div class="grid-cards-artigos">
            <?php
            // Monta consulta com filtros
            $condicoes = ['a.status = "publicado"'];
            $parametros = [];
            $tipo_parametros = '';

            if(!empty($_GET['pesquisa'])){
                $pesquisa = sanitizar_entrada($_GET['pesquisa']);
                $condicoes[] = '(a.titulo LIKE ? OR a.resumo LIKE ?)';
                $parametros[] = "%$pesquisa%";
                $parametros[] = "%$pesquisa%";
                $tipo_parametros .= 'ss';
            }

            // Define ordem dos resultados
            $ordenacao = 'a.data_publicacao DESC';
            if(isset($_GET['ordenar']) && $_GET['ordenar'] == 'mais-antigos'){
                $ordenacao = 'a.data_publicacao ASC';
            }elseif(isset($_GET['ordenar']) && $_GET['ordenar'] == 'mais-visualizados'){
                $ordenacao = 'qtd_visualizacoes DESC';
            }

            // Trata paginação
            $pagina_atual = isset($_GET['pagina']) ? max(1, (int)sanitizar_entrada($_GET['pagina'])) : 1;
            $offset = ($pagina_atual - 1) * $configuracao_site['quantidade_artigos_por_pagina'];

            $condicao_sql = implode(' AND ', $condicoes);
            $consulta = $conexao->prepare("SELECT a.id, a.titulo, a.slug, a.resumo, a.imagem_destacada, a.data_publicacao, a.tempo_leitura, c.nome as categoria_nome, c.slug as categoria_slug, aut.nome as autor_nome, COUNT(v.id) as qtd_visualizacoes
                                           FROM artigos a
                                           INNER JOIN categorias c ON a.categoria_id = c.id
                                           INNER JOIN autores aut ON a.autor_id = aut.id
                                           LEFT JOIN visualizacoes v ON a.id = v.artigo_id
                                           WHERE $condicao_sql
                                           GROUP BY a.id
                                           ORDER BY $ordenacao
                                           LIMIT $configuracao_site[quantidade_artigos_por_pagina] OFFSET $offset");

            if(!empty($parametros)){
                $consulta->bind_param($tipo_parametros, ...$parametros);
            }
            $consulta->execute();
            $lista_artigos = $consulta->get_result();

            while($artigo = $lista_artigos->fetch_assoc()){
            ?>
            <article class="card-artigo">
                <a href="/artigos/<?php echo $artigo['slug']; ?>">
                    <img src="<?php echo $artigo['imagem_destacada']; ?>" alt="<?php echo $artigo['titulo']; ?>" loading="lazy" class="imagem-card-artigo">
                    <div class="conteudo-card-artigo">
                        <span class="categoria-card"><?php echo $artigo['categoria_nome']; ?></span>
                        <h2><?php echo $artigo['titulo']; ?></h2>
                        <p><?php echo $artigo['resumo']; ?></p>
                        <div class="dados-card-artigo">
                            <span>Por <?php echo $artigo['autor_nome']; ?></span>
                            <span><?php echo date('d/m/Y', strtotime($artigo['data_publicacao'])); ?></span>
                            <span><?php echo $artigo['tempo_leitura']; ?> min de leitura</span>
                        </div>
                    </div>
                </a>
            </article>
            <?php } ?>
        </div>

        <!-- Sistema de paginação -->
        <div class="area-paginacao">
            <?php
            $consulta_total = $conexao->prepare("SELECT COUNT(DISTINCT a.id) as total FROM artigos a WHERE $condicao_sql");
            if(!empty($parametros)){
                $consulta_total->bind_param($tipo_parametros, ...$parametros);
            }
            $consulta_total->execute();
            $total_registros = $consulta_total->get_result()->fetch_assoc()['total'];
            $total_paginas = ceil($total_registros / $configuracao_site['quantidade_artigos_por_pagina']);

            if($total_paginas > 1){
                for($i = 1; $i <= $total_paginas; $i++){
                    $classe_atual = $i == $pagina_atual ? 'pagina-atual' : '';
                    echo '<a href="?pagina='.$i.'" class="link-paginacao '.$classe_atual.'">'.$i.'</a>';
                }
            }
            ?>
        </div>
    </div>
</main>

<?php
// Carrega rodapé
require_once __DIR__ . '/../php/rodape.php';

// Salva todo o conteúdo gerado no cache
$conteudo_buffer = ob_get_clean();
salvar_conteudo_cache($identificador_cache, $conteudo_buffer);
echo $conteudo_buffer;
?>
