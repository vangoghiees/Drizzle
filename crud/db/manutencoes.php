<?php
require "connection.php";

// segurança: só entra se for sindico
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'sindico') {
    header("Location: login.html");
    exit;
}

// contadores para os cards
$totalAvisos = $pdo->query("SELECT COUNT(*) FROM avisos")->fetchColumn();
$totalManutPend = $pdo->query("SELECT COUNT(*) FROM manutencoes WHERE status='Pendente'")->fetchColumn();
$totalReservas = $pdo->query("SELECT COUNT(*) FROM reservas WHERE status='Confirmada'")->fetchColumn();
$totalManut = $pdo->query("SELECT COUNT(*) FROM manutencoes")->fetchColumn();
$AvisosExp = $pdo->query("SELECT COUNT(*) FROM avisos")->fetchColumn();
$UltAviso = $pdo->query("SELECT COUNT(1) FROM reservas WHERE status='confirmada'")->fetchColumn();

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
  <title>Painel de manutenções | Drizzle</title>
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
      <li><a href="reclamacoes.php">Reclamações</a></li>
    </ul>
  </aside>

<main class="content">
    <h1>Manutenções</h1>

    <!-- Vizualizar manutenção -->
     <div class="grid-container">
    <section class="create-notice">
         
        
        <div class="topsearch">
            <div class="search-container">
                    <input type="text" placeholder="Pesquisar...">
                <button type="submit">🔍</button>
                <button> Todos </button> <button> Ativos </button> <button> Expirados </button>
            </div>  

        <div class="search">
        <table>
          <tr>
            <th>Prioridade</th>
            <th>Solicitação</th>
            <th>Solicitante</th>
            <th>Data</th>
            <th>Status</th>
          </tr>
          <tr>
            <?php foreach($manutencoes as $m): ?>
            <td> <?= $m['prioridade'] ?> </td>
            <td> <?= $m['titulo'] ?> </td>
            <td> <?= $m['nome_completo'] ?? 'Anônimo' ?> </td>
            <td> <?= date('d/m/Y', strtotime($m['data'])) ?> </'td>
            <td> <?= $m['status'] ?> </td>
            <?php endforeach; ?>
        </tr>
        </div>
            </table>

    </section>
    <section class="create-notice">
        <div class="solicitacao">
            <?php foreach($manutencoes as $m): ?>
            <h2> Solicitação </h2>
            <div class="title"><h3><?= $m['titulo'] ?></h3>  <br>
            <table><tr><th>Status da solicitação</th></tr> <tr><td><?= $m['status'] ?></td></tr></table>
            <table><tr><th>Solicitante</th></tr> <tr><td><?= $m['nome_completo'] ?? 'Anônimo' ?></td></tr></table> 
            <table><tr><th> Data de solicitação</th></tr> <tr><td><?= date('d/m/Y', strtotime($m['data'])) ?></td></tr></table> 
            <?php endforeach; ?>
        </div>
    </div>
    </section>
    </div>

    <div class="grid-container">
      <!-- Últimos avisos -->
        <section class="create-notice">
            <div class="vife">
                <h2>Solicitar manutenção</h2> 
                <label class="prioridade"> prioridade</label>
                <select class="prioridade">
                <option value="baixa">Baixa</option>
                <option value="media">Média</option>
                <option value="alta">Alta</option>
                </select> 
            </div>
                <form action="criar_aviso.php" method="POST">
            <input type="text" name="titulo" placeholder="Título" required>
            <textarea name="descricao" placeholder="Digite o aviso"></textarea>
            <button type="submit">Publicar</button>
            </form>
        </section>

      <!-- Manutenções -->
        <div class="cards">
            <div class="card">
            <h3><?php echo $AvisosExp; ?></h3>
            <p>Avisos a expirar</p>
        </div>
        <div class="card">
            <h3><?php echo $UltAviso; ?></h3>
            <p>Último aviso</p>
        </div>
    </div>

</main>
</body>
</html>