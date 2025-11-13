<?php

// parâmetros de busca/paginação
$q = trim((string)($_GET['q'] ?? ''));
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

$users = [];
$total = 0;
$db_error = null;

try {
    $whereSql = '';
    $params = [];

    if ($q !== '') {
        if (ctype_digit($q)) {
            $whereSql = 'WHERE id = ? OR nome LIKE ?';
            $params = [$q, "%{$q}%"];
        } else {
            $whereSql = 'WHERE nome LIKE ?';
            $params = ["%{$q}%"];
        }
    }

    if (isset($pdo) && $pdo instanceof PDO) {
        $countSql = "SELECT COUNT(*) FROM usuarios {$whereSql}";
        $stmt = $pdo->prepare($countSql);
        $stmt->execute($params ?: []);
        $total = (int)$stmt->fetchColumn();

        $selectSql = "SELECT id, nome, email, bloq, tipo FROM usuarios {$whereSql} ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($selectSql);
        if ($params) {
            $i = 1;
            foreach ($params as $pval) {
                $stmt->bindValue($i, $pval, is_numeric($pval) ? PDO::PARAM_INT : PDO::PARAM_STR);
                $i++;
            }
        }
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } elseif (isset($conn) && ($conn instanceof mysqli || get_class($conn) === 'mysqli')) {
        // COUNT
        if ($whereSql === '') {
            $res = $conn->query("SELECT COUNT(*) as cnt FROM usuarios");
            if ($res === false) throw new Exception('DB error (count usuarios): ' . $conn->error);
            $row = $res->fetch_assoc();
            $total = (int)$row['cnt'];
        } else {
            $countSql = "SELECT COUNT(*) as cnt FROM usuarios {$whereSql}";
            $stmt = $conn->prepare($countSql);
            if (!$stmt) throw new Exception('Erro ao preparar contagem usuarios: ' . $conn->error);
            if (count($params) === 2) $stmt->bind_param('is', $params[0], $params[1]);
            else $stmt->bind_param('s', $params[0]);
            if (!$stmt->execute()) { $stmt->close(); throw new Exception('Erro ao executar contagem usuarios: ' . $conn->error); }
            $r = $stmt->get_result();
            if ($r === false) { $stmt->bind_result($cnt); $stmt->fetch(); $total = (int)$cnt; $stmt->close(); }
            else { $row = $r->fetch_assoc(); $total = (int)$row['cnt']; $stmt->close(); }
        }

        // SELECT paginado
        $selectSql = "SELECT id, nome, email, bloq, tipo FROM usuarios {$whereSql} ORDER BY id DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($selectSql);
        if (!$stmt) throw new Exception('Erro ao preparar select usuarios: ' . $conn->error);
        if ($whereSql === '') {
            if (!$stmt->bind_param('ii', $perPage, $offset)) { $stmt->close(); throw new Exception('bind_param usuarios sem where: ' . $conn->error); }
        } else {
            if (count($params) === 2) {
                if (!$stmt->bind_param('isii', $params[0], $params[1], $perPage, $offset)) { $stmt->close(); throw new Exception('bind_param usuarios 2 params: ' . $conn->error); }
            } else {
                if (!$stmt->bind_param('sii', $params[0], $perPage, $offset)) { $stmt->close(); throw new Exception('bind_param usuarios 1 param: ' . $conn->error); }
            }
        }
        if (!$stmt->execute()) { $stmt->close(); throw new Exception('Erro ao executar select usuarios: ' . $conn->error); }

        $res = $stmt->get_result();
        if ($res === false) {
            $stmt->bind_result($id, $nome, $email, $bloq, $tipo);
            while ($stmt->fetch()) $users[] = ['id'=>$id,'nome'=>$nome,'email'=>$email,'bloq'=>$bloq,'tipo'=>$tipo];
            $stmt->close();
        } else {
            while ($row = $res->fetch_assoc()) $users[] = $row;
            $stmt->close();
        }
    } else {
        throw new Exception('Nenhuma conexão com o banco encontrada em config.php');
    }
} catch (Exception $ex) {
    $users = [];
    $db_error = $ex->getMessage();
}

$totalPages = (int)ceil($total / $perPage);
$prevPage = $page > 1 ? $page - 1 : null;
$nextPage = $page < $totalPages ? $page + 1 : null;
?>

<main class="admin-section">
    <h2>Gerenciar Usuários</h2>

    <?php if (!empty($db_error)): ?>
        <div style="color:#a00;margin-bottom:12px;">Erro ao carregar usuários: <?php echo htmlspecialchars($db_error); ?></div>
    <?php endif; ?>

    <form method="get" class="search-row" action="">
        <input type="search" name="q" placeholder="Pesquisar por nome ou ID" value="<?php echo htmlspecialchars($q); ?>">
        <button type="submit">Buscar</button>
    </form>

    <table class="admin-table" id="users-table">
        <thead>
            <tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Tipo</th><th>Status</th><th style="width:260px">Ações</th></tr>
        </thead>
        <tbody>
        <?php if (empty($users)): ?>
            <tr><td colspan="6" style="text-align:center;color:#666;padding:18px;">Nenhum usuário encontrado.</td></tr>
        <?php else: foreach ($users as $u): ?>
            <tr data-user-id="<?php echo (int)$u['id']; ?>">
                <td><?php echo (int)$u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['nome']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td class="cell-tipo"><?php echo ((int)$u['tipo']===1)?'Admin':'Usuário'; ?></td>
                <td class="cell-bloq"><?php echo (!empty($u['bloq']) && (int)$u['bloq']===1) ? '<span class="status-pill pill-blocked">Bloqueado</span>' : '<span class="status-pill pill-active">Ativo</span>'; ?></td>
                <td>
                    <?php if (!empty($u['bloq']) && (int)$u['bloq']===1): ?>
                        <button class="action-btn btn-unblock" data-action="unblock">Desbloquear</button>
                    <?php else: ?>
                        <button class="action-btn btn-block" data-action="block">Bloquear</button>
                    <?php endif; ?>
                    <?php if ((int)$u['tipo']===1): ?>
                        <button class="action-btn btn-removeadmin" data-action="demote">Remover Admin</button>
                    <?php else: ?>
                        <button class="action-btn btn-makeadmin" data-action="promote">Tornar Admin</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>

    <div class="pagination" aria-label="Paginação">
        <div style="margin-right:auto;color:#666;padding:8px 0;"><?php echo $total; ?> usuário(s) encontrado(s)</div>

        <?php
        $buildLink = function($p) use ($q) {
            $qs = [];
            if ($q !== '') $qs['q'] = $q;
            $qs['page'] = $p;
            return '?' . http_build_query($qs);
        };
        ?>

        <?php if ($prevPage): ?><a href="<?php echo $buildLink($prevPage); ?>">&laquo; Anterior</a><?php endif; ?>
        <a class="current"><?php echo $page; ?>/<?php echo max(1,$totalPages); ?></a>
        <?php if ($nextPage): ?><a href="<?php echo $buildLink($nextPage); ?>">Próxima &raquo;</a><?php endif; ?>
    </div>
</main>