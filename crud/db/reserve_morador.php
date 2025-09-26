<?php
require "connection.php";


// Garante que o usuário está logado e é morador
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'morador') {
    header("Location: ../../login.html");
    exit;
}

$id = $_SESSION['usuario_id'];

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $espaco = trim($_POST['espaco']);
    $data = $_POST['data'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fim = $_POST['hora_fim'];

    if (empty($espaco) || empty($data) || empty($hora_inicio) || empty($hora_fim)) {
        die("Preencha todos os campos.");
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO reservas (morador_id, espaco, data, hora_inicio, hora_fim, status)
            VALUES (:morador_id, :espaco, :data, :hora_inicio, :hora_fim, 'Pendente')
        ");
        $stmt->execute([
            ':morador_id' => $id,
            ':espaco' => $espaco,
            ':data' => $data,
            ':hora_inicio' => $hora_inicio,
            ':hora_fim' => $hora_fim
        ]);

        // Redireciona de volta para a home do morador
        header("Location: home_morador.php?reserva=sucesso");
        exit;
    } catch (PDOException $e) {
        echo "Erro ao reservar: " . $e->getMessage();
    }
} else {
    header("Location: home_morador.php");
    exit;
}
?>
