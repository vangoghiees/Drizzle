<?php
require "connection.php";

// só sindico
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'sindico') {
    header("Location: login.html");
    exit;
}

$id = intval($_GET['id'] ?? 0);

// buscar aviso
$stmt = $pdo->prepare("SELECT * FROM avisos WHERE id = :id");
$stmt->execute([':id' => $id]);
$aviso = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$aviso) {
    die("Aviso não encontrado.");
}

// se enviou o form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $validade_raw = trim($_POST['validade'] ?? '');
    $importante = isset($_POST['importante']) ? 1 : 0;
    $fixado = isset($_POST['fixado']) ? 1 : 0;
    $evento = isset($_POST['evento']) ? 1 : 0;

    // normaliza data igual antes
    function normalize_date_for_db($d) {
        if ($d === '' || $d === null) return null;
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) return $d;
        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $d)) {
            $dt = DateTime::createFromFormat('d/m/Y', $d);
            if ($dt) return $dt->format('Y-m-d');
        }
        $dt = date_create($d);
        return $dt ? $dt->format('Y-m-d') : null;
    }

    $validade = normalize_date_for_db($validade_raw);

    $sql = "UPDATE avisos 
            SET titulo=:titulo, descricao=:descricao, validade=:validade,
                importante=:importante, fixado=:fixado, evento=:evento
            WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':titulo', $titulo);
    $stmt->bindValue(':descricao', $descricao);
    if ($validade === null) {
        $stmt->bindValue(':validade', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':validade', $validade);
    }
    $stmt->bindValue(':importante', $importante, PDO::PARAM_INT);
    $stmt->bindValue(':fixado', $fixado, PDO::PARAM_INT);
    $stmt->bindValue(':evento', $evento, PDO::PARAM_INT);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: avisos.php?editado=1");
        exit;
    } else {
        echo "Erro ao editar aviso";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Aviso</title>
</head>
<body>
  <h1>Editar Aviso</h1>
  <form method="post">
    <input type="text" name="titulo" value="<?= htmlspecialchars($aviso['titulo']) ?>" required><br><br>
    <textarea name="descricao"><?= htmlspecialchars($aviso['descricao']) ?></textarea><br><br>
    Válido até: <input type="date" name="validade" value="<?= htmlspecialchars($aviso['validade']) ?>"><br><br>
    <label><input type="checkbox" name="importante" <?= $aviso['importante']?'checked':'' ?>> Importante</label>
    <label><input type="checkbox" name="fixado" <?= $aviso['fixado']?'checked':'' ?>> Fixado</label>
    <label><input type="checkbox" name="evento" <?= $aviso['evento']?'checked':'' ?>> Evento</label><br><br>
    <button type="submit">Salvar alterações</button>
  </form>
  <p><a href="avisos.php">Voltar</a></p>
</body>
</html>
