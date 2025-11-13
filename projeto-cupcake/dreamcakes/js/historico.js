document.addEventListener('DOMContentLoaded', function () {
    fetch('get_historico.php')
        .then(res => res.json())
        .then(lista => {
            const div = document.getElementById('historico-lista');
            if (!lista || lista.length === 0) {
                div.innerHTML = '<p style="color:#888;text-align:center;">Nenhuma compra realizada ainda.</p>';
                return;
            }
            let html = '';
            lista.forEach(item => {
                html += `
                    <div class="historico-item" style="background:#fff;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,0.06);padding:18px 18px 12px 18px;margin-bottom:18px;display:flex;align-items:center;gap:18px;">
                        <img src="../${item.produto_imagem}" alt="${item.produto_nome}" style="width:80px;height:80px;object-fit:cover;border-radius:10px;">
                        <div style="flex:1;">
                            <div style="font-weight:bold;font-size:17px;color:#4a2c2a;">${item.produto_nome}</div>
                            <div style="font-size:15px;color:#7a4a4a;">Quantidade: ${item.quantidade}</div>
                            <div style="font-size:15px;color:#7a4a4a;">Valor: <b>R$ ${parseFloat(item.valor_total).toFixed(2)}</b></div>
                            <div style="font-size:15px;color:#7a4a4a;">Forma de Pagamento: ${item.bandeira ? item.bandeira.toUpperCase() : ''} ${item.banco ? '- ' + item.banco : ''}</div>
                            <div style="font-size:13px;color:#aaa;">Pedido #${item.pedido_id} em ${new Date(item.data_pedido).toLocaleString('pt-BR')}</div>
                        </div>
                    </div>
                `;
            });
            div.innerHTML = html;
        });
});