<?php
require "connection.php";

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// Busca usuário pelo email e senha (texto puro)
$stmt = $pdo->prepare("SELECT * FROM usuarios_teste WHERE email = :email AND senha = :senha");
$stmt->execute(['email' => $email, 'senha' => $senha]);

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    // Login correto
    header("Location: ../../teste.html");
    exit();
} else {
    // E-mail ou senha incorretos
    header("Location: login.html?erro=1");
    exit();
}
