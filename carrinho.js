const listaCarrinho = document.getElementById("listaCarrinho");
const resumoCarrinho = document.getElementById("resumoCarrinho");

let carrinho = JSON.parse(localStorage.getItem("carrinho")) || [];

const cep = document.getElementById("cep");
const frete = document.getElementById("frete");
const totalElemento = document.getElementById("total");
const avisoCep = document.getElementById("avisoCep");

let valorFreteCalculado = 0;

const PROMOCOES = {
    freteGratis: true,
    quantidadeMinima: 3
};

renderizarCarrinho();

function renderizarCarrinho() {

    listaCarrinho.innerHTML = "";

    if (carrinho.length === 0) {

        listaCarrinho.innerHTML = `
            <div style="text-align:center;padding:60px 20px;">
                <h2>Seu carrinho está vazio.</h2>

                <a href="produtos.html" class="btn btn-primary">
                    Continuar Comprando
                </a>
            </div>
        `;

    } else {

        let total = 0;

        carrinho.forEach(item => {

            const subtotal = item.preco * item.quantidade;

            total += subtotal;

            listaCarrinho.innerHTML += `

<div class="compra-card item-carrinho">

    <div class="item-carrinho-imagem-area">

        <img
            src="${item.imagem}"
            alt="${item.nome}"
            class="item-carrinho-imagem">

    </div>

    <div class="item-carrinho-conteudo">

        <div class="item-carrinho-topo">

            <h3 class="item-carrinho-titulo">
                ${item.nome}
            </h3>

            <p class="item-carrinho-variacao">
                ${item.variacao}
            </p>

            <strong class="item-carrinho-subtotal">
                ${subtotal.toLocaleString("pt-BR", {
                    style: "currency",
                    currency: "BRL"
                })}
            </strong>

        </div>

        <div class="item-carrinho-centro">

            <span class="item-carrinho-label">
                Quantidade
            </span>

            <div class="item-carrinho-quantidade">

                <button
                    class="btn btn-outline diminuir"
                    data-id="${item.id}"
                    data-variacao="${item.variacao}">

                    −

                </button>

                <strong>
                    ${item.quantidade}
                </strong>

                <button
                    class="btn btn-outline aumentar"
                    data-id="${item.id}"
                    data-variacao="${item.variacao}">

                    +

                </button>

            </div>

        </div>

        <div class="item-carrinho-rodape">

            <span class="item-carrinho-preco">

                Valor unitário:
                ${item.preco.toLocaleString("pt-BR", {
                    style: "currency",
                    currency: "BRL"
                })}

            </span>

            <button
                class="btn btn-outline remover-item"
                data-id="${item.id}"
                data-variacao="${item.variacao}">

                <i class="fas fa-trash"></i>
                Remover

            </button>

        </div>

    </div>

</div>

`;

        });

        const subtotalElemento = document.getElementById("subtotal");

        subtotalElemento.textContent =
            total.toLocaleString("pt-BR", {
                style: "currency",
                currency: "BRL"
            });

        const quantidadeTotal = carrinho.reduce(
            (soma, item) => soma + item.quantidade,
            0
        );

        let freteAtual = valorFreteCalculado;

        if (
            PROMOCOES.freteGratis &&
            quantidadeTotal >= PROMOCOES.quantidadeMinima &&
            valorFreteCalculado > 0
        ) {

            freteAtual = 0;

            frete.textContent = "Grátis";

        } else {

            frete.textContent =
                valorFreteCalculado > 0
                    ? valorFreteCalculado.toLocaleString("pt-BR", {
                          style: "currency",
                          currency: "BRL"
                      })
                    : "Informe o CEP";

        }

        totalElemento.textContent =
            (total + freteAtual).toLocaleString("pt-BR", {
                style: "currency",
                currency: "BRL"
            });

    }

    document.querySelectorAll(".aumentar").forEach(botao => {

                botao.addEventListener("click", () => {

            const item = carrinho.find(produto =>

                produto.id === botao.dataset.id &&
                produto.variacao === botao.dataset.variacao

            );

            if (item) {

                item.quantidade++;

                localStorage.setItem(
                    "carrinho",
                    JSON.stringify(carrinho)
                );

                renderizarCarrinho();

            }

        });

    });

    document.querySelectorAll(".remover-item").forEach(botao => {

        botao.addEventListener("click", () => {

            const id = botao.dataset.id;

            const variacao = botao.dataset.variacao;

            carrinho = carrinho.filter(item =>

                !(
                    item.id === id &&
                    item.variacao === variacao
                )

            );

            localStorage.setItem(
                "carrinho",
                JSON.stringify(carrinho)
            );

            renderizarCarrinho();

        });

    });

    document.querySelectorAll(".diminuir").forEach(botao => {

        botao.addEventListener("click", () => {

            const item = carrinho.find(produto =>

                produto.id === botao.dataset.id &&
                produto.variacao === botao.dataset.variacao

            );

            if (!item) return;

            if (item.quantidade > 1) {

                item.quantidade--;

            } else {

                carrinho = carrinho.filter(produto =>

                    !(
                        produto.id === item.id &&
                        produto.variacao === item.variacao
                    )

                );

            }

            localStorage.setItem(
                "carrinho",
                JSON.stringify(carrinho)
            );

            renderizarCarrinho();

        });

    });

}

cep.addEventListener("blur", async () => {

    const cepLimpo = cep.value.replace(/\D/g, "");

    if (cepLimpo === "") {

        valorFreteCalculado = 0;

        renderizarCarrinho();

        return;

    }

    if (cepLimpo.length !== 8) {

        valorFreteCalculado = 0;

        frete.textContent = "CEP inválido";

        renderizarCarrinho();

        return;

    }

    try {

        const resposta = await fetch(
            `https://viacep.com.br/ws/${cepLimpo}/json/`
        );

        const dados = await resposta.json();

        if (dados.erro) {

            valorFreteCalculado = 0;

            frete.textContent = "CEP não encontrado";

            renderizarCarrinho();

            return;

        }

        switch (dados.uf) {

            case "SP":
            case "RJ":
            case "MG":
                valorFreteCalculado = 55;
                break;

            case "ES":
            case "PR":
            case "SC":
            case "RS":
                valorFreteCalculado = 65;
                break;

            default:
                valorFreteCalculado = 75;

        }

        avisoCep.style.display = "none";

        renderizarCarrinho();

    } catch (erro) {

        console.error(erro);

        valorFreteCalculado = 0;

        frete.textContent = "Erro ao calcular";

        renderizarCarrinho();

    }

});

cep.addEventListener("input", () => {

    cep.value = cep.value
        .replace(/\D/g, "")
        .replace(/^(\d{5})(\d)/, "$1-$2")
        .substring(0, 9);

});

function validarCep() {

    if (valorFreteCalculado <= 0) {

        avisoCep.textContent =
            "Informe um CEP válido para calcular o frete.";

        avisoCep.style.display = "block";

        cep.focus();

        return false;

    }

    avisoCep.style.display = "none";

    return true;

}

document.getElementById("btnFinalizar").addEventListener("click", () => {

    if (!validarCep()) return;

    let mensagem =
`Olá!

Gostaria de fazer o seguinte pedido:

`;

    let subtotal = 0;

    carrinho.forEach(item => {

        const valorItem = item.preco * item.quantidade;

        subtotal += valorItem;

        mensagem +=
`📦 ${item.nome}
📋 Variação: ${item.variacao}
🔢 Quantidade: ${item.quantidade}
💰 Subtotal: ${valorItem.toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL"
})}

`;

    });

    const quantidadeTotal = carrinho.reduce(
        (soma, item) => soma + item.quantidade,
        0
    );

    let freteAtual = valorFreteCalculado;

    let textoFrete = valorFreteCalculado.toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL"
    });

    if (
        PROMOCOES.freteGratis &&
        quantidadeTotal >= PROMOCOES.quantidadeMinima &&
        valorFreteCalculado > 0
    ) {

        freteAtual = 0;
        textoFrete = "Grátis";

    }

    mensagem +=
`🧾 Subtotal: ${subtotal.toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL"
})}

🚚 Frete: ${textoFrete}

💳 Total: ${(subtotal + freteAtual).toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL"
})}

📍 CEP: ${cep.value}
`;

    const numeroWhatsapp = "5599999999999";

    window.open(
        `https://wa.me/${numeroWhatsapp}?text=${encodeURIComponent(mensagem)}`,
        "_blank"
    );

});