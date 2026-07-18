document.addEventListener('DOMContentLoaded', function(){
    const campoPesquisa = document.querySelector('#campo-pesquisa-autocomplete');
    if(!campoPesquisa) return;

    let timerDebounce;
    const containerSugestoes = document.createElement('div');
    containerSugestoes.classList.add('sugestoes-pesquisa');
    containerSugestoes.style.cssText = 'position:absolute; top:100%; left:0; width:100%; background:white; box-shadow:0 4px 12px rgba(0,0,0,0.1); border-radius:8px; margin-top:5px; display:none; z-index:999; max-height:400px; overflow-y:auto;';
    campoPesquisa.parentElement.style.position = 'relative';
    campoPesquisa.parentElement.appendChild(containerSugestoes);

    campoPesquisa.addEventListener('input', function(){
        clearTimeout(timerDebounce);
        const termo = this.value.trim();
        if(termo.length < 2){
            containerSugestoes.style.display = 'none';
            return;
        }

        timerDebounce = setTimeout(async function(){
            const resposta = await fetch(`/api/pesquisa.php?q=${encodeURIComponent(termo)}`);
            const dados = await resposta.json();
            
            if(dados.status !== 'sucesso') return;
            containerSugestoes.innerHTML = '';

            if(dados.dados.artigos.length > 0){
                const tituloSecao = document.createElement('h4');
                tituloSecao.textContent = 'Artigos';
                tituloSecao.style.padding = '12px 15px 8px 15px';
                tituloSecao.style.color = '#666';
                tituloSecao.style.fontSize = '0.9rem';
                tituloSecao.style.margin = 0;
                containerSugestoes.appendChild(tituloSecao);

                dados.dados.artigos.forEach(artigo => {
                    const item = document.createElement('a');
                    item.href = `/artigos/${artigo.slug}`;
                    item.textContent = artigo.titulo;
                    item.style.display = 'block';
                    item.style.padding = '10px 15px';
                    item.style.textDecoration = 'none';
                    item.style.color = '#1a1a1a';
                    item.addEventListener('mouseover', () => item.style.backgroundColor = '#f3f4f6');
                    item.addEventListener('mouseout', () => item.style.backgroundColor = 'transparent');
                    containerSugestoes.appendChild(item);
                })
            }

            containerSugestoes.style.display = 'block';
        }, 300);
    })

    document.addEventListener('click', function(evento){
        if(!campoPesquisa.contains(evento.target) && !containerSugestoes.contains(evento.target)){
            containerSugestoes.style.display = 'none';
        }
    })
})
