<?php
$blocked = isset($_GET['blocked']) && $_GET['blocked'] == '1';
$prefill_email = isset($_GET['email']) ? $_GET['email'] : '';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DreamCakes - Login</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="container">
    <div class="logo">
      <img src="images/im1.png" alt="DreamCakes Logo">
    </div>
    <div class="description">Entre com seu e-mail ou cadastre-se</div>

    <input id="email-input" class="input-field" type="email" placeholder="email@domain.com" autocomplete="email" />

    <div id="senha-area" style="display:none;margin-top:10px;">
      <input id="password-input" class="input-field" type="password" placeholder="Senha"
        autocomplete="current-password" />
      <div id="login-erro" style="color:#c00;margin-top:8px;"></div>
    </div>

    <?php if ($blocked): ?>
      <div id="login-erro1" style="color:#c00;margin-top:8px;">Conta bloqueada. Contate o suporte.</div>
    <?php endif; ?>

    <div id="btn-wrap" style="margin-top:12px;">
      <button id="login-pass-btn" class="btn-login">Entrar</button>
    </div>

    <div id="sem-conta" style="display:none;margin-top:10px;">
      <div style="margin-bottom:8px;color:#666;">E-mail não cadastrado.</div>
      <a id="cadastro-link" href="register.php" style="text-decoration:none;">
        <button class="cadastrar">Criar Conta</button>
      </a>
    </div>

    <div class="divider">ou</div>

    <button class="google-login-btn" onclick="window.location='google-oauth.php'">
      <img src="https://img.icons8.com/color/24/000000/google-logo.png" alt="Google"> Continue com Google
    </button>

    <div class="terms">
      Ao clicar em continuar, você aceita os <a href="#">Termos de Serviço</a> e a <a href="#">Política de
        Privacidade</a>
    </div>
  </div>

  <script src="js/login.js"></script>
</body>

</html>