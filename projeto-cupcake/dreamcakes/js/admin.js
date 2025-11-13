
// módulo mínimo para chamadas AJAX ao endpoint admin_actions.php
async function postAction(action, userId) {
    const res = await fetch('admin_actions.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action, id: userId })
    });
    return res.json();
}

document.addEventListener('click', function (e) {
    const btn = e.target.closest('button.action-btn');
    if (!btn) return;
    const tr = btn.closest('tr');
    const userId = tr ? tr.getAttribute('data-user-id') : null;
    const action = btn.getAttribute('data-action');
    if (!userId || !action) return;

    btn.disabled = true;
    btn.textContent = 'Aguarde...';

    postAction(action, userId).then(json => {
        btn.disabled = false;
        if (!json || !json.success) {
            alert(json && json.message ? json.message : 'Erro na ação');
            btn.textContent = btn.getAttribute('data-action') === 'block' ? 'Bloquear' : (btn.getAttribute('data-action') === 'unblock' ? 'Desbloquear' : btn.textContent);
            return;
        }

        // atualiza UI da linha
        const row = document.querySelector('tr[data-user-id="' + userId + '"]');
        if (!row) return;
        // atualizar status
        const bloq = json.bloq;
        const tipo = json.tipo;
        const cellBloq = row.querySelector('.cell-bloq');
        const cellTipo = row.querySelector('.cell-tipo');
        const actionsCell = row.querySelector('td:last-child');

        if (cellBloq) {
            cellBloq.innerHTML = bloq == 1
                ? '<span class="status-pill pill-blocked">Bloqueado</span>'
                : '<span class="status-pill pill-active">Ativo</span>';
        }
        if (cellTipo) {
            cellTipo.textContent = tipo == 1 ? 'Admin' : 'Usuário';
        }

        // reconstruir botões
        actionsCell.innerHTML = '';
        const btn1 = document.createElement('button');
        btn1.classList.add('action-btn');
        if (bloq == 1) {
            btn1.classList.add('btn-unblock');
            btn1.setAttribute('data-action', 'unblock');
            btn1.textContent = 'Desbloquear';
        } else {
            btn1.classList.add('btn-block');
            btn1.setAttribute('data-action', 'block');
            btn1.textContent = 'Bloquear';
        }
        const btn2 = document.createElement('button');
        btn2.classList.add('action-btn');
        if (tipo == 1) {
            btn2.classList.add('btn-removeadmin');
            btn2.setAttribute('data-action', 'demote');
            btn2.textContent = 'Remover Admin';
        } else {
            btn2.classList.add('btn-makeadmin');
            btn2.setAttribute('data-action', 'promote');
            btn2.textContent = 'Tornar Admin';
        }
        actionsCell.appendChild(btn1);
        actionsCell.appendChild(btn2);
    }).catch(err => {
        btn.disabled = false;
        alert('Erro de conexão');
        btn.textContent = btn.getAttribute('data-action');
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const newBtn = document.getElementById('btn-new-product');
    const formWrap = document.getElementById('product-form-wrap');
    const prodForm = document.getElementById('product-form');
    const preview = document.getElementById('prod-preview');
    const prodImagem = document.getElementById('prod-imagem');
    const prodCancel = document.getElementById('prod-cancel-btn');
    const prodSaveBtn = document.getElementById('prod-save-btn');
    const prodMessage = document.getElementById('prod-message');

    newBtn && newBtn.addEventListener('click', () => {
        formWrap.style.display = 'block';
        prodForm.reset();
        document.getElementById('prod-id').value = '';
        preview.style.display = 'none';
    });

    prodCancel && prodCancel.addEventListener('click', () => { formWrap.style.display = 'none'; });

    prodImagem && prodImagem.addEventListener('change', (e) => {
        const f = e.target.files[0];
        if (!f) { preview.style.display = 'none'; return; }
        const url = URL.createObjectURL(f);
        preview.src = url; preview.style.display = 'block';
    });

    prodForm && prodForm.addEventListener('submit', async (ev) => {
        ev.preventDefault();
        prodMessage.textContent = '';
        prodSaveBtn.disabled = true;
        prodSaveBtn.textContent = 'Enviando...';

        const fd = new FormData(prodForm);
        fd.append('action', 'save');

        try {
            const res = await fetch('admin_products_actions.php', { method: 'POST', body: fd });
            const json = await res.json();
            prodSaveBtn.disabled = false;
            prodSaveBtn.textContent = 'Salvar Produto';
            if (!json || !json.success) {
                prodMessage.textContent = json && json.message ? json.message : 'Erro ao salvar';
                return;
            }
            // recarregar a página para ver mudanças simples (pode melhorar para atualizar via DOM)
            location.reload();
        } catch (err) {
            prodSaveBtn.disabled = false;
            prodSaveBtn.textContent = 'Salvar Produto';
            prodMessage.textContent = 'Erro de conexão.';
        }
    });

    // ações na tabela de produtos (delegation)
    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('button.product-action');
        if (!btn) return;
        const tr = btn.closest('tr');
        const id = tr && tr.getAttribute('data-prod-id');
        const action = btn.getAttribute('data-action');
        if (!id || !action) return;

        if (action === 'edit') {
            // buscar dados via AJAX e preencher form
            try {
                const res = await fetch('admin_products_actions.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'get', id: id })
                });
                const json = await res.json();
                if (!json || !json.success) { alert(json && json.message ? json.message : 'Erro'); return; }
                const p = json.product;
                formWrap.style.display = 'block';
                document.getElementById('prod-id').value = p.id;
                document.getElementById('prod-nome').value = p.nome;
                document.getElementById('prod-descricao').value = p.descricao;
                document.getElementById('prod-preco').value = p.preco;
                document.getElementById('prod-estoque').value = p.estoque;
                document.getElementById('prod-bloq').checked = p.bloq == 1;
                if (p.imagem) {
                    preview.src = '../images/products/' + p.imagem;
                    preview.style.display = 'block';
                } else preview.style.display = 'none';
            } catch (err) { alert('Erro de conexão'); }
            return;
        }

        if (!confirm('Confirma a ação "' + action + '" no produto #' + id + '?')) return;

        btn.disabled = true;
        btn.textContent = 'Aguarde...';
        try {
            const res = await fetch('admin_products_actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: action, id: id })
            });
            const json = await res.json();
            btn.disabled = false;
            if (!json || !json.success) { alert(json && json.message ? json.message : 'Erro'); btn.textContent = action; return; }
            // reload to reflect change
            location.reload();
        } catch (err) {
            btn.disabled = false;
            alert('Erro de conexão');
            btn.textContent = action;
        }
    });
});