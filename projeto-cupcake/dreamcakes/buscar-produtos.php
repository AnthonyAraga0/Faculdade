<?php
include 'config.php';

$sql = "SELECT id, nome, preco, imagem, descricao FROM produtos ORDER BY criado DESC LIMIT 10";
$result = $conn->query($sql);

$products = [];

while($row = $result->fetch_assoc()) {
    $products[] = $row;
}

header('Content-Type: application/json');
echo json_encode($products);
?>
