<?php
session_start();
include '../config.php';

$data = json_decode(file_get_contents('php://input'), true);
$usuario_id = $_SESSION['google_id'] ?? 0;

if (!$usuario_id || !$data) {
    echo json_encode(['sucesso' => false]);
    exit;
}

// Garante valores numéricos válidos
$valor_frete = isset($data['valor_frete']) && $data['valor_frete'] !== '' ? floatval($data['valor_frete']) : 0.00;
$valor_desconto = isset($data['valor_desconto']) && $data['valor_desconto'] !== '' ? floatval($data['valor_desconto']) : 0.00;
$valor_total = isset($data['valor_total']) && $data['valor_total'] !== '' ? floatval($data['valor_total']) : 0.00;


// Salva o pedido
$stmt = $conn->prepare("INSERT INTO pedidos (usuario_id, cartao_id, endereco, cep, valor_frete, valor_desconto, valor_total) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
    "iissddd",
    $usuario_id,
    $data['cartao_id'],
    $data['endereco'],
    $data['cep'],
    $valor_frete,
    $valor_desconto,
    $valor_total
);
$stmt->execute();
$pedido_id = $stmt->insert_id;
$stmt->close();

$mensagem = "Sua compra #$pedido_id foi realizada com sucesso e já está em preparação!";
$stmtNotif = $conn->prepare("INSERT INTO notificacoes (usuario_id, mensagem) VALUES (?, ?)");
$stmtNotif->bind_param("is", $usuario_id, $mensagem);
$stmtNotif->execute();
$stmtNotif->close();

// Salva os itens do pedido
foreach ($data['itens'] as $item) {
    $produto_id = $item['id'];
    $quantidade = $item['quantidade'];
    // Busque o preço do produto
    $res = $conn->query("SELECT preco FROM produtos WHERE id = $produto_id");
    $row = $res->fetch_assoc();
    $preco_unitario = $row ? $row['price'] : 0;
    $stmt2 = $conn->prepare("INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
    $stmt2->bind_param("iiid", $pedido_id, $produto_id, $quantidade, $preco_unitario);
    $stmt2->execute();
    $stmt2->close();
}

echo json_encode(['sucesso' => true, 'pedido_id' => $pedido_id]);
?>