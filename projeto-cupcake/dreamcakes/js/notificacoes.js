document.querySelectorAll('.notification-btn').forEach(btn => {
    btn.addEventListener('click', async function () {
        await carregarNotificacoes();              // carrega e mostra popup
        document.getElementById('notificacoes-popup').style.display = 'flex';
    });
});

// Fecha popup ao clicar no botão de fechar
document.addEventListener('click', function (e) {
    if (e.target.closest('.notificacoes-close')) {
        fecharNotificacoes();
        return;
    }
});

// Fecha com ESC
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') fecharNotificacoes();
});

async function carregarNotificacoes() {
    try {
        const res = await fetch('../notificacoes/get_notificacoes.php');
        const lista = await res.json();
        const div = document.getElementById('notificacoes-lista');
        if (!div) return;
        if (!lista || lista.length === 0) {
            div.innerHTML = '<p style="color:#888;text-align:center;">Nenhuma notificação.</p>';
        } else {
            div.innerHTML = `
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                    <strong>Notificações</strong>
                    <button id="marcar-todas" style="background:#e94f4f;color:#fff;border:none;padding:6px 10px;border-radius:8px;cursor:pointer;margin-left:8px;">Marcar todas como lidas</button>
                </div>
                ${lista.map(n => `
                    <div class="notificacao-item ${n.visualizada == 0 ? 'nao-visualizada' : ''}" data-id="${n.id}">
                        <div class="notificacao-texto">${escapeHtml(n.mensagem)}</div>
                        <div class="notificacao-meta">
                            <small>${new Date(n.data_envio).toLocaleString('pt-BR')}</small>
                            <button class="mark-read" data-id="${n.id}" title="Marcar como lida">✓</button>
                        </div>
                    </div>
                `).join('')}
            `;
            // Delegação de eventos: marcar individual
            div.querySelectorAll('.mark-read').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    const id = btn.dataset.id;
                    await marcarNotificacao(id);
                    await atualizarBadgeNotificacoes();
                    await carregarNotificacoes();
                });
            });

            // botão marcar todas
            const todasBtn = document.getElementById('marcar-todas');
            if (todasBtn) {
                todasBtn.addEventListener('click', async () => {
                    await fetch('../notificacoes/marcar_notificacoes.php', { method: 'POST' });
                    await atualizarBadgeNotificacoes();
                    await carregarNotificacoes();
                });
            }

            // permitir clicar no item para abrir (pode abrir detalhes, aqui marca como lida)
            div.querySelectorAll('.notificacao-item').forEach(item => {
                item.addEventListener('click', async () => {
                    const id = item.dataset.id;
                    await marcarNotificacao(id);
                    // aqui você pode exibir detalhes da notificação
                    await atualizarBadgeNotificacoes();
                    await carregarNotificacoes();
                });
            });
        }
    } catch (err) {
        console.error('Erro ao carregar notificações', err);
    }
}

async function marcarNotificacao(id) {
    try {
        await fetch('../notificacoes/marcar_notificacao.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        });
    } catch (err) {
        console.error('Erro ao marcar notificação', err);
    }
}

function fecharNotificacoes() {
    const pop = document.getElementById('notificacoes-popup');
    if (pop) pop.style.display = 'none';
}

function escapeHtml(text) {
    if (!text) return '';
    return text.replace(/[&<>"'\/]/g, function (s) {
        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;', '/': '&#x2F;' })[s];
    });
}

async function atualizarBadgeNotificacoes() {
    try {
        const res = await fetch('../notificacoes/get_notificacoes.php');
        const lista = await res.json();
        const badge = document.getElementById('notificacao-badge');
        if (!badge) return;
        const naoLidas = lista.filter(n => n.visualizada == 0).length;
        if (naoLidas > 0) {
            badge.textContent = naoLidas;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    } catch (err) {
        console.error('Erro ao atualizar badge', err);
    }
}

setInterval(atualizarBadgeNotificacoes, 10000);
atualizarBadgeNotificacoes();