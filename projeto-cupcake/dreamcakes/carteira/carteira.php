

<?php
session_start();
$is_admin = !empty($_SESSION['tipo']) && (int)$_SESSION['tipo'] === 1;
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
            <button class="notification-btn" title="Notificações" style="position:relative;background:transparent;border:none;cursor:pointer;">
                <img src="../images/icones/sino_branca.png" alt="Notificações" style="width:35px;">
                <span id="notificacao-badge" style="display:none;position:absolute;top:-6px;right:-6px;background:#e94f4f;color:#fff;border-radius:50%;padding:2px 6px;font-size:12px;">0</span>
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
                <img src="../images/icones/carteira.png" alt="Carteira">
                Carteira
            </button>
        </div>
        <div class="header-options">
            <div class="dropdown">
                <button class="dropdown-btn">
                    <img src="../images/icones/opcoes.png" alt="Opções"
                        style="width:50px;vertical-align:middle;margin-left:6px;">
                </button>
                <div class="dropdown-content">
                    <a href="http://localhost/projeto-cupcake/dreamcakes/carrinho/carrinho.php"><span style="font-size:18px;">
                        <img src="../images/icones/carrinho.png" alt="Carrinho" style="width:20px;vertical-align:middle;margin-left:6px;">
                    </span> Carrinho</a>
                    <a href="http://localhost/projeto-cupcake/dreamcakes/carteira/carteira.php"><span style="font-size:18px;">
                        <img src="../images/icones/carteira.png" alt="Carteira" style="width:20px;vertical-align:middle;margin-left:6px;">
                    </span> Carteira</a>
                    <a href="http://localhost/projeto-cupcake/dreamcakes/meu-perfil/meu-perfil.php"><span style="font-size:18px;">
                        <img src="../images/icones/perfil.png" alt="Perfil" style="width:20px;vertical-align:middle;margin-left:6px;">
                    </span> Meu Perfil</a>

                    <?php if ($is_admin): ?>
                        <a href="http://localhost/projeto-cupcake/dreamcakes/admin/admin.php"><span style="font-size:18px;">
                            <img src="../images/icones/cadeado.png" alt="Admin" style="width:20px;vertical-align:middle;margin-left:6px;">
                        </span> Painel Admin</a>
                    <?php endif; ?>
                                        
                    <a href="http://localhost/projeto-cupcake/google-login/profile.php"><span style="font-size:18px;">
                        <img src="../images/icones/logoff.png" alt="Sair" style="width:20px;vertical-align:middle;margin-left:6px;">
                    </span> Sair</a>
                </div>
            </div>
        </div>
    </header>

    <div class="cartoes-cadastrados-container">
        <h3>Meus Cartões</h3>
        <div id="lista-cartoes"></div>
    </div>

    <div style="margin-left: 2%;">
        <h3>Adicionar Cartão</h3>
    </div>

    <div class="cartao-form-container">
        <form id="formCartao" class="cartao-form">
            <h3>Cartões de Crédito</h3>
            <div class="form-group">
                <label for="banco">Banco:</label>
                <select id="banco" required>
                    <option value="">Selecione o banco</option>
                    <option value="001">Banco do Brasil - 001</option>
                    <option value="237">Bradesco - 237</option>
                    <option value="341">Itaú - 341</option>
                    <option value="033">Santander - 033</option>
                    <option value="104">Caixa Econômica Federal - 104</option>
                    <option value="422">Banco Safra - 422</option>
                    <option value="077">Banco Inter - 077</option>
                    <option value="212">Banco Original - 212</option>
                    <option value="623">Banco Pan - 623</option>
                    <option value="318">Banco BMG - 318</option>
                    <option value="655">Banco Votorantim - 655</option>
                    <option value="389">Banco Mercantil do Brasil - 389</option>
                    <option value="735">Banco Neon - 735</option>
                    <option value="336">Banco C6 - 336</option>
                    <option value="746">Banco Modal - 746</option>
                    <option value="237">Banco Next - 237</option>
                    <option value="208">Banco BTG Pactual - 208</option>
                    <option value="707">Banco Daycoval - 707</option>
                    <option value="082">Banco Topázio - 082</option>
                    <option value="246">Banco ABC Brasil - 246</option>
                    <option value="041">Banco Banrisul - 041</option>
                    <option value="756">Banco Sicoob - 756</option>
                    <option value="748">Banco Sicredi - 748</option>
                    <option value="121">Banco Agibank - 121</option>
                    <option value="021">Banco Banestes - 021</option>
                    <option value="037">Banco Banpará - 037</option>
                    <option value="028">Banco Banese - 028</option>
                    <option value="070">Banco BRB - 070</option>
                    <option value="003">Banco da Amazônia - 003</option>
                    <option value="004">Banco do Nordeste - 004</option>
                    <option value="643">Banco Pine - 643</option>
                    <option value="637">Banco Sofisa - 637</option>
                    <option value="348">Banco XP - 348</option>
                </select>
            </div>
            <div class="form-group">
                <label for="numero">Número:</label>
                <input type="text" id="numero" maxlength="19" autocomplete="off" required>
            </div>
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" maxlength="100" autocomplete="off" required>
            </div>
            <div class="form-group">
                <label for="validade">Mês/Ano:</label>
                <input type="text" id="validade" maxlength="7" placeholder="MM/YYYY" required>
            </div>
            <div class="form-group">
                <label for="cvv">CVV:</label>
                <input type="text" id="cvv" maxlength="4" required>
            </div>
            <div class="cartao-visual">
                <img id="bandeira-img" src="../images/icones/visa.png" alt="Bandeira"
                    style="width:40px;vertical-align:middle;">
                <div id="cartao-numero" class="cartao-numero">•••• •••• •••• ••••</div>
                <div id="cartao-nome" class="cartao-nome">NOME DO TITULAR</div>
                <div id="cartao-validade" class="cartao-validade">MM/AAAA</div>
            </div>
            <div class="cartao-form-botoes">
                <button type="button" onclick="window.history.back()"
                    style="background:#7bb7e2;color:#fff;padding:8px 18px;border:none;border-radius:8px;cursor:pointer;">Voltar</button>
                <button type="submit"
                    style="background:#7bb7e2;color:#fff;padding:8px 18px;border:none;border-radius:8px;cursor:pointer;">Adicionar</button>
            </div>
        </form>
    </div>

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
            <button class="notificacoes-close" aria-label="Fechar notificações">×</button>
        </div>
    </div>

    <script src="../js/products.js"></script>
    <script src="../js/dropdown.js"></script>
    <script src="../js/buscar-dados-cartao.js"></script>
    <script src="../js/notificacoes.js"></script>

</body>

</html>