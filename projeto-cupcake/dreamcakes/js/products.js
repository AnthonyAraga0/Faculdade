let produtosCache = [];

async function carregarProdutos() {
    const response = await fetch("../buscar-produtos.php");
    const produtos = await response.json();
    produtosCache = produtos; // Salva todos os produtos para filtro

    renderizarProdutos(produtosCache);

    const container = document.getElementById("products-list");
    container.innerHTML = "";

    for (let i = 0; i < produtos.length; i += 4) {
        const row = document.createElement("div");
        row.classList.add("products-row");

        for (let j = i; j < i + 4 && j < produtos.length; j++) {
            const produto = produtos[j];
            const card = document.createElement("div");
            card.classList.add("product-item");

            card.innerHTML = `
                <img src="../${produto.imagem}" alt="${produto.nome}">
                <h3>${produto.nome}</h3>
                <span class="price">R$ ${produto.preco}</span>
                <button class="comprar-btn" data-index="${j}">Comprar</button>
            `;

            row.appendChild(card);
        }

        container.appendChild(row);
    }

    // Adiciona evento aos botões "Comprar"
    document.querySelectorAll('.comprar-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            const idx = parseInt(this.getAttribute('data-index'));
            mostrarPopupProduto(produtos[idx]);
        });
    });
}

// Função para mostrar o popup do produto
function mostrarPopupProduto(produto) {
    let popup = document.getElementById('produtoPopup');
    if (!popup) {
        popup = document.createElement('div');
        popup.id = 'produtoPopup';
        popup.className = 'popup active';
        popup.innerHTML = `
            <div class="popup-content" id="produtoPopupContent"></div>
        `;
        document.body.appendChild(popup);
    } else {
        popup.classList.add('active');
    }

    const content = document.getElementById('produtoPopupContent');
    content.innerHTML = `
        <span class="close" onclick="closeProdutoPopup()">&times;</span>
        <img src="../${produto.imagem}" alt="${produto.nome}" style="width:350px;max-width:90%;border-radius:12px;margin-bottom:18px;">
        <h3>${produto.nome}</h3>
        <p class="price">R$ ${produto.preco}</p>
        <p>${produto.descricao ? produto.descricao : "Sem descrição detalhada."}</p>
        <div style="display:flex;align-items:center;justify-content:center;margin:18px 0;">
            <button id="menosQtd" style="background:#e94f4f;color:#fff;padding:6px 12px;border:none;border-radius:8px;cursor:pointer;font-size:18px;">-</button>
            <span id="qtdProduto" style="margin:0 16px;font-size:18px;">1</span>
            <button id="maisQtd" style="background:#e94f4f;color:#fff;padding:6px 12px;border:none;border-radius:8px;cursor:pointer;font-size:18px;">+</button>
        </div>
        <button onclick="adicionarAoCarrinho(${produto.id}, document.getElementById('qtdProduto').textContent)" style="background:#e94f4f;color:#fff;padding:10px 18px;border:none;border-radius:8px;margin-bottom:10px;cursor:pointer;">Adicionar ao Carrinho</button>
        <button onclick="closeProdutoPopup()">Fechar</button>
    `;

    // Controle de quantidade
    let qtd = 1;
    document.getElementById('menosQtd').onclick = function () {
        if (qtd > 1) {
            qtd--;
            document.getElementById('qtdProduto').textContent = qtd;
        }
    };
    document.getElementById('maisQtd').onclick = function () {
        qtd++;
        document.getElementById('qtdProduto').textContent = qtd;
    };
}


// Função para fechar o popup do produto
function closeProdutoPopup() {
    const popup = document.getElementById('produtoPopup');
    if (popup) popup.classList.remove('active');
}

window.onload = function () {
    carregarProdutos();

    setTimeout(() => {
        document.getElementById("welcomePopup").classList.add("active");
    }, 1000);
}

function adicionarAoCarrinho(produtoId, quantidade) {
    closeProdutoPopup();
    let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
    // Verifica se já existe o produto no carrinho
    const idx = carrinho.findIndex(item => item.id == produtoId);
    if (idx > -1) {
        carrinho[idx].quantidade += parseInt(quantidade);
    } else {
        carrinho.push({ id: produtoId, quantidade: parseInt(quantidade) });
    }
    localStorage.setItem('carrinho', JSON.stringify(carrinho));
    alert("Produto adicionado ao carrinho!");
}

function renderizarProdutos(produtos) {
    const container = document.getElementById("products-list");
    container.innerHTML = "";

    for (let i = 0; i < produtos.length; i += 4) {
        const row = document.createElement("div");
        row.classList.add("products-row");

        for (let j = i; j < i + 4 && j < produtos.length; j++) {
            const produto = produtos[j];
            const card = document.createElement("div");
            card.classList.add("product-item");

            card.innerHTML = `
                <img src="../${produto.imagem}" alt="${produto.nome}">
                <h3>${produto.nome}</h3>
                <span class="price">R$ ${produto.preco}</span>
                <button class="comprar-btn" data-index="${j}">Comprar</button>
            `;

            row.appendChild(card);
        }
        container.appendChild(row);
    }

    // Adiciona evento aos botões "Comprar"
    document.querySelectorAll('.comprar-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const idx = parseInt(this.getAttribute('data-index'));
            mostrarPopupProduto(produtos[idx]);
        });
    });
}

// Filtra produtos ao clicar na lupa
document.addEventListener('DOMContentLoaded', function () {
    carregarProdutos();

    const searchBtn = document.querySelector('.search-btn-inside');
    const searchInput = document.querySelector('.search-bar input');

    searchBtn.addEventListener('click', function () {
        const termo = searchInput.value.trim().toLowerCase();
        if (!termo) {
            renderizarProdutos(produtosCache);
            return;
        }
        const filtrados = produtosCache.filter(produto =>
            produto.nome.toLowerCase().includes(termo) ||
            (produto.descricao && produto.descricao.toLowerCase().includes(termo))
        );
        renderizarProdutos(filtrados);
    });

    // Opcional: filtrar ao pressionar Enter
    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            searchBtn.click();
        }
    });
});
