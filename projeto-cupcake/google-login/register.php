<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Criar Conta - DreamCakes</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Capriola&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="register-wrap" aria-labelledby="reg-title">
    <div class="register-header">
      <h2 id="reg-title">Criar Conta</h2>
      <div class="register-login-link">Já tem conta? <a class="small-link" href="login.php">Entrar</a></div>
    </div>

    <form id="register-form" class="register-form" method="post" action="register-action.php" autocomplete="on">
      <input name="nome" class="input-field" type="text" placeholder="Nome completo" required>
      <input id="email-field" name="email" class="input-field" type="email" placeholder="E-mail" required>

      <div class="field-row">
        <input name="cpf_cnpj" id="cpf-field" class="input-field half" type="text" placeholder="CPF ou CNPJ" inputmode="numeric" required>
        <input name="cep" id="cep-field" class="input-field half" type="text" placeholder="CEP" inputmode="numeric" required>
      </div>

      <input name="endereco" class="input-field" type="text" placeholder="Endereço" required>

      <div class="field-row">
        <input name="senha" id="senha" class="input-field half" type="password" placeholder="Senha" required>
        <input name="senha_confirm" id="senha_confirm" class="input-field half" type="password" placeholder="Confirmar senha" required>
      </div>

      <div class="btn-row">
        <button type="button" class="btn-secondary" onclick="location.href='login.php'">Voltar</button>
        <button type="submit" class="btn-primary">Cadastrar</button>
      </div>

      <div id="form-msg" class="help">Ao criar a conta você aceita os <a href="#" class="small-link">Termos</a>.</div>
      <div id="form-err" class="error" role="status" aria-live="polite"></div>
    </form>
  </main>

  <script src="js/register.js"></script>
</body>
</html>