<?php
session_start();
include '../config.php';
if (empty($_SESSION['tipo']) || (int) $_SESSION['tipo'] !== 1) {
    header('HTTP/1.1 403 Forbidden');
    exit('Acesso negado. Apenas administradores.');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DreamCakes - Painel Admin</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        /* ajustes rápidos para a tabela de usuários */
        .admin-section {
            max-width: 1000px;
            margin: 24px auto;
            background: #fff;
            padding: 16px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        }

        .admin-section h2 {
            margin: 0 0 12px;
            font-size: 20px;
        }

        .search-row {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
        }

        .search-row input[type="search"] {
            flex: 1;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #e6e6e6;
            font-size: 15px;
        }

        .search-row button {
            padding: 10px 14px;
            border-radius: 8px;
            border: none;
            background: #4b2b20;
            color: #fff;
            cursor: pointer;
            font-weight: 700;
        }

        table.admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.admin-table th,
        table.admin-table td {
            padding: 10px 8px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        table.admin-table th {
            background: #fafafa;
        }

        .action-btn {
            padding: 8px 10px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            margin-right: 6px;
        }

        .btn-unblock {
            background: #2e7d32;
            color: #fff;
        }

        .btn-block {
            background: #e53935;
            color: #fff;
        }

        .btn-makeadmin {
            background: #4b2b20;
            color: #fff;
        }

        .btn-removeadmin {
            background: #888;
            color: #fff;
        }

        .status-pill {
            padding: 6px 8px;
            border-radius: 999px;
            font-size: 13px;
            display: inline-block;
        }

        .pill-blocked {
            background: #ffdede;
            color: #8a1e1e;
        }

        .pill-active {
            background: #e7f7ee;
            color: #1e7a3a;
        }

        .pagination {
            margin-top: 12px;
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: flex-end;
        }

        .pagination a {
            padding: 8px 12px;
            background: #fafafa;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            border: 1px solid #e6e6e6;
        }

        .pagination .current {
            background: #4b2b20;
            color: #fff;
            border-color: #4b2b20;
        }
    </style>
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
                <img src="../images/icones/cadeado.png" alt="Carrinho">
                Painel Admin
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

                    <?php if (!empty($_SESSION['tipo']) && (int) $_SESSION['tipo'] === 1): ?>
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

    <!-- inclui módulo Usuários -->
    <?php include __DIR__ . '/users_admin.php'; ?>

    <!-- inclui módulo Produtos -->
    <?php include __DIR__ . '/products_admin.php'; ?>

    <footer class="footer">
        <p>
            <img src="../images/icones/cupcake.png" alt="Cupcake"
                style="width:20px;vertical-align:middle;margin-left:6px;">
            DreamCakes - Cupcake Shop © 2025
        </p>
    </footer>

    <script src="../js/dropdown.js"></script>
    <script src="../js/notificacoes.js"></script>
    <script src="../js/admin.js"></script>

</body>

</html>