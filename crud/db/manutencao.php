<?php
require "connection.php";

$id = $_SESSION['usuario_id'];
$titulo = $_POST['titulo'];
$descricao = $_POST['descricao'];
$prioridade = $_POST['prioridade'];

$stmt = $pdo->prepare("INSERT INTO manutencoes (titulo, descricao, prioridade, solicitante_id) 
                       VALUES (:titulo, :descricao, :prioridade, :id)");
$stmt->execute([
    'titulo' => $titulo,
    'descricao' => $descricao,
    'prioridade' => $prioridade,
    'id' => $id
]);

header("Location: home_morador.php");
exit;

