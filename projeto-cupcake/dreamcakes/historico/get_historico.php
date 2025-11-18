<?php
session_start();
include '../config.php';

$usuario_id = $_SESSION['google_id'] ?? 0;

$sql = "SELECT p.id as pedido_id, p.valor_total, p.data_pedido, c.bandeira, c.banco, pi.quantidade, pr.nome as produto_nome, pr.imagem as produto_imagem
        FROM pedidos p
        JOIN cartoes_credito c ON p.cartao_id = c.id
        JOIN pedido_itens pi ON pi.pedido_id = p.id
        JOIN produtos pr ON pr.id = pi.produto_id
        WHERE p.usuario_id = ?
        ORDER BY p.data_pedido DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$historico = [];
while ($row = $result->fetch_assoc()) {
    $historico[] = $row;
}
header('Content-Type: application/json');
echo json_encode($historico);
?>