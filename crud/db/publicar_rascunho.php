<?php
require "connection.php";

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'sindico') {
    header("Location: ../../login.html");
    exit;
}

$id = $_POST['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("UPDATE avisos SET rascunho = 0 WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: avisos.php");
exit;
