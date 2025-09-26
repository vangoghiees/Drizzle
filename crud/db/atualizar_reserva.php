<?php
require "connection.php";

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'sindico') {
    http_response_code(403);
    echo "Acesso negado";
    exit;
}

if (isset($_POST['id'], $_POST['acao'])) {
    $id = (int) $_POST['id'];
    $acao = $_POST['acao'];

    $novoStatus = null;
    if ($acao === "aceitar") $novoStatus = "Confirmada";
    if ($acao === "recusar") $novoStatus = "Recusada";

    if ($novoStatus) {
        $stmt = $pdo->prepare("UPDATE reservas SET status=? WHERE id=?");
        $stmt->execute([$novoStatus, $id]);
        echo "ok";
    } else {
        echo "erro";
    }
} else {
    echo "dados inválidos";
}
