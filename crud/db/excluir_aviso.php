<?php
require "connection.php";

// só sindico
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'sindico') {
    header("Location: login.html");
    exit;
}

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM avisos WHERE id=:id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        header("Location: avisos.php?excluido=1");
        exit;
    } else {
        echo "Erro ao excluir aviso";
    }
} else {
    echo "ID inválido";
}
