document.addEventListener('DOMContentLoaded', function () {
    // Carrega dados do usuário
    fetch('get_usuario.php')
        .then(res => res.json())
        .then(data => {
            if (data) {
                document.getElementById('nome').value = data.nome || '';
                document.getElementById('email').value = data.email || '';
                document.getElementById('cpf_cnpj').value = data.cpf_cnpj || '';
                document.getElementById('endereco').value = data.endereco || '';
                document.getElementById('cep').value = data.cep || '';
            }
        });

    // Salva alterações
    document.getElementById('perfil-form').addEventListener('submit', function (e) {
        e.preventDefault();
        fetch('atualizar_usuario.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                nome: document.getElementById('nome').value,
                cpf_cnpj: document.getElementById('cpf_cnpj').value,
                endereco: document.getElementById('endereco').value,
                cep: document.getElementById('cep').value
            })
        })
        .then(res => res.json())
        .then(resp => {
            document.getElementById('perfil-msg').textContent = resp.sucesso ? 'Dados atualizados!' : 'Erro ao atualizar.';
            setTimeout(() => document.getElementById('perfil-msg').textContent = '', 3000);
        });
    });
});


// js/perfil.js

function formatarCPFouCNPJ(valor) {
    valor = valor.replace(/\D/g, '');
    if (valor.length <= 11) {
        // CPF: 000.000.000-00
        return valor.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, function(_, a, b, c, d) {
            let out = '';
            if (a) out += a;
            if (b) out += '.' + b;
            if (c) out += '.' + c;
            if (d) out += '-' + d;
            return out;
        });
    } else {
        // CNPJ: 00.000.000/0000-00
        return valor.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2})/, function(_, a, b, c, d, e) {
            let out = '';
            if (a) out += a;
            if (b) out += '.' + b;
            if (c) out += '.' + c;
            if (d) out += '/' + d;
            if (e) out += '-' + e;
            return out;
        });
    }
}

function formatarCEP(valor) {
    valor = valor.replace(/\D/g, '');
    return valor.replace(/(\d{5})(\d{0,3})/, function(_, a, b) {
        let out = a;
        if (b) out += '-' + b;
        return out;
    });
}

document.addEventListener('DOMContentLoaded', function () {
    fetch('get_usuario.php')
        .then(res => res.json())
        .then(data => {
            if (data) {
                document.getElementById('nome').value = data.nome || '';
                document.getElementById('email').value = data.email || '';
                document.getElementById('cpf_cnpj').value = formatarCPFouCNPJ(data.cgc || data.cpf_cnpj || '');
                document.getElementById('endereco').value = data.endereco || '';
                document.getElementById('cep').value = formatarCEP(data.cep || '');
            }
        });

    // Máscara ao digitar CPF/CNPJ
    document.getElementById('cpf_cnpj').addEventListener('input', function () {
        this.value = formatarCPFouCNPJ(this.value);
    });

    // Máscara ao digitar CEP
    document.getElementById('cep').addEventListener('input', function () {
        this.value = formatarCEP(this.value);
    });

    document.getElementById('perfil-form').addEventListener('submit', function (e) {
        e.preventDefault();
        fetch('atualizar_usuario.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                nome: document.getElementById('nome').value,
                cpf_cnpj: document.getElementById('cpf_cnpj').value.replace(/\D/g, ''),
                endereco: document.getElementById('endereco').value,
                cep: document.getElementById('cep').value.replace(/\D/g, '')
            })
        })
        .then(res => res.json())
        .then(resp => {
            document.getElementById('perfil-msg').textContent = resp.sucesso ? 'Dados atualizados!' : 'Erro ao atualizar.';
            setTimeout(() => document.getElementById('perfil-msg').textContent = '', 3000);
        });
    });
});