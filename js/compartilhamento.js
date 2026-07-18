document.addEventListener('DOMContentLoaded', function(){
    const botoesCompartilhamento = document.querySelectorAll('.botao-compartilhar-nativo');
    botoesCompartilhamento.forEach(botao => {
        botao.addEventListener('click', async function(e){
            e.preventDefault();
            if(navigator.share){
                try{
                    await navigator.share({
                        title: document.title,
                        text: document.querySelector('meta[name="description"]').content,
                        url: window.location.href
                    })
                } catch (erro) {
                    console.log('Compartilhamento cancelado');
                }
            } else {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert('Link copiado para a área de transferência!');
                })
            }
        })
    })
})
