<?php
session_start();
include '../config.php';

$data = json_decode(file_get_contents('php://input'), true);
$usuario_id = $_SESSION['google_id'] ?? 0;

if ($usuario_id && $data) {
    $stmt = $conn->prepare("INSERT INTO cartoes_credito (usuario_id, bandeira, banco, numero_final, nome_titular, validade) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "isssss",
        $usuario_id,
        $data['bandeira'],
        $data['banco'],
        $data['numero_final'],
        $data['nome_titular'],
        $data['validade']
    );
    $sucesso = $stmt->execute();
    $stmt->close();
    echo json_encode(['sucesso' => $sucesso]);
} else {
    echo json_encode(['sucesso' => false]);
}
?>