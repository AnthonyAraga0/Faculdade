
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
        <div class="logo">
            <a href="http://localhost/projeto-cupcake/dreamcakes/inicio/inicio.php"
                style="background-color: transparent;border:none;cursor:pointer;">
                <img src="../images/logo.png" alt="Logo">
            </a>
        </div>
        <div class="header-center">
            <div class="search-bar">
                <input type="text" placeholder="Buscar cupcakes, bolos...">
                <button class="search-btn-inside">
                    <img src="../images/icones/lupa.png" alt="Pesquisar">
                </button>
            </div>
            <div class="nav-buttons">
                <button>❤ Favoritos</button>
                <a href="http://localhost/projeto-cupcake/dreamcakes/historico/historico.php">
                    <button class="historico-btn">
                        <img src="../images/icones/historico.png" alt="Histórico"
                            style="width:20px;vertical-align:middle;margin-left:6px;">
                        Histórico
                    </button>
                </a>
                <button>★ Mais pedidos</button>
            </div>
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
                            <img src="../images/icones/cadeado.png" alt="Admin" style="width:20px;vertical-align:middle;margin-left:6px;">
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

    <!-- Banner promocional -->
    <section class="banner">
        <img id="banner-img" alt="Banner promocional">
    </section>

    <!-- Produtos trazidos via consulta -->
    <section class="products">
        <div class="products-list" id="products-list">
        </div>
    </section>

    <!-- Pesquisa por categoria -->
    <section class="categories">
        <h2>Categorias</h2>
        <div class="categories-list">
            <div>
                <img src="../images/halloween.jpg" alt="Halloween">
                <p>Halloween</p>
            </div>
            <div>
                <img src="../images/aniversario.jpg" alt="Aniversários">
                <p>Aniversários</p>
            </div>
            <div>
                <img src="../images/natal.png" alt="Natal">
                <p>Natal</p>
            </div>
            <div>
                <img src="../images/casamento.jpg" alt="Casamento">
                <p>Casamento</p>
            </div>
        </div>
    </section>

    <footer class="footer">
        <p>
            <img src="../images/icones/cupcake.png" alt="Cupcake"
                style="width:20px;vertical-align:middle;margin-left:6px;">
            DreamCakes - Cupcake Shop © 2025
        </p>
    </footer>

    <!-- POPUP de boas vindas -->
    <div class="popup" id="welcomePopup">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <img style="width: 500px;" src="../images/bem-vindo.jpg">
            <button onclick="closePopup()">Fechar</button>
        </div>
    </div>
    
    <!-- popup de notificações-->
    <div id="notificacoes-popup" class="notificacoes-popup" style="display:none;">
        <div class="notificacoes-content">
            <button class="notificacoes-close" aria-label="Fechar notificações">×</button>
            <div id="notificacoes-lista"></div>
        </div>
    </div>

    <script src="../js/welcome.js"></script>
    <script src="../js/products.js"></script>
    <script src="../js/banner.js"></script>
    <script src="../js/dropdown.js"></script>
    <script src="../js/notificacoes.js"></script>

</body>

</html>