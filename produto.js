const produtos = {

    whey: {

        nome: "Whey Protein Concentrado",

        categoria: "Proteínas",

        preco: 190.00,

        precoAntigo: 210.00,

        imagem: "assets/produtos/whey.png",

variacoes: [

    {

        nome: "Chocolate 900g",

        preco: 190.00

    },

    {

        nome: "Baunilha 900g",

        preco: 195.00

    },

    {

        nome: "Morango 900g",

        preco: 200.00

    }

],

        marca: "Growth Supplements",

        peso: "1kg",

        conteudo: "30 porções",

        validade: "Vide embalagem",

        descricao:
            "Suplemento proteico indicado para auxiliar na recuperação muscular e no ganho de massa magra.",

        descricaoCompleta: `
            <p>
                O Whey Protein Concentrado é uma excelente fonte de proteínas de alto valor biológico,
                fornecendo aminoácidos essenciais para recuperação e crescimento muscular.
            </p>

            <p>
                Ideal para atletas e praticantes de musculação que desejam melhorar o desempenho e acelerar
                a recuperação após os treinos.
            </p>
        `,

        beneficios: [

            "Alta concentração de proteínas",

            "Auxilia no ganho de massa muscular",

            "Melhora a recuperação pós-treino",

            "Excelente absorção",

            "Fonte de BCAA"

        ],

        modoUso: `
            Misture 30g (1 scoop) em aproximadamente 200ml de água ou leite.
            Consumir após o treino ou conforme orientação profissional.
        `,

        tabela: [

            ["Porção", "30g"],

            ["Proteínas", "24g"],

            ["Carboidratos", "3g"],

            ["Gorduras Totais", "2g"],

            ["BCAA", "5,4g"]

        ]

    },

    creatina: {

        nome: "Creatina Monohidratada",

        categoria: "Creatinas",

        preco: 190.00,

        precoAntigo: 210.00,

        imagem: "assets/produtos/creatina.png",

variacoes: [

    {

        nome: "150g",

        preco: 89.90

    },

    {

        nome: "300g",

        preco: 159.90

    },

    {

        nome: "500g",

        preco: 239.90

    }

],

        marca: "Growth Supplements",

        peso: "250g",

        conteudo: "50 porções",

        validade: "Vide embalagem",

        descricao:
            "Creatina pura para aumento de força, potência e desempenho físico.",

        descricaoCompleta: `
            <p>
                A Creatina Monohidratada auxilia no aumento do desempenho durante exercícios repetidos
                de curta duração e alta intensidade.
            </p>

            <p>
                Produto ideal para quem busca evolução na força e explosão muscular.
            </p>
        `,

        beneficios: [

            "Mais força",

            "Mais potência",

            "Melhora o desempenho",

            "Auxilia no ganho de massa",

            "Creatina 100% pura"

        ],

        modoUso: `
            Consumir 3g ao dia, preferencialmente após o treino ou conforme orientação profissional.
        `,

        tabela: [

            ["Porção", "3g"],

            ["Creatina", "3g"]

        ]

    }

};

const params = new URLSearchParams(window.location.search);

const id = params.get("id");

const produto = produtos[id];

if (!produto) {

    document.body.innerHTML = `

        <div style="text-align:center;padding:80px 20px;">

            <h1>Produto não encontrado</h1>

            <p>O produto solicitado não foi encontrado.</p>

            <a href="produtos.html" class="btn-principal">

                Voltar para Produtos

            </a>

        </div>

    `;

    throw new Error("Produto não encontrado");

}

document.title = `${produto.nome} | DYXON MAROMBA`;

document.getElementById("breadcrumbProduto").textContent = produto.nome;

const nomeProduto = document.getElementById("produtoNome");

nomeProduto.textContent = produto.variacoes?.length
    ? `${produto.nome} - ${produto.variacoes[0].nome}`
    : produto.nome;

document.getElementById("produtoCategoria").textContent = produto.categoria;

if (produto.variacoes?.length) {

    produto.preco = produto.variacoes[0].preco;

    produto.precoAntigo = Number((produto.preco / 0.90).toFixed(2));

}

document.getElementById("produtoPreco").textContent =
    produto.preco.toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL"
    });

    const precoProduto = document.getElementById("produtoPreco");

document.getElementById("produtoPrecoAntigo").textContent =
    produto.precoAntigo.toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL"
    });

const desconto = Math.round(
    ((produto.precoAntigo - produto.preco) /
    produto.precoAntigo) * 100
);

document.getElementById("descontoProduto").textContent =
    `-${desconto}%`;

const economia = produto.precoAntigo - produto.preco;

document.getElementById("economiaProduto").textContent =
    `Economize ${economia.toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL"
    })}`;

document.getElementById("produtoDescricao").textContent = produto.descricao;

document.getElementById("produtoImagem").src = produto.imagem;

document.getElementById("produtoImagem").alt = produto.nome;

document.getElementById("produtoMarca").textContent = produto.marca;

document.getElementById("produtoPeso").textContent = produto.peso;

document.getElementById("produtoConteudo").textContent = produto.conteudo;

document.getElementById("produtoValidade").textContent = produto.validade;

document.getElementById("descricaoCompleta").innerHTML = produto.descricaoCompleta;

document.getElementById("produtoModoUso").innerHTML = `<p>${produto.modoUso}</p>`;

const quantidade = document.getElementById("quantidade");

const subtotal = document.getElementById("subtotal");

const cep = document.getElementById("cep");

const frete = document.getElementById("frete");

const total = document.getElementById("total");

const btnWhatsapp = document.getElementById("btnWhatsapp");

const btnWhatsapp2 = document.getElementById("btnWhatsapp2");

const btnCarrinho = document.getElementById("btnCarrinho");

const avisoCep = document.getElementById("avisoCep");

const listaBeneficios = document.getElementById("produtoBeneficios");

const tabela = document.getElementById("tabelaNutricional");

const relacionados = document.getElementById("produtosRelacionados");

const selectVariacao = document.getElementById("variacao");

let valorFreteCalculado = 0;

const PROMOCOES = {

    freteGratis: true,

    quantidadeMinima: 3

};

const numeroWhatsapp = "5599999999999";

if (produto.variacoes && selectVariacao) {

    produto.variacoes.forEach(variacao => {

        const option = document.createElement("option");

        option.value = variacao.nome;

        option.textContent = `${variacao.nome} - ${variacao.preco.toLocaleString("pt-BR", {
            style: "currency",
            currency: "BRL"
        })}`;

        selectVariacao.appendChild(option);

    });

}

selectVariacao.addEventListener("change", () => {

    const variacao = produto.variacoes[selectVariacao.selectedIndex];

    produto.preco = variacao.preco;

    produto.precoAntigo = Number((produto.preco / 0.90).toFixed(2));

    nomeProduto.textContent = `${produto.nome} - ${variacao.nome}`;

    document.getElementById("produtoPreco").textContent =
        produto.preco.toLocaleString("pt-BR", {
            style: "currency",
            currency: "BRL"
        });

    document.getElementById("produtoPrecoAntigo").textContent =
        produto.precoAntigo.toLocaleString("pt-BR", {
            style: "currency",
            currency: "BRL"
        });

    const desconto = Math.round(
        ((produto.precoAntigo - produto.preco) / produto.precoAntigo) * 100
    );

    document.getElementById("descontoProduto").textContent = `-${desconto}%`;

    const economia = produto.precoAntigo - produto.preco;

    document.getElementById("economiaProduto").textContent =
        `Economize ${economia.toLocaleString("pt-BR", {
            style: "currency",
            currency: "BRL"
        })}`;

    atualizarValores();

});

produto.beneficios.forEach(beneficio => {

    const li = document.createElement("li");

    li.textContent = beneficio;

    listaBeneficios.appendChild(li);

});

tabela.innerHTML = `

    <thead>

        <tr>

            <th>Nutriente</th>

            <th>Quantidade</th>

        </tr>

    </thead>

    <tbody></tbody>

`;

const tbody = tabela.querySelector("tbody");

produto.tabela.forEach(item => {

    const tr = document.createElement("tr");

    tr.innerHTML = `

        <td>${item[0]}</td>

        <td>${item[1]}</td>

    `;

    tbody.appendChild(tr);

});

Object.entries(produtos).forEach(([key, item]) => {

    if (key === id) return;

    relacionados.innerHTML += `

        <div class="product-card">

            <img src="${item.imagem}" alt="${item.nome}">

            <div class="product-info">

                <span>${item.categoria}</span>

                <h3>${item.nome}</h3>

                <p>${item.descricao}</p>

                <div class="price">

                    ${item.preco.toLocaleString("pt-BR", {

                        style: "currency",

                        currency: "BRL"

                    })}

                </div>

                <a
                    href="produto.html?id=${key}"
                    class="btn btn-primary">

                    Ver Detalhes

                </a>

            </div>

        </div>

    `;

});

function atualizarValores() {

    const qtd = Math.max(1, Number(quantidade.value) || 1);

    quantidade.value = qtd;

    const subtotalCompra = produto.preco * qtd;

    let freteAtual = valorFreteCalculado;

    if (

        PROMOCOES.freteGratis &&

        qtd >= PROMOCOES.quantidadeMinima &&

        valorFreteCalculado > 0

    ) {

        freteAtual = 0;

        frete.textContent = "Grátis";

    }

    else {

        if (valorFreteCalculado > 0) {

            frete.textContent = valorFreteCalculado.toLocaleString(

                "pt-BR",

                {

                    style: "currency",

                    currency: "BRL"

                }

            );

        }

        else {

            frete.textContent = valorFreteCalculado.toLocaleString(
    "pt-BR",
    {
        style: "currency",
        currency: "BRL"
    }
);

        }

    }

    subtotal.textContent = subtotalCompra.toLocaleString(

        "pt-BR",

        {

            style: "currency",

            currency: "BRL"

        }

    );

    total.textContent = (subtotalCompra + freteAtual).toLocaleString(

        "pt-BR",

        {

            style: "currency",

            currency: "BRL"

        }

    );

    atualizarWhatsapp();

}

quantidade.addEventListener("input", atualizarValores);

function atualizarWhatsapp() {

    const qtd = Number(quantidade.value) || 1;

    const subtotalCompra = produto.preco * qtd;

    let freteAtual = valorFreteCalculado;

    if (

        PROMOCOES.freteGratis &&

        qtd >= PROMOCOES.quantidadeMinima &&

        valorFreteCalculado > 0

    ) {

        freteAtual = 0;

    }

    const totalCompra = subtotalCompra + freteAtual;

    const textoFrete =

        freteAtual === 0 && valorFreteCalculado > 0

            ? "Grátis"

            : freteAtual.toLocaleString(

                  "pt-BR",

                  {

                      style: "currency",

                      currency: "BRL"

                  }

              );

    const mensagem = encodeURIComponent(

`Olá!

Gostaria de comprar o seguinte produto:

📦 Produto: ${produto.nome}

🔢 Quantidade: ${qtd}

💰 Valor Unitário: ${produto.preco.toLocaleString("pt-BR", {

style: "currency",

currency: "BRL"

})}

🧾 Subtotal: ${subtotalCompra.toLocaleString("pt-BR", {

style: "currency",

currency: "BRL"

})}

🚚 Frete: ${textoFrete}

💳 Total: ${totalCompra.toLocaleString("pt-BR", {

style: "currency",

currency: "BRL"

})}

📍 CEP: ${cep.value || "Não informado"}

Aguardo atendimento.`

    );

    const link = `https://wa.me/${numeroWhatsapp}?text=${mensagem}`;

    btnWhatsapp.href = link;

    btnWhatsapp2.href = link;

}

atualizarWhatsapp();

cep.addEventListener("blur", async () => {

    const cepLimpo = cep.value.replace(/\D/g, "");

    if (cepLimpo === "") {

    valorFreteCalculado = 0;

    frete.textContent = "";

    if (valorFreteCalculado > 0) {

    avisoCep.style.display = "none";

}

atualizarValores();

    return;

}

    if (cepLimpo.length !== 8) {

        valorFreteCalculado = 0;

        frete.textContent = "CEP inválido";

        atualizarValores();

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

            atualizarValores();

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

        atualizarValores();

    }

        catch (erro) {

        console.error(erro);

        valorFreteCalculado = 0;

        frete.textContent = "Erro ao calcular";

        atualizarValores();

    }

});

document.getElementById("btnMais").addEventListener("click", () => {

    quantidade.value = Number(quantidade.value || 1) + 1;

    atualizarValores();

});

document.getElementById("btnMenos").addEventListener("click", () => {

    const valor = Math.max(1, Number(quantidade.value || 1) - 1);

    quantidade.value = valor;

    atualizarValores();

});

btnWhatsapp.addEventListener("click", (e) => {

    if (valorFreteCalculado <= 0) {

        e.preventDefault();

        avisoCep.textContent =
            "Informe um CEP válido para calcular o frete.";

        avisoCep.style.display = "block";

        cep.focus();

        return;
    }

    avisoCep.style.display = "none";

});

btnWhatsapp2.addEventListener("click", (e) => {

    if (valorFreteCalculado <= 0) {

        e.preventDefault();

        avisoCep.textContent =
            "Informe um CEP válido para calcular o frete.";

        avisoCep.style.display = "block";

        cep.focus();

        return;
    }

    avisoCep.style.display = "none";

});

quantidade.addEventListener("change", () => {

    if (Number(quantidade.value) < 1 || isNaN(Number(quantidade.value))) {

        quantidade.value = 1;

    }

    atualizarValores();

});

cep.addEventListener("input", () => {

    cep.value = cep.value
        .replace(/\D/g, "")
        .replace(/^(\d{5})(\d)/, "$1-$2")
        .substring(0, 9);

});

atualizarValores();

function adicionarAoCarrinho() {

    const carrinho = JSON.parse(localStorage.getItem("carrinho")) || [];

    const item = {

        id: id,

        nome: produto.nome,

        variacao: selectVariacao.value,

        quantidade: Number(quantidade.value),

        preco: produto.preco,

        imagem: produto.imagem

    };

    const existente = carrinho.find(produtoCarrinho =>
    produtoCarrinho.id === item.id &&
    produtoCarrinho.variacao === item.variacao
);

if (existente) {

    existente.quantidade += item.quantidade;

} else {

    carrinho.push(item);

}

    localStorage.setItem("carrinho", JSON.stringify(carrinho));

}

cep.addEventListener("input", () => {

    if (valorFreteCalculado > 0) {

        avisoCep.style.display = "none";

    }

});

btnCarrinho.addEventListener("click", () => {

    adicionarAoCarrinho();

});

