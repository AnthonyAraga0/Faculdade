document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('register-form');
  const cpfField = document.getElementById('cpf-field').addEventListener('input', function (e) {
    this.value = this.value.replace(/\D/g, '').slice(0, 14);
  });
  const cepField = document.getElementById('cep-field').addEventListener('input', function (e) {
    this.value = this.value.replace(/\D/g, '').slice(0, 8);
  });
  const senha = document.getElementById('senha');
  const senhaConfirm = document.getElementById('senha_confirm');
  const errDiv = document.getElementById('form-err');

  function onlyDigits(v) { return String(v || '').replace(/\D/g, ''); }

  function formatCPFouCNPJ(v) {
    v = onlyDigits(v);
    if (v.length <= 11) {
      // CPF: 000.000.000-00
      v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, function (_, a, b, c, d) {
        return (a ? a : '') + (b ? '.' + b : '') + (c ? '.' + c : '') + (d ? '-' + d : '');
      });
      return v;
    } else {
      // CNPJ: 00.000.000/0000-00
      v = v.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2})/, function (_, a, b, c, d, e) {
        return (a ? a : '') + (b ? '.' + b : '') + (c ? '.' + c : '') + (d ? '/' + d : '') + (e ? '-' + e : '');
      });
    }
    return v;
  }

  function formatCEP(v) {
    v = onlyDigits(v);
    return v.replace(/(\d{5})(\d{0,3})/, function (_, a, b) { return a + (b ? '-' + b : ''); });
  }

  const applyMask = (el, fn) => {
    if (!el) return;
    el.addEventListener('input', function (e) {
      const start = this.selectionStart;
      this.value = fn(this.value);
      this.setSelectionRange(start, start);
    });
  };

  applyMask(cpfField, formatCPFouCNPJ);
  applyMask(cepField, formatCEP);

  form.addEventListener('submit', function (e) {
    errDiv.textContent = '';

    if (!form.checkValidity()) {
      form.reportValidity();
      e.preventDefault();
      return;
    }

    if ((senha.value || '').length < 6) {
      e.preventDefault();
      errDiv.textContent = 'Senha deve ter ao menos 6 caracteres.';
      senha.focus();
      return;
    }
    if (senha.value !== senhaConfirm.value) {
      e.preventDefault();
      errDiv.textContent = 'As senhas não coincidem.';
      senhaConfirm.focus();
      return;
    }

    if (cpfField) cpfField.value = onlyDigits(cpfField.value);
    if (cepField) cepField.value = onlyDigits(cepField.value);

  });

});