async function carregarCarrinho() {
    const carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
    if (carrinho.length === 0) {
        document.getElementById('carrinho-lista').innerHTML = "<p>Seu carrinho está vazio.</p>";
        document.getElementById('carrinho-resumo').innerHTML = "";
        return;
    }

    // Busca os dados dos produtos do backend
    const response = await fetch('../buscar-produtos.php');
    const produtos = await response.json();

    let total = 0;
    let html = '';
    carrinho.forEach(item => {
        const produto = produtos.find(p => p.id == item.id);
        if (produto) {
            const subtotal = produto.preco * item.quantidade;
            total += subtotal;
            html += `
                <div class="carrinho-item">
                    <div>
                        <strong>${produto.nome}</strong>
                        <div style="display:flex;align-items:center;margin:8px 0;">
                            <button onclick="alterarQtd(${produto.id}, -1)">-</button>
                            <span style="margin:0 10px;">${item.quantidade}</span>
                            <button onclick="alterarQtd(${produto.id}, 1)">+</button>
                        </div>
                        <span>R$ ${subtotal.toFixed(2)}</span>
                    </div>
                    <img src="../${produto.imagem}" alt="${produto.nome}" style="width:200px;height:60px;object-fit:cover;border-radius:8px;">
                    <button onclick="removerItem(${produto.id})" title="Remover" style="background:none;border:none;color:#e94f4f;font-size:22px;cursor:pointer;">
                        <img src="../images/icones/lixeira.png" alt="Remover" style="width:35px;vertical-align:middle;margin-left:6px;">
                    </button>
                </div>
            `;
        }
    });

    document.getElementById('carrinho-lista').innerHTML = html;

    // Resumo do carrinho
    document.getElementById('carrinho-resumo').innerHTML = `
        <div class="carrinho-resumo-box">
            <div>Frete: <span>R$ 0,00</span></div>
            <div>Desconto: <span>R$ 0,00</span></div>
            <div>Total: <span>R$ ${total.toFixed(2)}</span></div>
        </div>
        <div style="display:flex;gap:10px;margin-top:12px;">
            <button onclick="esvaziarCarrinho()" style="background:#e94f4f;color:#fff;padding:10px 18px;border:none;border-radius:8px;cursor:pointer;">Esvaziar Carrinho</button>
            <a href="http://localhost/projeto-cupcake/dreamcakes/carrinho/carrinho_2.php">
                <button style="background:#b6e2b6;color:#333;padding:10px 18px;border:none;border-radius:8px;cursor:pointer;">Continuar</button>
            </a>
        </div>
    `;
}

// Altera quantidade
function alterarQtd(produtoId, delta) {
    let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
    const idx = carrinho.findIndex(item => item.id == produtoId);
    if (idx > -1) {
        carrinho[idx].quantidade += delta;
        if (carrinho[idx].quantidade < 1) carrinho[idx].quantidade = 1;
        localStorage.setItem('carrinho', JSON.stringify(carrinho));
        carregarCarrinho();
    }
}

// Remove item
function removerItem(produtoId) {
    let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
    carrinho = carrinho.filter(item => item.id != produtoId);
    localStorage.setItem('carrinho', JSON.stringify(carrinho));
    carregarCarrinho();
}

// Esvazia carrinho
function esvaziarCarrinho() {
    localStorage.removeItem('carrinho');
    carregarCarrinho();
}

window.onload = carregarCarrinho;