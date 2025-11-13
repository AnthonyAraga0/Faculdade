<?php
session_start();
include '../config.php';

$usuario_id = $_SESSION['google_id'] ?? 0;
$sql = "SELECT id, bandeira, banco, numero_final, nome_titular FROM cartoes_credito WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$cartoes = [];
while ($row = $result->fetch_assoc()) {
    $cartoes[] = $row;
}
header('Content-Type: application/json');
echo json_encode($cartoes);
?>