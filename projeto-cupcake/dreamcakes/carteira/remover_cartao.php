<?php
session_start();
include 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
$usuario_id = $_SESSION['google_id'] ?? 0;
$id = $data['id'] ?? 0;

if ($usuario_id && $id) {
    $stmt = $conn->prepare("DELETE FROM cartoes_credito WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $id, $usuario_id);
    $sucesso = $stmt->execute();
    $stmt->close();
    echo json_encode(['sucesso' => $sucesso]);
} else {
    echo json_encode(['sucesso' => false]);
}
?>