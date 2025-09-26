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
  <title>Painel de reclamações | Drizzle</title>
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
    <h1>Reclamações e mensagens</h1>

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
            <th>Solicitação</th>
            <th>Solicitante</th>
            <th>Data</th>
            <th>Status</th>
          </tr>
          <tr>
            <?php foreach($reclamacoes as $r): ?>
            <td> <?= $r['titulo'] ?> </td>
            <td> <?= $r['nome_completo'] ?? 'Anônimo' ?> </td>
            <td> <?= date('d/m/Y', strtotime($r['data'])) ?> </'td>
            <td> <?= $r['status'] ?> </td>
            <?php endforeach; ?>
        </tr>
        </div>
            </table>

    </section>
    <section class="create-notice">
        <div class="solicitacao">
            <?php foreach($reclamacoes as $r): ?>
            <h2> Reclamação </h2>
            <div class="title"><h3><?= $r['titulo'] ?></h3>  <br>
            <h4>Detalhes</h4><h5><?= $r['descricao'] ?></h5>
            <h4>Solicitante</h4><h5><?= $r['nome_completo'] ?? 'Anônimo' ?></h5> 
            <h4> Data de solicitação</h4><h5><?= date('d/m/Y', strtotime($r['data'])) ?></h5> 
            <?php endforeach; ?>
        </div>
        <hr>
        <h2>Resposta</h2>
                <form action="criar_aviso.php" method="POST">
            <textarea name="descricao" placeholder="Digite o aviso"></textarea>
            <div><button type="submit">Publicar</button> <button type="submit">marcar como concluída</button></div>
    </div>
    </section>
    </div>

    <div class="grid-container">
      <!-- Últimos avisos -->
        <section class="create-notice">
            <h2>Mensagens</h2> 
        <table>
          <tr>
            <th>Título</th>
            <th>Solicitante</th>
          </tr>
          <tr>
            <?php foreach($reclamacoes as $r): ?>
            <td> <?= $r['titulo'] ?> </td>
            <td> <?= $r['nome_completo'] ?? 'Anônimo' ?> </td>
            <td> <button> Responder </button> </td>
            <?php endforeach; ?>
        </tr>
        </div>
            
        </table>
        </section>

      <!-- Manutenções -->
    <section class="create-notice">
        <div class="solicitacao">
            <?php foreach($reclamacoes as $r): ?>
            <h2> Mensagem </h2>
            <div class="title"><h3><?= $r['titulo'] ?></h3>
            <h5><?= $r['descricao'] ?></h5>
            <?php endforeach; ?>
        </div>
        <hr>
        <h2>Resposta</h2>
                <form action="criar_aviso.php" method="POST">
            <textarea name="descricao" placeholder="Digite o aviso"></textarea><br>
            <button type="submit">Publicar</button>
    </div>
    </section>

</main>
</body>
</html>