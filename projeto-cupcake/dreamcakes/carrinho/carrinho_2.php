<?php
session_start();
$is_admin = !empty($_SESSION['tipo']) && (int) $_SESSION['tipo'] === 1;
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DreamCakes - Home</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Capriola&display=swap" rel="stylesheet">

</head>

<body>

    <!-- Barra superior de localização/avisos -->
    <div class="top-bar">
        <span>
            <img src="../images/icones/localizacao.png" alt="Localização"
                style="width:20px;vertical-align:middle;margin-left:6px;">
            Você está em João Pessoa - PB ▼</span>
        <span class="top-icons">
            <button class="notification-btn" title="Notificações"
                style="position:relative;background:transparent;border:none;cursor:pointer;">
                <img src="../images/icones/sino_branca.png" alt="Notificações" style="width:35px;">
                <span id="notificacao-badge"
                    style="display:none;position:absolute;top:-6px;right:-6px;background:#e94f4f;color:#fff;border-radius:50%;padding:2px 6px;font-size:12px;">0</span>
            </button>
        </span>
    </div>

    <!-- Barra Logo e menu principal -->
    <header>
        <div class="header-left">
            <div class="logo">
                <a href="http://localhost/projeto-cupcake/dreamcakes/inicio/inicio.php"
                    style="background-color: transparent;border:none;cursor:pointer;">
                    <img src="../images/logo.png" alt="Logo">
                </a>
            </div>
            <button class="icone-btn">
                <img src="../images/icones/carrinho.png" alt="Carrinho">
                Carrinho
            </button>
        </div>
        <div class="header-options">
            <div class="dropdown">
                <button class="dropdown-btn">
                    <img src="../images/icones/opcoes.png" alt="Opções"
                        style="width:50px;vertical-align:middle;margin-left:6px;">
                </button>
                <div class="dropdown-content">
                    <a href="http://localhost/projeto-cupcake/dreamcakes/carrinho/carrinho.php"><span
                            style="font-size:18px;">
                            <img src="../images/icones/carrinho.png" alt="Carrinho"
                                style="width:20px;vertical-align:middle;margin-left:6px;">
                        </span> Carrinho</a>
                    <a href="http://localhost/projeto-cupcake/dreamcakes/carteira/carteira.php"><span
                            style="font-size:18px;">
                            <img src="../images/icones/carteira.png" alt="Carteira"
                                style="width:20px;vertical-align:middle;margin-left:6px;">
                        </span> Carteira</a>
                    <a href="http://localhost/projeto-cupcake/dreamcakes/meu-perfil/meu-perfil.php"><span
                            style="font-size:18px;">
                            <img src="../images/icones/perfil.png" alt="Perfil"
                                style="width:20px;vertical-align:middle;margin-left:6px;">
                        </span> Meu Perfil</a>

                    <?php if ($is_admin): ?>
                        <a href="http://localhost/projeto-cupcake/dreamcakes/admin/admin.php"><span style="font-size:18px;">
                                <img src="../images/icones/cadeado.png" alt="Admin"
                                    style="width:20px;vertical-align:middle;margin-left:6px;">
                            </span> Painel Admin</a>
                    <?php endif; ?>

                    <a href="http://localhost/projeto-cupcake/google-login/profile.php"><span style="font-size:18px;">
                            <img src="../images/icones/logoff.png" alt="Sair"
                                style="width:20px;vertical-align:middle;margin-left:6px;">
                        </span> Sair</a>
                </div>
            </div>
        </div>
    </header>

    <main class="carrinho-main">
        <form class="carrinho-form">
            <div class="carrinho-pagamento">
                <label for="forma-pag">Forma de Pagamento:</label>
                <select id="forma-pag">
                    <option>Cartão de Crédito</option>
                    <option>Cartão de Débito</option>
                    <option>PIX</option>
                </select>
            </div>

            <div class="carrinho-cartoes">
                <label>Cartões de Crédito</label>
                <div id="cartoes-lista" style="margin-bottom: 12px;"></div>
            </div>

            <div class="carrinho-parcelamento">
                <label for="parcelamento">Parcelamento:</label>
                <select id="parcelamento" style="font-family: 'Capriola', Arial, sans-serif; font-size: 12px;">
                    <option>À Vista - 1x</option>
                    <option>2 Vezes - 2x</option>
                    <option>3 Vezes - 3x</option>
                    <option>4 Vezes - 4x</option>
                    <option>5 Vezes - 5x</option>
                    <option>6 vezes - 6x</option>
                </select>
            </div>

            <div class="carrinho-endereco">
                <label for="endereco">Endereço:</label>
                <input id="endereco" type="text"
                    style="font-family: 'Capriola', Arial, sans-serif; font-size: 15px;width: 350px" required>
            </div>
            <div class="carrinho-cep">
                <label for="cep">CEP:</label>
                <input id="cep" type="text"
                    style="font-family: 'Capriola', Arial, sans-serif; font-size: 15px;width: 350px" required
                    maxlength="8" pattern="\d{8}">
            </div>

            <div class="carrinho-resumo-box">
                <div>Frete: <span id="valor-frete">R$ 0,00</span></div>
                <div>Desconto: <span id="valor-desconto">R$ 0,00</span></div>
                <div>Total: <span id="valor-total">R$ 0,00</span></div>
            </div>

            <div class="carrinho-botoes">
                <a href="http://localhost/projeto-cupcake/dreamcakes/carrinho/carrinho.php">
                    <button type="button"
                        style="background:#e94f4f;color:#fff;padding:10px 18px;border:none;border-radius:8px;cursor:pointer;">Voltar</button>
                </a>
                <button type="button" id="finalizar-compra-btn"
                    style="background:#b6e2b6;color:#333;padding:10px 18px;border:none;border-radius:8px;cursor:pointer;">
                    Finalizar Compra
                </button>
            </div>
        </form>
    </main>

    <footer class="footer">
        <p>
            <img src="../images/icones/cupcake.png" alt="Cupcake"
                style="width:20px;vertical-align:middle;margin-left:6px;">
            DreamCakes - Cupcake Shop © 2025
        </p>
    </footer>

    <!-- popup de notificações-->
    <div id="notificacoes-popup" class="notificacoes-popup" style="display:none;">
        <div class="notificacoes-content">
            <button onclick="fecharNotificacoes()"
                style="position:absolute;top:10px;right:12px;border:none;background:transparent;font-size:20px;cursor:pointer;">&times;</button>
            <div id="notificacoes-lista"></div>
        </div>
    </div>

    <script src="../js/dropdown.js"></script>
    <script src="../js/atualizar_carrinho.js"></script>
    <script src="../js/buscar-dados-usuario.js"></script>
    <script src="../js/listar_cartoes.js"></script>
    <script src="../js/finalizar_compra.js"></script>
    <script src="../js/notificacoes.js"></script>


</body>

</html>