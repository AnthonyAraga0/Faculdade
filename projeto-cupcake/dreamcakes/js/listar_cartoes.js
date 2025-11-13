document.addEventListener('DOMContentLoaded', function () {
    fetch('../carrinho/listar_cartoes.php')
        .then(res => res.json())
        .then(cartoes => {
            const lista = document.getElementById('cartoes-lista');
            if (!lista) return;
            if (!cartoes || cartoes.length === 0) {
                lista.innerHTML = '<p style="color:#888;">Nenhum cartão cadastrado.</p>';
                return;
            }
            lista.innerHTML = '';
            cartoes.forEach((cartao, idx) => {
                lista.innerHTML += `
                    <label class="cartao-listado${idx === 0 ? ' selected' : ''}">
                        <div class="cartao-esquerda">
                            <input type="radio" name="cartao_selecionado" value="${cartao.id}" ${idx === 0 ? 'checked' : ''}>
                            <img src="../images/icones/${cartao.bandeira || 'visa'}.png" alt="Bandeira" style="width: 38px;">
                        </div>
                        <div class="cartao-info">
                            <span class="cartao-nome" style="font-size: 14px;margin-bottom: 4px;">${cartao.nome_titular}</span>
                            <span style="font-size: 12px;color:#fff;">Banco: ${cartao.banco}</span>
                            <span style="font-size: 12px;color:#fff;">Final ${cartao.numero_final}</span>
                        </div>
                    </label>
                `;
            });

            // Destaque visual ao selecionar
            document.querySelectorAll('input[name="cartao_selecionado"]').forEach(radio => {
                radio.addEventListener('change', function () {
                    document.querySelectorAll('.cartao-listado').forEach(div => div.classList.remove('selected'));
                    this.closest('.cartao-listado').classList.add('selected');
                });
            });
        });
});