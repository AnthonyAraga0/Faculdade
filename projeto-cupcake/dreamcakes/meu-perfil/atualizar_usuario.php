<?php
session_start();
include '../config.php';

$usuario_id = $_SESSION['google_id'] ?? 0;
$data = json_decode(file_get_contents('php://input'), true);

if ($usuario_id && $data) {
    $stmt = $conn->prepare("UPDATE usuarios SET nome=?, cgc=?, endereco=?, cep=? WHERE id=?");
    $stmt->bind_param("ssssi", $data['nome'], $data['cpf_cnpj'], $data['endereco'], $data['cep'], $usuario_id);
    $sucesso = $stmt->execute();
    $stmt->close();
    echo json_encode(['sucesso' => $sucesso]);
} else {
    echo json_encode(['sucesso' => false]);
}
?>