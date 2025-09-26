<?php
require "connection.php";

// segurança
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'sindico') {
    header("Location: login.html");
    exit;
}

// contadores (deixa como estava)
$totalAvisos = $pdo->query("SELECT COUNT(*) FROM avisos")->fetchColumn();
$totalManutPend = $pdo->query("SELECT COUNT(*) FROM manutencoes WHERE status='Pendente'")->fetchColumn();
$totalReservas = $pdo->query("SELECT COUNT(*) FROM reservas WHERE status='Confirmada'")->fetchColumn();
$totalManut = $pdo->query("SELECT COUNT(*) FROM manutencoes")->fetchColumn();
$AvisosExp = $pdo->query("SELECT COUNT(*) FROM avisos WHERE validade < CURDATE()")->fetchColumn();

// --- NOVO: lógica do filtro ---
$filtro = $_GET['filtro'] ?? 'ativos';

if ($filtro === 'expirados') {
    $avisos = $pdo->query("SELECT * FROM avisos WHERE validade < CURDATE() ORDER BY data DESC")->fetchAll();
} elseif ($filtro === 'todos') {
    $avisos = $pdo->query("SELECT * FROM avisos ORDER BY data DESC")->fetchAll();
} else { // ativos
    $avisos = $pdo->query("SELECT * FROM avisos WHERE validade >= CURDATE() OR validade IS NULL ORDER BY data DESC")->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Painel de avisos | Drizzle</title>
  <link rel="stylesheet" href="../../css/avisos.css">
</head>
<body>
  <aside class="sidebar">
    <h2>Drizzle</h2>
    <ul>
      <li><a href="home_sindico.php">Home</a></li>
      <li><a href="avisos.php">Avisos</a></li>
      <li><a href="reserve_sindico.php">Reservas</a></li>
      <li><a href="manutencoes.php">Manutenções</a></li>
      <li><a href="#">Reclamações</a></li>
    </ul>
  </aside>

  <main class="content">
    <h1>Avisos</h1>

    <!-- Criar aviso -->
    <section class="create-notice">
      <div class="midaviso">
        <div>
      <h2>Criar aviso</h2>
      <form action="criar_avisos.php" method="POST">
        <input type="text" name="titulo" placeholder="Título" required>
        <textarea name="descricao" placeholder="Digite o aviso"></textarea>

        <div class="linha-opcoes">
          <label>Válido até:
            <input type="date" name="validade">
          </label>

          <label><input name="importante" type="checkbox"> Importante</label>
          <label><input name="fixado" type="checkbox"> Fixado</label>
          <label><input name="evento" type="checkbox"> Evento</label>
        </div>

        <button type="submit">Publicar</button>
      </form>
        </div>
        <div>
<div class="search">
  <div class="search-container">
    <input type="text" placeholder="Pesquisar...">
    <button type="submit">🔍</button>
  </div>
  <a href="avisos.php?filtro=todos"><button>Todos</button></a>
  <a href="avisos.php?filtro=ativos"><button>Ativos</button></a>
  <a href="avisos.php?filtro=expirados"><button>Expirados</button></a>
</div>

<ul class="notices-list">
  <?php foreach($avisos as $a): ?>
    <?php 
      // aplica classe expirado se validade passada
      $classeExtra = '';
      if (!empty($a['validade']) && strtotime($a['validade']) < time()) {
          $classeExtra = ' expirado';
      }
    ?>
    <li class="<?= $classeExtra ?>">
      <strong><?= htmlspecialchars($a['titulo']) ?></strong>
      - <?= date('d/m/Y', strtotime($a['data'])) ?><br>
      <span><?= htmlspecialchars(mb_strimwidth($a['descricao'],0,60,'...')) ?></span>
    </li>
  <?php endforeach; ?>
</ul>

        </div>
          </div>
    </section>

    <div class="grid-container">
      <!-- Últimos avisos -->
      <section class="grid-section">
        <h2>Prévia dos avisos</h2>
       <ul class="notices-list">
  <?php foreach($avisos as $a): ?>
    <li>
      <div class="notice-info">
        <strong><?= htmlspecialchars($a['titulo']) ?></strong>
        <span class="date"><?= date('d/m/Y', strtotime($a['data'])) ?></span>
        <span class="desc"><?= mb_strimwidth($a['descricao'], 0, 60, '...') ?></span>
      </div>
      <div class="notice-actions">
        <a href="editar_aviso.php?id=<?= $a['id'] ?>" class="btn-editar">Editar</a>
        <a href="excluir_aviso.php?id=<?= $a['id'] ?>" 
           class="btn-excluir"
           onclick="return confirm('Tem certeza que deseja excluir este aviso?')">Excluir</a>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

      </section>

      <!-- Manutenções (cards abaixo) -->
      <div class="cards">
        <div class="card">
          <h3><?php echo $AvisosExp; ?></h3>
          <p>Avisos a expirar</p>
        </div>

        <div class="card">
          <h3><?php echo $totalAvisos; ?></h3>
          <p>Avisos ativos</p>
        </div>
        <div class="card">
          <h3><?php echo $totalManutPend; ?></h3>
          <p>Chamadas pendentes</p>
        </div>

        <div class="card">
          <h3><?php echo $totalReservas; ?></h3>
          <p>Reservas ativas</p>
        </div>
        <div class="card">
          <h3><?php echo $totalManut; ?></h3>
          <p>Solicitações de manutenção</p>
        </div>
      </div>
    </div>
  </main>
</body>
</html>
