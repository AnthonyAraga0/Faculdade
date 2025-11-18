function atualizarResumoCarrinho() {
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
                    total += produto.preco * item.quantidade;
                }
            });

            document.getElementById('valor-frete').textContent = 'R$ ' + frete.toFixed(2);
            document.getElementById('valor-desconto').textContent = 'R$ ' + desconto.toFixed(2);
            document.getElementById('valor-total').textContent = 'R$ ' + (total + frete - desconto).toFixed(2);
        });
}

document.addEventListener('DOMContentLoaded', function () {
    atualizarResumoCarrinho();
});