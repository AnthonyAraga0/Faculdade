document.addEventListener('DOMContentLoaded', function () {
  const emailInput = document.getElementById('email-input');
  const senhaArea = document.getElementById('senha-area');
  const semConta = document.getElementById('sem-conta');
  const cadastroLink = document.getElementById('cadastro-link');
  const loginPassBtn = document.getElementById('login-pass-btn');
  const passwordInput = document.getElementById('password-input');
  const loginErro = document.getElementById('login-erro');

  async function verificarEmail(email) {
    try {
      const res = await fetch('check-email.php?email=' + encodeURIComponent(email));
      return await res.json(); // espera { exists: true/false }
    } catch (e) {
      return { exists: false, error: 'network' };
    }
  }

  function mostrarSenha() {
    if (semConta) semConta.style.display = 'none';
    if (senhaArea) senhaArea.style.display = 'block';
    if (passwordInput) {
      passwordInput.value = '';
      passwordInput.focus();
    }
  }

  async function tentarMostrarSenhaOuLogar() {
    loginErro.textContent = '';
    const email = (emailInput.value || '').trim();
    const senhaVisivel = senhaArea && getComputedStyle(senhaArea).display !== 'none';

    if (!email) {
      loginErro.textContent = 'Digite o e‑mail.';
      emailInput.focus();
      return;
    }

    // Se a senha ainda não está visível: valida o e-mail primeiro e mostra o campo
    if (!senhaVisivel) {
      const resp = await verificarEmail(email);
      if (resp && resp.exists) {
        mostrarSenha();
        return;
      } else {
        // e-mail não cadastrado -> mostrar opção de cadastro preenchendo o e-mail
        if (cadastroLink) cadastroLink.href = 'register.php?email=' + encodeURIComponent(email);
        if (semConta) semConta.style.display = 'block';
        return;
      }
    }

    // Se a senha já está visível -> tenta autenticar
    const senha = (passwordInput && passwordInput.value) ? passwordInput.value : '';
    if (!senha) {
      loginErro.textContent = 'Digite a senha.';
      passwordInput && passwordInput.focus();
      return;
    }

    try {
      const res = await fetch('login-action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, senha })
      });
      const data = await res.json();
      if (data.sucesso) {
        window.location.href = data.redirect || '../dreamcakes/inicio/inicio.php';
      } else {
        loginErro.textContent = data.mensagem || 'Credenciais inválidas.';
      }
    } catch (err) {
      loginErro.textContent = 'Erro de conexão.';
    }
  }

  // clique no botão Entrar (sempre visível)
  if (loginPassBtn) {
    loginPassBtn.addEventListener('click', function (e) {
      e.preventDefault();
      tentarMostrarSenhaOuLogar();
    });
  }

  // Enter no e-mail -> tenta validar (mostra senha)
  if (emailInput) {
    emailInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        tentarMostrarSenhaOuLogar();
      }
    });
  }

  // Enter na senha -> tenta logar
  if (passwordInput) {
    passwordInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        tentarMostrarSenhaOuLogar();
      }
    });
  }
});