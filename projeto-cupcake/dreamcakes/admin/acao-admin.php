<?php
session_start();

// verifica se é admin
if (empty($_SESSION['tipo']) || (int)$_SESSION['tipo'] !== 1) {
    http_response_code(403);
    echo json_encode(['success'=>false,'message'=>'Acesso negado']);
    exit;
}

include '../config.php';

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$id = isset($input['id']) ? (int)$input['id'] : 0;
if (!$id || !in_array($action, ['block','unblock','promote','demote'])) {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'Parâmetros inválidos']);
    exit;
}

try {
    if (isset($pdo) && $pdo instanceof PDO) {
        // buscar estado atual
        $stmt = $pdo->prepare('SELECT bloq, tipo FROM usuarios WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) throw new Exception('Usuário não encontrado');

        if ($action === 'block') {
            $newBloq = 1;
            $stmt = $pdo->prepare('UPDATE usuarios SET bloq = ? WHERE id = ?');
            $stmt->execute([$newBloq, $id]);
        } elseif ($action === 'unblock') {
            $newBloq = 0;
            $stmt = $pdo->prepare('UPDATE usuarios SET bloq = ? WHERE id = ?');
            $stmt->execute([$newBloq, $id]);
        } elseif ($action === 'promote') {
            $newTipo = 1;
            $stmt = $pdo->prepare('UPDATE usuarios SET tipo = ? WHERE id = ?');
            $stmt->execute([$newTipo, $id]);
        } elseif ($action === 'demote') {
            $newTipo = 0;
            $stmt = $pdo->prepare('UPDATE usuarios SET tipo = ? WHERE id = ?');
            $stmt->execute([$newTipo, $id]);
        }

        // retornar valores atualizados
        $stmt = $pdo->prepare('SELECT bloq, tipo FROM usuarios WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $updated = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['success'=>true,'bloq'=>(int)$updated['bloq'],'tipo'=>(int)$updated['tipo']]);
        exit;

    } elseif (isset($conn) && ($conn instanceof mysqli || get_class($conn) === 'mysqli')) {
        $stmt = $conn->prepare('SELECT bloq, tipo FROM usuarios WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        if (!$user) throw new Exception('Usuário não encontrado');

        if ($action === 'block') {
            $newBloq = 1;
            $stmt = $conn->prepare('UPDATE usuarios SET bloq = ? WHERE id = ?');
            $stmt->bind_param('ii', $newBloq, $id);
            $stmt->execute();
            $stmt->close();
        } elseif ($action === 'unblock') {
            $newBloq = 0;
            $stmt = $conn->prepare('UPDATE usuarios SET bloq = ? WHERE id = ?');
            $stmt->bind_param('ii', $newBloq, $id);
            $stmt->execute();
            $stmt->close();
        } elseif ($action === 'promote') {
            $newTipo = 1;
            $stmt = $conn->prepare('UPDATE usuarios SET tipo = ? WHERE id = ?');
            $stmt->bind_param('ii', $newTipo, $id);
            $stmt->execute();
            $stmt->close();
        } elseif ($action === 'demote') {
            $newTipo = 0;
            $stmt = $conn->prepare('UPDATE usuarios SET tipo = ? WHERE id = ?');
            $stmt->bind_param('ii', $newTipo, $id);
            $stmt->execute();
            $stmt->close();
        }

        $stmt = $conn->prepare('SELECT bloq, tipo FROM usuarios WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $updated = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        echo json_encode(['success'=>true,'bloq'=> (int)$updated['bloq'], 'tipo' => (int)$updated['tipo']]);
        exit;
    } else {
        throw new Exception('Nenhuma conexão com o banco encontrada');
    }
} catch (Exception $ex) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>$ex->getMessage()]);
    exit;
}
?>