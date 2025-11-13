const bandeiras = [
    { nome: "Visa", regex: /^4[0-9]{0,}/, img: "../images/icones/visa.png" },
    { nome: "MasterCard", regex: /^5[1-5][0-9]{0,}/, img: "../images/icones/mastercard.png" },
    { nome: "Elo", regex: /^6(4011|431274|438935|451416|457393|457631|457632|504175|506699|5067[0-9]{2}|509[0-9]{3}|627780|636297|636368|650[0-9]{3}|6516[0-9]{2}|6550[0-9]{2})/, img: "../images/icones/elo.png" },
    { nome: "Amex", regex: /^3[47][0-9]{0,}/, img: "../images/icones/amex.png" }
];

function detectarBandeira(numero) {
    numero = numero.replace(/\s/g, '');
    for (const b of bandeiras) {
        if (b.regex.test(numero)) return b.img;
    }
    return "../images/icones/visa.png";
}

document.getElementById('numero').addEventListener('input', function () {
    let val = this.value.replace(/\D/g, '').replace(/(.{4})/g, '$1 ').trim();
    this.value = val;
    document.getElementById('cartao-numero').textContent = val || '•••• •••• •••• ••••';
    document.getElementById('bandeira-img').src = detectarBandeira(val);
});
document.getElementById('nome').addEventListener('input', function () {
    document.getElementById('cartao-nome').textContent = this.value.toUpperCase() || 'NOME DO TITULAR';
});
document.getElementById('validade').addEventListener('input', function () {
    document.getElementById('cartao-validade').textContent = this.value || 'MM/AAAA';
});

document.getElementById('formCartao').addEventListener('submit', function (e) {
    e.preventDefault();
    const numero = document.getElementById('numero').value.replace(/\s/g, '');
    const nome = document.getElementById('nome').value;
    const validade = document.getElementById('validade').value;
    const cvv = document.getElementById('cvv').value;
    const banco = document.getElementById('banco').value;
    const bandeira = document.getElementById('bandeira-img').src.split('/').pop().split('.')[0];

    // Envia via AJAX para salvar_cartao.php
    fetch('salvar_cartao.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            numero_final: numero.slice(-4),
            nome_titular: nome,
            validade: validade,
            bandeira: bandeira,
            banco: banco,
            cvv: cvv
        })
    }).then(res => res.json())
      .then(data => {
        if (data.sucesso) {
            alert('Cartão salvo com sucesso!');
            window.location.reload();
        } else {
            alert('Erro ao salvar cartão!');
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    fetch('../carrinho/listar_cartoes.php')
        .then(res => res.json())
        .then(cartoes => {
            const lista = document.getElementById('lista-cartoes');
            if (!cartoes || cartoes.length === 0) {
                lista.innerHTML = '<p style="color:#888;">Nenhum cartão cadastrado.</p>';
                return;
            }
            lista.innerHTML = '';
            cartoes.forEach(cartao => {
                lista.innerHTML += `
                    <div class="cartao-listado">
                        <img src="../images/icones/${cartao.bandeira || 'visa'}.png" alt="Bandeira" style="width: 38px;">
                        <div class="cartao-info">
                            <span><strong>${cartao.nome_titular}</strong></span>
                            <span style="font-size:13px;color:#fff;">Banco: ${cartao.banco}</span>
                            <span style="font-size:13px;color:#fff;">Final ${cartao.numero_final}</span>
                        </div>
                        <button class="cartao-remover" title="Remover" onclick="removerCartao(${cartao.id})">
                            <img src="../images/icones/lixeira_branca.png" style="width: 20px;">
                        </button>
                    </div>
                `;
            });
        });
});

// Função para remover cartão
function removerCartao(id) {
    if (confirm('Deseja remover este cartão?')) {
        fetch('remover_cartao.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.sucesso) {
                location.reload();
            } else {
                alert('Erro ao remover cartão.');
            }
        });
    }
}
