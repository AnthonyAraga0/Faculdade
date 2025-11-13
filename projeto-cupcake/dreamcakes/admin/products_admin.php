<?php

// parâmetros de busca/paginação produto
$p_q = trim((string)($_GET['p_q'] ?? ''));
$p_page = max(1, (int)($_GET['p_page'] ?? 1));
$p_perPage = 10;
$p_offset = ($p_page - 1) * $p_perPage;

$products = [];
$p_total = 0;
$db_error_products = null;

try {
    $whereSql = '';
    $params = [];

    if ($p_q !== '') {
        if (ctype_digit($p_q)) {
            $whereSql = 'WHERE id = ? OR nome LIKE ?';
            $params = [$p_q, "%{$p_q}%"];
        } else {
            $whereSql = 'WHERE nome LIKE ?';
            $params = ["%{$p_q}%"];
        }
    }

    if (isset($pdo) && $pdo instanceof PDO) {
        $countSql = "SELECT COUNT(*) FROM produtos {$whereSql}";
        $stmt = $pdo->prepare($countSql);
        $stmt->execute($params ?: []);
        $p_total = (int)$stmt->fetchColumn();

        $selectSql = "SELECT id, nome, preco, estoque, imagem, bloq, criado FROM produtos {$whereSql} ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($selectSql);
        if ($params) {
            $i = 1; foreach ($params as $pval) { $stmt->bindValue($i, $pval, is_numeric($pval)?PDO::PARAM_INT:PDO::PARAM_STR); $i++; }
        }
        $stmt->bindValue(':limit', (int)$p_perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$p_offset, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } elseif (isset($conn) && ($conn instanceof mysqli || get_class($conn) === 'mysqli')) {
        if ($whereSql === '') {
            $res = $conn->query("SELECT COUNT(*) as cnt FROM produtos");
            if ($res === false) throw new Exception('DB error (count produtos): ' . $conn->error);
            $row = $res->fetch_assoc();
            $p_total = (int)$row['cnt'];
        } else {
            $countSql = "SELECT COUNT(*) as cnt FROM produtos {$whereSql}";
            $stmt = $conn->prepare($countSql);
            if (!$stmt) throw new Exception('Erro ao preparar contagem produtos: ' . $conn->error);
            if (count($params) === 2) $stmt->bind_param('is', $params[0], $params[1]);
            else $stmt->bind_param('s', $params[0]);
            if (!$stmt->execute()) { $stmt->close(); throw new Exception('Erro ao executar contagem produtos: ' . $conn->error); }
            $r = $stmt->get_result();
            if ($r === false) { $stmt->bind_result($cnt); $stmt->fetch(); $p_total = (int)$cnt; $stmt->close(); }
            else { $row = $r->fetch_assoc(); $p_total = (int)$row['cnt']; $stmt->close(); }
        }

        $selectSql = "SELECT id, nome, preco, estoque, imagem, bloq, criado FROM produtos {$whereSql} ORDER BY id DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($selectSql);
        if (!$stmt) throw new Exception('Erro ao preparar select produtos: ' . $conn->error);
        if ($whereSql === '') {
            if (!$stmt->bind_param('ii', $p_perPage, $p_offset)) { $stmt->close(); throw new Exception('bind_param produtos sem where: ' . $conn->error); }
        } else {
            if (count($params) === 2) {
                if (!$stmt->bind_param('isii', $params[0], $params[1], $p_perPage, $p_offset)) { $stmt->close(); throw new Exception('bind_param produtos 2 params: ' . $conn->error); }
            } else {
                if (!$stmt->bind_param('sii', $params[0], $p_perPage, $p_offset)) { $stmt->close(); throw new Exception('bind_param produtos 1 param: ' . $conn->error); }
            }
        }
        if (!$stmt->execute()) { $stmt->close(); throw new Exception('Erro ao executar select produtos: ' . $conn->error); }

        $res = $stmt->get_result();
        if ($res === false) {
            $stmt->bind_result($id,$nome,$preco,$estoque,$imagem,$bloq,$criado);
            while ($stmt->fetch()) $products[] = ['id'=>$id,'nome'=>$nome,'preco'=>$preco,'estoque'=>$estoque,'imagem'=>$imagem,'bloq'=>$bloq,'criado'=>$criado];
            $stmt->close();
        } else {
            while ($row = $res->fetch_assoc()) $products[] = $row;
            $stmt->close();
        }
    }
} catch (Exception $ex) {
    $products = [];
    $db_error_products = $ex->getMessage();
}

$p_totalPages = (int)ceil($p_total / $p_perPage);
$p_prevPage = $p_page > 1 ? $p_page - 1 : null;
$p_nextPage = $p_page < $p_totalPages ? $p_page + 1 : null;
?>

<main class="admin-section" style="margin-top:18px;">
    <h2>Gerenciar Produtos</h2>

    <?php if (!empty($db_error_products)): ?>
        <div style="color:#a00;margin-bottom:12px;">Erro ao carregar produtos: <?php echo htmlspecialchars($db_error_products); ?></div>
    <?php endif; ?>

    <form id="product-search-form" method="get" class="search-row" action="">
        <?php if (!empty($q)) echo '<input type="hidden" name="q" value="'.htmlspecialchars($q).'">'; ?>
        <?php if (!empty($page)) echo '<input type="hidden" name="page" value="'.(int)$page.'">'; ?>

        <input type="search" name="p_q" placeholder="Pesquisar produto por nome ou ID" value="<?php echo htmlspecialchars($p_q); ?>">
        <button type="submit">Buscar</button>
        <button type="button" id="btn-new-product" style="margin-left:8px;background:#2b7a78;color:#fff;border:none;padding:10px 12px;border-radius:8px;cursor:pointer;">Novo Produto</button>
    </form>

    <!-- form e tabela (id/classs mantidos) -->
    <div id="product-form-wrap" style="display:none;background:#fff;padding:12px;border-radius:8px;margin-bottom:12px;">
        <!-- form HTML igual ao anterior (omitido aqui por brevidade) -->
        <!-- copie o conteúdo do formulário de produtos que já estava no seu admin.php -->
    </div>

    <table class="admin-table" id="products-table">
        <thead>
            <tr><th>ID</th><th>Imagem</th><th>Nome</th><th>Preço</th><th>Estoque</th><th>Status</th><th style="width:260px">Ações</th></tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr><td colspan="7" style="text-align:center;color:#666;padding:18px;">Nenhum produto encontrado.</td></tr>
            <?php else: foreach ($products as $p): ?>
                <tr data-prod-id="<?php echo (int)$p['id']; ?>">
                    <td><?php echo (int)$p['id']; ?></td>
                    <td><?php if (!empty($p['imagem'])): ?><img src="../<?php echo htmlspecialchars($p['imagem']); ?>" style="width:64px;height:48px;object-fit:cover;border-radius:6px;"><?php else: ?><span style="color:#999">—</span><?php endif; ?></td>
                    <td><?php echo htmlspecialchars($p['nome']); ?></td>
                    <td>R$ <?php echo number_format($p['preco'],2,',','.'); ?></td>
                    <td><?php echo (int)$p['estoque']; ?></td>
                    <td><?php echo (!empty($p['bloq']) && (int)$p['bloq']===1) ? '<span class="status-pill pill-blocked">Bloqueado</span>' : '<span class="status-pill pill-active">Visível</span>'; ?></td>
                    <td>
                        <?php if (!empty($p['bloq']) && (int)$p['bloq']===1): ?>
                            <button class="action-btn btn-unblock product-action" data-action="unblock">Desbloquear</button>
                        <?php else: ?>
                            <button class="action-btn btn-block product-action" data-action="block">Bloquear</button>
                        <?php endif; ?>
                        <button class="action-btn btn-makeadmin product-action" data-action="edit">Editar</button>
                        <button class="action-btn btn-removeadmin product-action" data-action="delete">Excluir</button>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>

    <div class="pagination" aria-label="Paginação" style="margin-top:10px;">
        <div style="margin-right:auto;color:#666;padding:8px 0;"><?php echo $p_total; ?> produto(s) encontrado(s)</div>

        <?php
        $buildProdLink = function($pp) use ($p_q) {
            $qs = [];
            if ($p_q !== '') $qs['p_q'] = $p_q;
            $qs['p_page'] = $pp;
            return '?' . http_build_query($qs);
        };
        ?>

        <?php if ($p_prevPage): ?><a href="<?php echo $buildProdLink($p_prevPage); ?>">&laquo; Anterior</a><?php endif; ?>
        <a class="current"><?php echo $p_page; ?>/<?php echo max(1,$p_totalPages); ?></a>
        <?php if ($p_nextPage): ?><a href="<?php echo $buildProdLink($p_nextPage); ?>">Próxima &raquo;</a><?php endif; ?>
    </div>

    <script src="../js/admin-products.js"></script>
    <!-- pequeno script inline para abrir o form pode ficar aqui ou em admin-products.js -->
</main>