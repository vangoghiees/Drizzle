<?php
require "connection.php";

$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$cpf = $_POST['cpf'] ?? '';
$senha = $_POST['senha'] ?? '';
$bloco = $_POST['bloco'] ?? '';
$numero = $_POST['numero'] ?? '';

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$stmtCheck = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email OR cpf = :cpf");
$stmtCheck->execute(['email' => $email, 'cpf' => $cpf]);
$existe = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if ($existe) {
    // Usuário já existe, redireciona para a página de erro
    header("Location: ../../register.html?erro=1");
    exit();
};

$stmt = $pdo->prepare("INSERT INTO usuarios (nome_completo, email, cpf, senha, bloco_torre, numero_ap) VALUES (:nome, :email, :cpf, :senha, :bloco, :numero)");

try {
    $stmt->execute([
        'nome' => $nome,
        'email' => $email,
        'cpf' => $cpf,
        'senha' => $senhaHash,
        'bloco' => $bloco,
        'numero' => $numero
    ]);
    
    // se tiver dado certo vai pra pagina de login bb
    header("Location: ../../login.html?sucesso=1");
    exit();
} catch (Exception $e) {
    // se tiver dado errado continua no register
    header("Location: ../../register.html?erro=2");
    exit();
}
?>