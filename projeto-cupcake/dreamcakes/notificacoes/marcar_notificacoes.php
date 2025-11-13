<?php
session_start();
include '../config.php';

$usuario_id = $_SESSION['google_id'] ?? 0;
if ($usuario_id) {
    $conn->query("UPDATE notificacoes SET visualizada = 1 WHERE usuario_id = $usuario_id");
    echo json_encode(['sucesso' => true]);
} else {
    echo json_encode(['sucesso' => false]);
}
?>