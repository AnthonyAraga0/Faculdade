function atualizarResumoCarrinho() {
    // Exemplo: obtendo do localStorage (ajuste para backend se necessário)
    let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
    let frete = 0.00;
    let desconto = 0.00;
    let total = 0.00;

    fetch('../buscar-produtos.php')
        .then(res => res.json())
        .then(produtos => {
            carrinho.forEach(item => {
                const produto = produtos.find(p => p.id == item.id);
                if (produto) {
                    total += produto.price * item.quantidade;
                }
            });

            // Aqui você pode calcular frete e desconto conforme sua regra
            // Exemplo: frete fixo de 0, desconto 0
            document.getElementById('valor-frete').textContent = 'R$ ' + frete.toFixed(2);
            document.getElementById('valor-desconto').textContent = 'R$ ' + desconto.toFixed(2);
            document.getElementById('valor-total').textContent = 'R$ ' + (total + frete - desconto).toFixed(2);
        });
}

document.addEventListener('DOMContentLoaded', function () {
    atualizarResumoCarrinho();
});