<?php
require "connection.php";

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
$stmt->execute(['email' => $email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario && password_verify($senha, $usuario['senha'])) {
    // cria a sessão do usuário
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['nome_completo'] = $usuario['nome_completo'];
    $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

    // redireciona para a página correta
    if ($usuario['tipo_usuario'] === 'sindico') {
        header("Location: home_sindico.php");
    } else {
        header("Location: home_morador.php");
    }
    exit();
} else {
    // email ou senha incorretos
    header("Location: login.html?erro=1");
    exit();
}
