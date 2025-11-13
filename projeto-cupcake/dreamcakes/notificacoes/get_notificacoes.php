<?php
session_start();
include '../config.php';

$usuario_id = $_SESSION['google_id'] ?? 0;

$sql = "SELECT id, mensagem, visualizada, data_envio FROM notificacoes WHERE usuario_id = ? ORDER BY data_envio DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$notificacoes = [];
while ($row = $result->fetch_assoc()) {
    $notificacoes[] = $row;
}
header('Content-Type: application/json');
echo json_encode($notificacoes);
?>