<?php
session_start();
require_once __DIR__ . '\config.php'; 

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$cpf = trim($_POST['cpf_cnpj'] ?? '');
$endereco = trim($_POST['endereco'] ?? '');
$cep = trim($_POST['cep'] ?? '');
$senha = $_POST['senha'] ?? '';
$senha_confirm = $_POST['senha_confirm'] ?? '';

if (!$nome || !$email || !$senha || !$senha_confirm || $senha !== $senha_confirm) {
    header('Location: register.php?error=1');
    exit;
}

// checar se email já existe
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    header('Location: register.php?error=exists');
    exit;
}
$stmt->close();

// inserir com senha hash
$hash = password_hash($senha, PASSWORD_DEFAULT);
$criacao = date('Y-m-d H:i:s');
$metodo = 'Site';
$is_blocked = 0;
$is_admin = 0;

$stmt = $conn->prepare("INSERT INTO usuarios (nome, email, cgc, endereco, cep, senha, criacao, metodo, bloq, tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    // opcional: registrar erro real em log
    header('Location: register.php?error=stmt');
    exit;
}
$stmt->bind_param("sssssss", $email, $nome, $foto, $criacao, $metodo, $bloq, $tipo);
$ok = $stmt->execute();
$insertId = $stmt->insert_id;
$stmt->close();

if ($ok) {
    $_SESSION['user_id'] = $insertId;
    $_SESSION['google_id'] = $insertId;
    header('Location: ../dreamcakes/inicio/inicio.php');
    exit;
} else {
    header('Location: register.php?error=save');
    exit;
}
?>