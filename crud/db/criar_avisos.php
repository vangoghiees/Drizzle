<?php
require "connection.php";

// checar permissão
if (!isset($_SESSION['usuario_id']) || ($_SESSION['tipo_usuario'] ?? '') !== 'sindico') {
    header("Location: login.html");
    exit;
}

// pegar dados do form
$titulo = trim($_POST['titulo'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$validade_raw = trim($_POST['validade'] ?? '');
$importante = isset($_POST['importante']) ? 1 : 0;
$fixado = isset($_POST['fixado']) ? 1 : 0;
$evento = isset($_POST['evento']) ? 1 : 0;

// função para normalizar data para Y-m-d ou null
function normalize_date_for_db($d) {
    if ($d === '' || $d === null) return null;

    // já no formato YYYY-MM-DD (input date)
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
        $dt = DateTime::createFromFormat('Y-m-d', $d);
        if ($dt && $dt->format('Y-m-d') === $d) return $d;
    }

    // se alguém enviou dd/mm/yyyy
    if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $d)) {
        $dt = DateTime::createFromFormat('d/m/Y', $d);
        if ($dt) return $dt->format('Y-m-d');
    }

    // tentar outras conversões seguras
    $dt = date_create($d);
    if ($dt) return $dt->format('Y-m-d');

    return null;
}

$validade = normalize_date_for_db($validade_raw);

// validações básicas
if ($titulo === '') {
    header("Location: avisos.php?erro=titulo");
    exit;
}

// Insere apenas nas colunas que existem no seu schema (veja o print da tabela)
$sql = "INSERT INTO avisos (titulo, descricao, validade, importante, evento, fixado, rascunho)
        VALUES (:titulo, :descricao, :validade, :importante, :evento, :fixado, :rascunho)";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':titulo', $titulo);
$stmt->bindValue(':descricao', $descricao);
if ($validade === null) {
    $stmt->bindValue(':validade', null, PDO::PARAM_NULL);
} else {
    $stmt->bindValue(':validade', $validade);
}
$stmt->bindValue(':importante', $importante, PDO::PARAM_INT);
$stmt->bindValue(':evento', $evento, PDO::PARAM_INT);
$stmt->bindValue(':fixado', $fixado, PDO::PARAM_INT);
$stmt->bindValue(':rascunho', 0, PDO::PARAM_INT); // se quiser salvar rascunho, mude para 1

try {
    if ($stmt->execute()) {
        header("Location: avisos.php?sucesso=1");
        exit;
    } else {
        header("Location: avisos.php?erro=bd");
        exit;
    }
} catch (PDOException $e) {
    // opcional: logar erro em arquivo para debug
    // error_log($e->getMessage());
    header("Location: avisos.php?erro=bd");
    exit;
}
