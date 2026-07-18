"use strict";

/*=========================================
CONFIGURAÇÃO
=========================================*/

const Banner={

    tempo:5000,

    fade:600,

    imagens:[],

    desktop:[

        "assets/banners/desktop/BANNER1DYXON.png",
        "assets/banners/desktop/BANNER2DYXON.png",
        "assets/banners/desktop/BANNER3DYXON.png",
        "assets/banners/desktop/BANNER4DYXON.png",
        "assets/banners/desktop/BANNER5DYXON.png"

    ],

    mobile:[

        "assets/banners/mobile/BANNER1DYXON.png",
        "assets/banners/mobile/BANNER2DYXON.png",
        "assets/banners/mobile/BANNER3DYXON.png",
        "assets/banners/mobile/BANNER4DYXON.png",
        "assets/banners/mobile/BANNER5DYXON.png"

    ]

};

/*=========================================
ESTADO
=========================================*/

function atualizarListaBanners(){

    Banner.imagens =

        window.innerWidth <= 768

            ? [...Banner.mobile]

            : [...Banner.desktop];

}

let atual=0;

let timer=null;

let imgA;

let imgB;

let ativa=0;

let indicadores;

let btnPrev;

let btnNext;

/*=========================================
INICIALIZAÇÃO
=========================================*/

document.addEventListener("DOMContentLoaded",()=>{

    atualizarListaBanners();

    imgA=document.getElementById("bannerA");

    imgB=document.getElementById("bannerB");

    indicadores=document.getElementById("bannerIndicators");

    btnPrev=document.getElementById("bannerPrev");

    btnNext=document.getElementById("bannerNext");

    if(
        !imgA||
        !imgB||
        Banner.imagens.length===0
    ){
        return;
    }

    imgA.src=Banner.imagens[0];

    imgA.classList.add("active");

    preload();

    criarIndicadores();

    eventos();

    autoplay();

});

/*=========================================
PRELOAD
=========================================*/

function preload(){

    Banner.imagens.forEach(src=>{

        const i=new Image();

        i.src=src;

    });

}

/*=========================================
INDICADORES
=========================================*/

function criarIndicadores(){

    indicadores.innerHTML="";

    Banner.imagens.forEach((_,i)=>{

        const dot=document.createElement("span");

        dot.className="banner-dot";

        if(i===0){

            dot.classList.add("active");

        }

        dot.onclick=()=>{

            if(i===atual)return;

            atual=i;

            mostrarBanner();

            reiniciar();

        };

        indicadores.appendChild(dot);

    });

}

function atualizarIndicadores(){

    if(!indicadores){
        return;
    }

    indicadores.querySelectorAll(".banner-dot").forEach((dot,i)=>{

        dot.classList.toggle("active",i===atual);

    });

}

/*=========================================
CROSSFADE
=========================================*/

function mostrarBanner(){

    const atualImg=ativa===0?imgA:imgB;

    const proximaImg=ativa===0?imgB:imgA;

    proximaImg.onload=()=>{

        proximaImg.classList.add("active");

        atualImg.classList.remove("active");

        ativa=ativa===0?1:0;

        atualizarIndicadores();

    };

    proximaImg.src=Banner.imagens[atual];

}

/*=========================================
NAVEGAÇÃO
=========================================*/

function proximo(){

    atual++;

    if(atual>=Banner.imagens.length){

        atual=0;

    }

    mostrarBanner();

}

function anterior(){

    atual--;

    if(atual<0){

        atual=Banner.imagens.length-1;

    }

    mostrarBanner();

}

/*=========================================
AUTOPLAY
=========================================*/

function autoplay(){

    parar();

    timer=setInterval(()=>{

        proximo();

    },Banner.tempo);

}

function parar(){

    if(timer){

        clearInterval(timer);

        timer=null;

    }

}

function reiniciar(){

    parar();

    autoplay();

}

/*=========================================
EVENTOS
=========================================*/

function eventos(){

    btnPrev.addEventListener("click",()=>{

        anterior();

        reiniciar();

    });

    btnNext.addEventListener("click",()=>{

        proximo();

        reiniciar();

    });

    [imgA,imgB].forEach(img=>{

        img.addEventListener("mouseenter",()=>{

            parar();

        });

        img.addEventListener("mouseleave",()=>{

            autoplay();

        });

    });

    swipe();

}

/*=========================================
SWIPE
=========================================*/

function swipe(){

    let inicio=0;

    let fim=0;

    [imgA,imgB].forEach(img=>{

        img.addEventListener("touchstart",(e)=>{

            inicio=e.changedTouches[0].clientX;

        });

        img.addEventListener("touchend",(e)=>{

            fim=e.changedTouches[0].clientX;

            if(Math.abs(fim-inicio)<50){

                return;

            }

            if(fim<inicio){

                proximo();

            }else{

                anterior();

            }

            reiniciar();

        });

    });

}

/*=========================================
TECLADO
=========================================*/

document.addEventListener("keydown",(e)=>{

    if(e.key==="ArrowLeft"){

        anterior();

        reiniciar();

    }

    if(e.key==="ArrowRight"){

        proximo();

        reiniciar();

    }

});

/*=========================================
VISIBILIDADE DA ABA
=========================================*/

document.addEventListener("visibilitychange",()=>{

    if(document.hidden){

        parar();

    }else{

        autoplay();

    }

});

/*=========================================
REDIMENSIONAMENTO
=========================================*/

window.addEventListener("resize",()=>{

    const listaAnterior=[...Banner.imagens];

    atualizarListaBanners();

    if(listaAnterior[0]!==Banner.imagens[0]){

        imgA.src=Banner.imagens[atual];

        imgB.src="";

        imgA.classList.add("active");

        imgB.classList.remove("active");

        ativa=0;

    }

    atualizarIndicadores();

});

/*=========================================
INICIALIZA PRIMEIRO BANNER
=========================================*/

if (document.getElementById("bannerIndicators")) {
    atualizarIndicadores();
}