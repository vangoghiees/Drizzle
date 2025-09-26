<?php
require "connection.php";



// segurança: só entra se for sindico
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'sindico') {
    header("Location: ../../login.html");
    exit;
}

// contadores para os cards
$totalAvisos = $pdo->query("SELECT COUNT(*) FROM avisos")->fetchColumn();
$totalManutPend = $pdo->query("SELECT COUNT(*) FROM manutencoes WHERE status='Pendente'")->fetchColumn();
$totalReservas = $pdo->query("SELECT COUNT(*) FROM reservas WHERE status='Confirmada'")->fetchColumn();
$totalManut = $pdo->query("SELECT COUNT(*) FROM manutencoes")->fetchColumn();

// últimas entradas
$avisos = $pdo->query("SELECT * FROM avisos ORDER BY data DESC LIMIT 4")->fetchAll();
$manutencoes = $pdo->query("SELECT m.*, u.nome_completo 
                             FROM manutencoes m
                             LEFT JOIN usuarios u ON u.id=m.solicitante_id
                             ORDER BY data DESC LIMIT 3")->fetchAll();
$reservas = $pdo->query("SELECT r.*, u.nome_completo 
                          FROM reservas r
                          LEFT JOIN usuarios u ON u.id=r.morador_id
                          ORDER BY data DESC LIMIT 3")->fetchAll();
$reclamacoes = $pdo->query("SELECT r.*, u.nome_completo 
                             FROM reclamacoes r
                             LEFT JOIN usuarios u ON u.id=r.solicitante_id
                             ORDER BY data DESC LIMIT 2")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Painel do Síndico | Drizzle</title>
  <link rel="stylesheet" href="../../css/home_sindico.css">
</head>
<body>
  <aside class="sidebar">
    <h2>Drizzle</h2>
    <ul>
      <li><a href="home_sindico.php">Home</a></li>
    <li><a href="avisos.php">Avisos</a></li> 
      <li><a href="reserve_sindico.php">Reservas</a></li>
      <li><a href="manutencoes.php">Manutenções</a></li>
      <li><a href="reclamacoes.php">Reclamações</a></li>
    </ul>
  </aside>

  <main class="content">
    <h1>Bem-vindo, Síndico!</h1>

    <!-- Cards resumo -->
    <div class="cards">
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

    <!-- Criar aviso -->
    <section class="create-notice">
      <h2>Criar aviso</h2>
      <form action="criar_aviso.php" method="POST">
        <input type="text" name="titulo" placeholder="Título" required>
        <textarea name="descricao" placeholder="Digite o aviso"></textarea>
        <button type="submit">Publicar</button>
      </form>
    </section>

    <div class="grid-container">
      <!-- Últimos avisos -->
      <section class="grid-section">
        <h2>Últimos avisos</h2>
        <ul class="notices-list">
          <?php foreach($avisos as $a): ?>
          <li><?= $a['titulo'] ?> - <?= date('d/m/Y', strtotime($a['data'])) ?></li>
          <?php endforeach; ?>
        </ul>
      </section>

      <!-- Manutenções -->
      <section class="grid-section">
        <h2>Manutenções</h2>
        <table>
          <tr>
            <th>Solicitação</th>
            <th>Solicitante</th>
            <th>Data</th>
            <th>Status</th>
          </tr>
          <?php foreach($manutencoes as $m): ?>
          <tr>
            <td><?= $m['titulo'] ?></td>
            <td><?= $m['nome_completo'] ?? 'Anônimo' ?></td>
            <td><?= date('d/m/Y', strtotime($m['data'])) ?></td>
            <td><span class="status <?= strtolower($m['status']) ?>"><?= $m['status'] ?></span></td>
          </tr>
          <?php endforeach; ?>
        </table>
      </section>

      <!-- Reservas -->
      <section class="grid-section">
        <h2>Reservas</h2>
        <table>
          <tr>
            <th>Espaço</th>
            <th>Morador</th>
            <th>Data</th>
            <th>Status</th>
          </tr>
          <?php foreach($reservas as $r): ?>
          <tr>
            <td><?= $r['espaco'] ?></td>
            <td><?= $r['nome_completo'] ?></td>
            <td><?= date('d/m/Y', strtotime($r['data'])) ?></td>
            <td><span class="status <?= strtolower($r['status']) ?>"><?= $r['status'] ?></span></td>
          </tr>
          <?php endforeach; ?>
        </table>
      </section>

      <!-- Reclamações -->
      <section class="grid-section">
        <h2>Reclamações</h2>
        <table>
          <tr>
            <th>Reclamação</th>
            <th>Morador</th>
            <th>Data</th>
          </tr>
          <?php foreach($reclamacoes as $r): ?>
          <tr>
            <td><?= $r['titulo'] ?></td>
            <td><?= $r['anonimo'] ? "Anônimo" : $r['nome_completo'] ?></td>
            <td><?= date('d/m/Y', strtotime($r['data'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </table>
      </section>
    </div>
  </main>
</body>
</html>