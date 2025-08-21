<?php
require "connection.php";

$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$cpf = $_POST['cpf'] ?? '';
$senha = $_POST['senha'] ?? '';
$bloco = $_POST['bloco'] ?? '';
$numero = $_POST['numero'] ?? '';
$endereco = $_POST['endereco'] ?? '';
$celular = $_POST['celular'] ?? '';
$tipo_usuario = $_POST['tipo_usuario'] ?? 'morador'; // valor padrão

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// verifica duplicados
$stmtCheck = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email OR cpf = :cpf");
$stmtCheck->execute(['email' => $email, 'cpf' => $cpf]);
$existe = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if ($existe) {
    header("Location: ../../register.html?erro=1");
    exit();
}

// insere no banco
$stmt = $pdo->prepare("
    INSERT INTO usuarios 
    (nome_completo, email, cpf, senha, bloco_torre, numero_ap, endereco, celular, tipo_usuario)
    VALUES 
    (:nome, :email, :cpf, :senha, :bloco, :numero, :endereco, :celular, :tipo_usuario)
");

try {
    $stmt->execute([
        'nome' => $nome,
        'email' => $email,
        'cpf' => $cpf,
        'senha' => $senhaHash,
        'bloco' => $bloco,
        'numero' => $numero,
        'endereco' => $endereco,
        'celular' => $celular,
        'tipo_usuario' => $tipo_usuario
    ]);

    header("Location: ../../login.html?sucesso=1");
    exit();
} catch (Exception $e) {
    header("Location: ../../register.html?erro=2");
    exit();
}
?>
