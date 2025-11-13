<?php
session_start();
include '../config.php';

$usuario_id = $_SESSION['google_id'] ?? 0;
$sql = "SELECT nome, email, endereco, cep, cgc FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
header('Content-Type: application/json');
echo json_encode($data);
?>