<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '\config.php'; 

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');
$senha = $input['senha'] ?? '';

if (!$email || !$senha) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos.']);
    exit;
}

$stmt = $conn->prepare("SELECT id, senha, bloq, tipo FROM usuarios WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$user) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não encontrado.']);
    exit;
}

if (!empty($user['bloq']) && $user['bloq'] == 1) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Conta bloqueada. Contate o suporte.']);
    exit;
}


// senha está armazenada com password_hash()
if (password_verify($senha, $user['senha'])) {
    // autentica
    $_SESSION['user_id'] = $user['id'];
    // manter compatibilidade com outras partes que usam google_id (opcional)
    $_SESSION['google_id'] = $user['id'];
    $_SESSION['tipo'] = (!empty($user['tipo']) && $user['tipo'] == 1);
    echo json_encode(['sucesso' => true, 'redirect' => '../dreamcakes/inicio/inicio.php']);
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Senha incorreta.']);
}