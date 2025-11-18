document.getElementById('finalizar-compra-btn').addEventListener('click', function () {
    // Coleta dados do formulário
    const cartaoSelecionado = document.querySelector('input[name="cartao_selecionado"]:checked');
    const endereco = document.getElementById('endereco').value;
    const cep = document.getElementById('cep').value;
    const valor_frete = document.getElementById('valor-frete').textContent.replace('R$', '').replace(',', '.').trim();
    const valor_desconto = document.getElementById('valor-desconto').textContent.replace('R$', '').replace(',', '.').trim();
    const valor_total = document.getElementById('valor-total').textContent.replace('R$', '').replace(',', '.').trim();
    const carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];

    if (!endereco) {
        alert('Informe um Endereço para finalizar a compra.');
        return;
    }

    if (!cep) {
        alert('Informe um CEP para finalizar a compra.');
        return;
    }

    if (!cartaoSelecionado) {
        alert('Selecione um cartão para finalizar a compra.');
        return;
    }
    if (carrinho.length === 0) {
        alert('Seu carrinho está vazio.');
        return;
    }

    fetch('../carrinho/finalizar_pedido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            cartao_id: cartaoSelecionado.value,
            endereco: endereco,
            cep: cep,
            valor_frete: valor_frete,
            valor_desconto: valor_desconto,
            valor_total: valor_total,
            itens: carrinho
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.sucesso) {
            // Limpa o carrinho
            localStorage.removeItem('carrinho');
            // Mostra popup de sucesso
            mostrarPopupPedido(data.pedido_id);
        } else {
            alert('Erro ao finalizar pedido. Tente novamente.');
        }
    });
});

function mostrarPopupPedido(pedidoId) {
    let popup = document.createElement('div');
    popup.className = 'popup active';
    popup.innerHTML = `
        <div class="popup-content" style="max-width:400px;text-align:center;">
            <span class="close" onclick="this.closest('.popup').remove()" style="position:absolute;top:10px;right:18px;font-size:28px;cursor:pointer;">&times;</span>
            <h2>Compra Realizada!</h2>
            <p>Seu pedido foi realizado com sucesso.</p>
            <p><strong>ID do Pedido:</strong> ${pedidoId}</p>
            <p>Obrigado por comprar na DreamCakes!<br>Seu pedido está a caminho 🍰</p>
            <button onclick="window.location.href='http://localhost/projeto-cupcake/dreamcakes/inicio/inicio.php'" style="background:#b6e2b6;color:#333;padding:10px 18px;border:none;border-radius:8px;cursor:pointer;margin-top:18px;">Voltar para Início</button>
        </div>
    `;
    document.body.appendChild(popup);
}