// Função nativa para lazy load de imagens
document.addEventListener('DOMContentLoaded', function(){
    const imagens = document.querySelectorAll('img[loading="lazy"]');
    const observador = new IntersectionObserver((entradas) => {
        entradas.forEach(entrada => {
            if(entrada.isIntersecting){
                entrada.target.src = entrada.target.dataset.src || entrada.target.src;
                observador.unobserve(entrada.target);
            }
        })
    })
    imagens.forEach(img => observador.observe(img));
})
