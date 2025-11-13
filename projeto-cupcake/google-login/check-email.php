<?php
header('Content-Type: application/json');
require_once __DIR__ . '\config.php';

$email = trim($_GET['email'] ?? '');
$debug = isset($_GET['debug']) && $_GET['debug'] == '1';

if ($email === '') {
    echo json_encode(['exists' => false, 'error' => 'empty email']);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND metodo <> 'google' LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
$exists = $stmt->num_rows > 0;
$stmt->close();

echo json_encode(['exists' => $exists]);