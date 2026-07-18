document.addEventListener('DOMContentLoaded', function(){
    const containerPaginacao = document.querySelector('.area-paginacao');
    if(!containerPaginacao) return;

    const containerGridArtigos = document.querySelector('.grid-cards-artigos');
    if(!containerGridArtigos) return;

    containerPaginacao.addEventListener('click', async function(e){
        const link = e.target.closest('.link-paginacao');
        if(!link) return;
        e.preventDefault();

        const urlPaginacao = link.getAttribute('href');
        containerGridArtigos.style.opacity = '0.5';
        containerGridArtigos.style.pointerEvents = 'none';

        const resposta = await fetch(urlPaginacao);
        const htmlPagina = await resposta.text();
        const parser = new DOMParser();
        const documentoNovo = parser.parseFromString(htmlPagina, 'text/html');
        const novoGrid = documentoNovo.querySelector('.grid-cards-artigos');
        const novaPaginacao = documentoNovo.querySelector('.area-paginacao');

        containerGridArtigos.innerHTML = novoGrid.innerHTML;
        containerPaginacao.innerHTML = novaPaginacao.innerHTML;
        containerGridArtigos.style.opacity = '1';
        containerGridArtigos.style.pointerEvents = 'all';

        window.history.pushState({}, '', urlPaginacao);
        window.scrollTo({top: containerGridArtigos.offsetTop - 80, behavior: 'smooth'});
    })
})
