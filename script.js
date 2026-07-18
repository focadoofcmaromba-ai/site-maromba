function openTab(tabIndex) {
  document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
  document.getElementById('tab-' + tabIndex).classList.add('active');
  document.querySelectorAll('.menu-item').forEach((el, i) => {
    el.classList.toggle('active', i === tabIndex);
  });
}

// ==================== OBJETOS ====================
let menuItems = JSON.parse(localStorage.getItem('dyxonMenuItems')) || [
  {name: 'Início', link: 'index.html', icon: '🏠', font: 'Inter', color: '#ffffff'},
  {name: 'Produtos', link: 'produtos.html', icon: '💪', font: 'Inter', color: '#ffffff'},
  {name: 'Antes e Depois', link: 'antes-depois.html', icon: '📸', font: 'Inter', color: '#ffffff'},
  {name: 'YouTube', link: 'youtube.html', icon: '▶️', font: 'Inter', color: '#ffffff'},
  {name: 'Rastreio', link: 'rastreio.html', icon: '📦', font: 'Inter', color: '#ffffff'},
  {name: 'Suporte', link: 'suporte.html', icon: '🎧', font: 'Inter', color: '#ffffff'}
];

let logoData = JSON.parse(localStorage.getItem('dyxonLogo')) || {
  text: 'DYXON', image: null, font: 'Inter', textColor: '#ffffff'
};

let footerData = JSON.parse(localStorage.getItem('dyxonFooter')) || {
  titulo: "DYXON MAROMBA",
  descricao: "Seu portal de musculação, nutrição e suplementação.",
  redes: [],
  copyright: "© 2026 DYXON MAROMBA. Todos os direitos reservados."
};
if (!Array.isArray(footerData.redes)) footerData.redes = [];

let beforeAfterData = JSON.parse(localStorage.getItem('dyxonBeforeAfter')) || [];
let canais = JSON.parse(localStorage.getItem("dyxonCanaisRSS")) || [];

// ==================== SALVAR TUDO ====================
function salvarTudo() {
  localStorage.setItem('dyxonMenuItems', JSON.stringify(menuItems));
  localStorage.setItem('dyxonLogo', JSON.stringify(logoData));
  localStorage.setItem('dyxonFooter', JSON.stringify(footerData));
  localStorage.setItem('dyxonBeforeAfter', JSON.stringify(beforeAfterData));
  localStorage.setItem('dyxonCanaisRSS', JSON.stringify(canais));

  renderMenuList();
  renderMenuPreview();
  renderLogoPreview();
  renderFooterPreview();
  renderListaTransformacoes();
  renderPreviewAntesDepois();
  renderCanais();
  renderPreview();
}

// ==================== MENU EDITOR ====================
function renderMenuList() {

    const container = document.getElementById("menuList");

    if (!container) return;

    container.innerHTML = "";

    menuItems.forEach((item, index) => {

        const card = document.createElement("div");

        card.className =
            "bg-zinc-900 border border-zinc-800 hover:border-emerald-500 rounded-xl p-4 mb-3 transition-all duration-200 cursor-move";

        card.draggable = true;
        card.dataset.index = index;

        card.innerHTML = `

            <div class="flex items-center justify-between">

                <div class="flex items-center gap-4">

                    <div class="text-3xl">
                        ${item.icon}
                    </div>

                    <div>

                        <div
                            class="font-semibold text-white"
                            style="
                                font-family:${item.font};
                                color:${item.color};
                            "
                        >
                            ${item.name}
                        </div>

                        <div class="text-xs text-zinc-400 mt-1">
                            ${item.link}
                        </div>

                    </div>

                </div>

                <div class="flex gap-2">

                    <button
                        class="px-3 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-600 transition"
                        onclick="editarItem(${index})"
                        title="Editar"
                    >
                        ✏️
                    </button>

                    <button
                        class="px-3 py-2 rounded-lg bg-red-700 hover:bg-red-600 transition"
                        onclick="removerItem(${index})"
                        title="Remover"
                    >
                        🗑️
                    </button>

                </div>

            </div>

        `;

        card.addEventListener("dragstart", (e) => {

    card.classList.add(
        "opacity-40",
        "scale-95",
        "ring-2",
        "ring-emerald-500"
    );

    e.dataTransfer.effectAllowed = "move";
    e.dataTransfer.setData("text/plain", card.dataset.index);

});

card.addEventListener("dragend", () => {

    card.classList.remove(
        "opacity-40",
        "scale-95",
        "ring-2",
        "ring-emerald-500"
    );

    document.querySelectorAll("#menuList > div").forEach(el => {

        el.classList.remove(
            "border-emerald-500",
            "bg-zinc-800",
            "scale-[1.02]"
        );

    });

});

card.addEventListener("dragover", (e) => {

    e.preventDefault();

    card.classList.add(
        "border-emerald-500",
        "bg-zinc-800",
        "scale-[1.02]"
    );

});

card.addEventListener("dragleave", () => {

    card.classList.remove(
        "border-emerald-500",
        "bg-zinc-800",
        "scale-[1.02]"
    );

});

card.addEventListener("drop", (e) => {

    e.preventDefault();

    document.querySelectorAll("#menuList > div").forEach(el => {

        el.classList.remove(
            "border-emerald-500",
            "bg-zinc-800",
            "scale-[1.02]"
        );

    });

    const origem = parseInt(
        e.dataTransfer.getData("text/plain")
    );

    const destino = parseInt(
        card.dataset.index
    );

    if (
        isNaN(origem) ||
        isNaN(destino) ||
        origem === destino
    ) return;

    const itemMovido = menuItems.splice(origem, 1)[0];

    menuItems.splice(destino, 0, itemMovido);

    salvarTudo();

});

        container.appendChild(card);

    });

}
function editarItem(index) {

    const item = menuItems[index];

    const modal = document.createElement("div");

    modal.className =
        "fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-[9999] p-5";

    modal.innerHTML = `

        <div class="bg-zinc-900 border border-zinc-700 rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden">

            <div class="flex items-center justify-between px-6 py-5 border-b border-zinc-800">

                <div>

                    <h2 class="text-2xl font-bold text-white">
                        Editar Item do Menu
                    </h2>

                    <p class="text-zinc-400 text-sm mt-1">
                        Personalize este item do menu.
                    </p>

                </div>

                <button
                    id="btnFecharModal"
                    class="text-2xl text-zinc-400 hover:text-white transition">
                    ✕
                </button>

            </div>

            <div class="p-6 space-y-5">

                <div>

                    <label class="block text-sm text-zinc-400 mb-2">
                        Nome
                    </label>

                    <input
                        id="mNome"
                        type="text"
                        value="${item.name}"
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-3 text-white">

                </div>

                <div>

                    <label class="block text-sm text-zinc-400 mb-2">
                        Link
                    </label>

                    <input
                        id="mLink"
                        type="text"
                        value="${item.link}"
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-3 text-white">

                </div>

                <div class="grid grid-cols-2 gap-4">

                    <div>

                        <label class="block text-sm text-zinc-400 mb-2">
                            Ícone
                        </label>

                        <input
                            id="mIcone"
                            type="text"
                            value="${item.icon}"
                            class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-3 text-white">

                    </div>

                    <div>

                        <label class="block text-sm text-zinc-400 mb-2">
                            Cor
                        </label>

                        <input
                            id="mCor"
                            type="color"
                            value="${item.color}"
                            class="w-full h-12 rounded-lg bg-zinc-800 border border-zinc-700">

                    </div>

                </div>

                <div>

                    <label class="block text-sm text-zinc-400 mb-2">
                        Fonte
                    </label>

                    <select
                        id="mFonte"
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-3 text-white">

                        <option value="Inter">Inter</option>
                        <option value="Poppins">Poppins</option>
                        <option value="Roboto">Roboto</option>
                        <option value="Montserrat">Montserrat</option>
                        <option value="Nunito">Nunito</option>
                        <option value="Oswald">Oswald</option>

                    </select>

                </div>

                <div class="bg-zinc-950 border border-zinc-800 rounded-xl p-5">

                    <div class="text-xs uppercase tracking-wider text-zinc-500 mb-3">
                        Pré-visualização
                    </div>

                    <div class="flex items-center gap-3">

                        <span
                            id="previewIcone"
                            class="text-2xl">
                            ${item.icon}
                        </span>

                        <span
                            id="previewTexto"
                            style="font-family:${item.font};color:${item.color};"
                            class="font-semibold text-lg">

                            ${item.name}

                        </span>

                    </div>

                </div>

            </div>

            <div class="border-t border-zinc-800 p-5 flex justify-end gap-3">

                <button
                    id="btnCancelarModal"
                    class="px-6 py-3 rounded-lg bg-zinc-700 hover:bg-zinc-600 transition">

                    Cancelar

                </button>

                <button
                    onclick="salvarEdicaoMenu(${index}, this)"
                    class="px-6 py-3 rounded-lg bg-emerald-600 hover:bg-emerald-500 transition">

                    Salvar Alterações

                </button>

            </div>

        </div>

    `;

    document.body.appendChild(modal);

    modal.querySelector("#mFonte").value = item.font;

    const nome = modal.querySelector("#mNome");
    const icone = modal.querySelector("#mIcone");
    const fonte = modal.querySelector("#mFonte");
    const cor = modal.querySelector("#mCor");

    function atualizarPreview() {

        modal.querySelector("#previewTexto").textContent = nome.value;
        modal.querySelector("#previewTexto").style.fontFamily = fonte.value;
        modal.querySelector("#previewTexto").style.color = cor.value;

        modal.querySelector("#previewIcone").textContent = icone.value;

    }

    nome.addEventListener("input", atualizarPreview);
    icone.addEventListener("input", atualizarPreview);
    fonte.addEventListener("change", atualizarPreview);
    cor.addEventListener("input", atualizarPreview);

    modal.querySelector("#btnCancelarModal")
        .addEventListener("click", () => modal.remove());

    modal.querySelector("#btnFecharModal")
        .addEventListener("click", () => modal.remove());

}

function salvarEdicaoMenu(index, btn) {

    const modal = btn.closest(".fixed");

    if (!modal) return;

    const nome = modal.querySelector("#mNome").value.trim();
    const link = modal.querySelector("#mLink").value.trim();
    const icone = modal.querySelector("#mIcone").value.trim();
    const cor = modal.querySelector("#mCor").value;
    const fonte = modal.querySelector("#mFonte").value;

    if (!nome) {
        alert("Informe o nome do item.");
        return;
    }

    if (!link) {
        alert("Informe o link do item.");
        return;
    }

    menuItems[index] = {
        ...menuItems[index],
        name: nome,
        link: link,
        icon: icone || "📄",
        color: cor,
        font: fonte
    };

    modal.remove();

    salvarTudo();

}

function adicionarItem() {

    const novoItem = {

        id: Date.now(),

        name: "Novo Item",

        link: "#",

        icon: "📄",

        font: "Inter",

        color: "#ffffff"

    };

    menuItems.push(novoItem);

    salvarTudo();

    const novoIndice = menuItems.length - 1;

    setTimeout(() => {

        editarItem(novoIndice);

    }, 100);

}

function removerItem(index) {

    const item = menuItems[index];

    if (!item) return;

    const modal = document.createElement("div");

    modal.className =
        "fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-[9999] p-5";

    modal.innerHTML = `

        <div class="bg-zinc-900 border border-zinc-700 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

            <div class="px-6 py-5 border-b border-zinc-800">

                <h2 class="text-xl font-bold text-white">

                    Remover Item

                </h2>

                <p class="text-zinc-400 mt-2">

                    Tem certeza que deseja remover este item do menu?

                </p>

            </div>

            <div class="p-6 rounded-lg bg-zinc-950 mx-6 mt-6">

                <div class="flex items-center gap-3">

                    <span class="text-2xl">

                        ${item.icon}

                    </span>

                    <div>

                        <div class="font-semibold text-white">

                            ${item.name}

                        </div>

                        <div class="text-sm text-zinc-500">

                            ${item.link}

                        </div>

                    </div>

                </div>

            </div>

            <div class="flex justify-end gap-3 p-6 border-t border-zinc-800 mt-6">

                <button
                    id="cancelarRemocao"
                    class="px-5 py-3 rounded-lg bg-zinc-700 hover:bg-zinc-600 transition">

                    Cancelar

                </button>

                <button
                    id="confirmarRemocao"
                    class="px-5 py-3 rounded-lg bg-red-600 hover:bg-red-500 transition">

                    Remover

                </button>

            </div>

        </div>

    `;

    document.body.appendChild(modal);

    modal.querySelector("#cancelarRemocao")
        .addEventListener("click", () => {

            modal.remove();

        });

    modal.querySelector("#confirmarRemocao")
        .addEventListener("click", () => {

            menuItems.splice(index, 1);

            modal.remove();

            salvarTudo();

        });

}

function renderMenuPreview() {

    const container = document.getElementById("menuPreview");

    if (!container) return;

    container.innerHTML = "";

    const wrapper = document.createElement("div");

    wrapper.className =
        "bg-zinc-950 border border-zinc-800 rounded-2xl overflow-hidden shadow-xl";

    /* HEADER */

    const header = document.createElement("div");

    header.className =
        "flex items-center justify-between px-5 py-4 border-b border-zinc-800";

    header.innerHTML = `

        <div class="flex items-center gap-3">

            ${
                logoData.image
                    ? `<img src="${logoData.image}" class="h-10 object-contain">`
                    : `<div style="
                            font-family:${logoData.font};
                            color:${logoData.textColor};
                        "
                        class="text-2xl font-bold">
                        ${logoData.text}
                    </div>`
            }

        </div>

        <div class="text-xs uppercase tracking-widest text-zinc-500">

            MENU

        </div>

    `;

    wrapper.appendChild(header);

    /* MENU */

    const nav = document.createElement("nav");

    nav.className = "p-4 flex flex-col gap-2";

    menuItems.forEach((item, index) => {

        const active = index === 0;

        const link = document.createElement("div");

        link.className = `

            flex
            items-center
            justify-between
            rounded-xl
            px-4
            py-3
            transition-all
            duration-200
            border

            ${
                active
                    ? "bg-emerald-600 border-emerald-500"
                    : "bg-zinc-900 border-zinc-800 hover:bg-zinc-800 hover:border-emerald-500"
            }

        `;

        link.innerHTML = `

            <div class="flex items-center gap-4">

                <div class="text-2xl">

                    ${item.icon || "📄"}

                </div>

                <div>

                    <div
                        class="font-semibold"
                        style="
                            color:${active ? "#ffffff" : item.color};
                            font-family:${item.font};
                        ">

                        ${item.name}

                    </div>

                    <div class="text-xs text-zinc-400 mt-1">

                        ${item.link}

                    </div>

                </div>

            </div>

            <div class="text-lg opacity-60">

                →

            </div>

        `;

        nav.appendChild(link);

    });

    wrapper.appendChild(nav);

    /* FOOTER */

    const footer = document.createElement("div");

    footer.className =
        "px-5 py-4 border-t border-zinc-800 text-xs text-zinc-500";

    footer.innerHTML = `

        ${menuItems.length} item(ns) no menu

    `;

    wrapper.appendChild(footer);

    container.appendChild(wrapper);

}

// ==================== LOGO ====================
function uploadLogoImage(e) {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = function(ev) {
    logoData.image = ev.target.result;
    salvarTudo();
  };
  reader.readAsDataURL(file);
}

function renderLogoPreview() {
  const container = document.getElementById('logoPreview');
  if (!container) return;
  container.innerHTML = '';
  if (logoData.image) {
    const img = document.createElement('img');
    img.src = logoData.image;
    img.className = 'max-h-16';
    container.appendChild(img);
  } else {
    const span = document.createElement('span');
    span.style.fontFamily = logoData.font;
    span.style.color = logoData.textColor;
    span.textContent = logoData.text;
    container.appendChild(span);
  }
}

function removerLogoImagem() {
  logoData.image = null;
  salvarTudo();
}

function editarLogoPopup() {
  const modal = document.createElement('div');
  modal.className = 'fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50';
  modal.innerHTML = `
    <div class="bg-zinc-900 p-8 rounded-xl w-full max-w-md">
      <h2 class="text-2xl font-bold mb-6">Editar Logo</h2>
      <input id="lTexto" value="${logoData.text}" class="w-full p-3 rounded bg-zinc-800 mb-3">
      <input id="lCor" type="color" value="${logoData.textColor}" class="w-full mb-3">
      <select id="lFonte" class="w-full p-3 rounded bg-zinc-800 mb-6">
        <option>Inter</option><option>Poppins</option>
      </select>
      <div class="flex gap-4">
        <button onclick="salvarLogo(this)" class="bg-emerald-600 px-6 py-3 rounded flex-1">Salvar</button>
        <button onclick="this.closest('.fixed').remove()" class="bg-zinc-700 px-6 py-3 rounded flex-1">Cancelar</button>
      </div>
    </div>
  `;
  document.body.appendChild(modal);
}

function salvarLogo(btn) {
  const modal = btn.closest('.fixed');
  logoData.text = modal.querySelector('#lTexto').value;
  logoData.textColor = modal.querySelector('#lCor').value;
  logoData.font = modal.querySelector('#lFonte').value;
  modal.remove();
  salvarTudo();
}

// ==================== LOGO ====================

function uploadLogoImage(e) {

    const arquivo = e.target.files[0];

    if (!arquivo) return;

    if (!arquivo.type.startsWith("image/")) {

        alert("Selecione uma imagem válida.");

        e.target.value = "";

        return;

    }

    const reader = new FileReader();

    reader.onload = function(evento) {

        logoData.image = evento.target.result;

        salvarTudo();

    };

    reader.readAsDataURL(arquivo);

    e.target.value = "";

}

// ==================== RODAPÉ ====================
function atualizarFooter() {
  footerData.titulo = document.getElementById('footerTitulo').value;
  footerData.descricao = document.getElementById('footerDescricao').value;
  footerData.copyright = document.getElementById('footerCopyright').value;
  salvarTudo();
}

function renderListaRedes() {
  const container = document.getElementById('listaRedes');
  if (!container) return;
  container.innerHTML = '';
  footerData.redes.forEach((rede, i) => {
    const div = document.createElement('div');
    div.className = 'flex justify-between items-center bg-zinc-800 p-2 rounded mb-2';
    div.innerHTML = `<span>${rede.nome}</span><button onclick="removerRedeSocial(${i})" class="text-red-400">🗑</button>`;
    container.appendChild(div);
  });
}

function adicionarRedeSocial() {
  const nome = prompt("Nome da rede:");
  const link = prompt("Link:");
  if (!nome || !link) return;
  footerData.redes.push({nome, link});
  salvarTudo();
}

function removerRedeSocial(i) {
  if (confirm('Remover rede?')) {
    footerData.redes.splice(i, 1);
    salvarTudo();
  }
}

function renderFooterPreview() {
  const container = document.getElementById('footerPreview');
  if (!container) return;
  container.innerHTML = `
    <div class="text-white font-bold">${footerData.titulo}</div>
    <div class="text-emerald-400">${footerData.descricao}</div>
    <div class="text-xs mt-2">${footerData.copyright}</div>
  `;
}

// ==================== ANTES E DEPOIS ====================
function adicionarTransformacao() {
  beforeAfterData.push({
    id: Date.now(),
    nome: "Nova Transformação",
    tempo: "",
    produtos: "",
    resultado: "",
    descricao: "",
    before: "",
    after: ""
  });
  salvarTudo();
}

function removerTransformacao(id) {
  if (!confirm("Remover esta transformação?")) return;
  beforeAfterData = beforeAfterData.filter(item => item.id !== id);
  salvarTudo();
}

function renderListaTransformacoes() {
  const lista = document.getElementById("listaTransformacoes");
  if (!lista) return;
  lista.innerHTML = "";
  if (beforeAfterData.length === 0) {
    lista.innerHTML = `<div class="text-center text-zinc-500 italic p-8">Nenhuma transformação cadastrada.</div>`;
    return;
  }
  beforeAfterData.forEach(item => {
    const div = document.createElement('div');
    div.className = 'bg-zinc-900 border border-emerald-900 rounded-xl p-4 mb-4';
    div.innerHTML = `
      <div class="flex justify-between items-start">
        <div>
          <div class="text-white font-bold text-lg">👤 ${item.nome}</div>
          <div class="text-xs text-emerald-400 mt-2">
            ${item.before ? "📷 Antes ✔" : "📷 Antes ✖"} &nbsp;&nbsp; ${item.after ? "📷 Depois ✔" : "📷 Depois ✖"}
          </div>
        </div>
        <div class="flex gap-2">
          <button onclick="editarTransformacao(${item.id})" class="px-3 py-2 rounded bg-emerald-700">✏️</button>
          <button onclick="removerTransformacao(${item.id})" class="px-3 py-2 rounded bg-red-700">🗑️</button>
        </div>
      </div>
    `;
    lista.appendChild(div);
  });
}

function editarTransformacao(id) {
  const item = beforeAfterData.find(i => i.id === id);
  if (!item) return;
  const modal = document.createElement('div');
  modal.className = 'fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50';
  modal.innerHTML = `
    <div class="bg-zinc-900 p-8 rounded-xl w-full max-w-2xl">
      <h2 class="text-2xl font-bold mb-6">Editar Transformação</h2>
      <input id="editNome" value="${item.nome}" class="w-full p-3 rounded bg-zinc-800 mb-3">
      <input id="editTempo" value="${item.tempo}" class="w-full p-3 rounded bg-zinc-800 mb-3">
      <input id="editProdutos" value="${item.produtos}" class="w-full p-3 rounded bg-zinc-800 mb-3">
      <input id="editResultado" value="${item.resultado}" class="w-full p-3 rounded bg-zinc-800 mb-3">
      <textarea id="editDescricao" class="w-full p-3 rounded bg-zinc-800 mb-3">${item.descricao}</textarea>
      <div class="flex gap-4 mt-4">
        <div>
          <button onclick="uploadBefore(${id}, this)" class="bg-emerald-700 px-4 py-2 rounded">Upload Antes</button>
          <button onclick="removerBefore(${id}, this)" class="bg-red-700 px-4 py-2 rounded ml-2">Remover</button>
          <img id="previewBefore" src="${item.before}" class="mt-2 max-h-32">
        </div>
        <div>
          <button onclick="uploadAfter(${id}, this)" class="bg-emerald-700 px-4 py-2 rounded">Upload Depois</button>
          <button onclick="removerAfter(${id}, this)" class="bg-red-700 px-4 py-2 rounded ml-2">Remover</button>
          <img id="previewAfter" src="${item.after}" class="mt-2 max-h-32">
        </div>
      </div>
      <div class="flex gap-4 mt-6">
        <button onclick="salvarEdicaoTransformacao(${id}, this)" class="bg-emerald-600 px-6 py-3 rounded flex-1">Salvar</button>
        <button onclick="this.closest('.fixed').remove()" class="bg-zinc-700 px-6 py-3 rounded flex-1">Cancelar</button>
      </div>
    </div>
  `;
  document.body.appendChild(modal);
}

function uploadBefore(id, btn) {
  const input = document.createElement('input');
  input.type = 'file';
  input.onchange = e => {
    const reader = new FileReader();
    reader.onload = ev => {
      const item = beforeAfterData.find(i => i.id === id);
      item.before = ev.target.result;
      btn.nextElementSibling.src = item.before;
    };
    reader.readAsDataURL(e.target.files[0]);
  };
  input.click();
}

function uploadAfter(id, btn) {
  const input = document.createElement('input');
  input.type = 'file';
  input.onchange = e => {
    const reader = new FileReader();
    reader.onload = ev => {
      const item = beforeAfterData.find(i => i.id === id);
      item.after = ev.target.result;
      btn.nextElementSibling.src = item.after;
    };
    reader.readAsDataURL(e.target.files[0]);
  };
  input.click();
}

function removerBefore(id, btn) {
  const item = beforeAfterData.find(i => i.id === id);
  item.before = "";
  btn.nextElementSibling.src = "";
}

function removerAfter(id, btn) {
  const item = beforeAfterData.find(i => i.id === id);
  item.after = "";
  btn.nextElementSibling.src = "";
}

function salvarEdicaoTransformacao(id, btn) {
  const item = beforeAfterData.find(i => i.id === id);
  const modal = btn.closest('.fixed');
  item.nome = modal.querySelector('#editNome').value;
  item.tempo = modal.querySelector('#editTempo').value;
  item.produtos = modal.querySelector('#editProdutos').value;
  item.resultado = modal.querySelector('#editResultado').value;
  item.descricao = modal.querySelector('#editDescricao').value;
  modal.remove();
  salvarTudo();
}

function renderPreviewAntesDepois() {
  const container = document.getElementById('previewAntesDepois');
  if (!container) return;
  container.innerHTML = '';
  beforeAfterData.forEach(item => {
    const card = document.createElement('div');
    card.className = 'bg-zinc-900 border border-emerald-900 rounded-2xl p-6 mb-8';
    card.innerHTML = `
      <div class="grid grid-cols-2 gap-6">
        <div><img src="${item.before}" class="w-full rounded-xl"></div>
        <div><img src="${item.after}" class="w-full rounded-xl"></div>
      </div>
      <div class="mt-6">
        <div class="text-2xl font-bold">${item.nome}</div>
        <div class