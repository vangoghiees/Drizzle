<?php
require "connection.php";

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
$stmt->execute(['email' => $email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario && password_verify($senha, $usuario['senha'])) { // verifica se a senha hasheada bate com a senha do banco
    // se o login estiver correto vai pra teste.html
    if ($usuario['tipo_usuario'] === 'sindico') {
        header("Location: ../../teste.html");
    } else {
        header("Location: ../../morador.html");
    }
    exit();
} else {
    // email ou senha incorretos, vai pra pagina de erro
    header("Location: login.html?erro=1");
    exit();
}
?>