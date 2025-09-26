<?php
require "connection.php";


// Garantir que está logado e que é morador
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'morador') {
    header("Location: ../../login.html");
    exit;
}

$id = $_SESSION['usuario_id'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Drizzle | Painel do Morador</title>
  <link rel="stylesheet" href="../../css/home_morador.css">
  <style>
    /* Estilo do popup */
    .overlay {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.6);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }
    .popup {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      width: 400px;
    }
    .popup h2 { margin-top: 0; }
    .popup input, .popup select {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .btn {
      background: #1e3a8a;
      color: #fff;
      padding: 10px 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .btn.cancelar { background: #6b7280; }
    .status.autorizado { color: green; font-weight: bold; }
    .status.pendente { color: orange; font-weight: bold; }
    .status.negado { color: red; font-weight: bold; }
  </style>
</head>
<body>
<header>
    <h1>Olá, Morador!</h1>
    <div class="logo"><img src="../../assets/logo.png" alt="Logo Drizzle"></div>
</header>

<main class="grid">
    <!-- Últimos avisos -->
    <section class="card">
        <h2>Últimos avisos</h2>
        <input type="text" placeholder="Pesquisar">
        <ul>
            <?php
            $avisos = $pdo->query("SELECT * FROM avisos ORDER BY data DESC LIMIT 5");
            foreach ($avisos as $a) {
                echo "<li><span>{$a['titulo']}</span><span>" . date('d/m/Y', strtotime($a['data'])) . "</span></li>";
            }
            ?>
        </ul>
    </section>

    <!-- Minhas reservas -->
    <section class="card">
        <h2>Minhas reservas</h2>
        <ul>
            <?php
            $reservas = $pdo->prepare("SELECT * FROM reservas WHERE morador_id = :id ORDER BY data DESC LIMIT 5");
            $reservas->execute(['id' => $id]);
            if ($reservas->rowCount() > 0) {
                foreach ($reservas as $r) {
                    $statusClass = strtolower($r['status']);
                   echo "<li>
        <span>{$r['espaco']}</span>
        <span class='status {$statusClass}'>{$r['status']}</span>
        <span>" . date('d/m/Y', strtotime($r['data'])) . " 
        das " . date('H:i', strtotime($r['hora_inicio'])) . 
        " às " . date('H:i', strtotime($r['hora_fim'])) . "</span>
      </li>";

                }
            } else {
                echo "<li>Nenhuma reserva encontrada.</li>";
            }
            ?>
        </ul>
        <button class="btn" onclick="abrirPopup()">Reservar espaço</button>
    </section>

    <!-- Solicitar manutenção -->
    <section class="card">
        <h2>Solicitar Manutenção</h2>
        <form action="manutencao.php" method="POST">
            <input type="text" name="titulo" placeholder="Título" required>
            <select name="prioridade">
                <option value="Normal">Normal</option>
                <option value="Prioridade">Prioridade</option>
            </select>
            <textarea name="descricao" placeholder="Descrição"></textarea>
            <button type="submit">Enviar</button>
        </form>
    </section>

    <!-- Abrir reclamação -->
    <section class="card">
        <h2>Abrir Reclamação</h2>
        <form action="reclamacao.php" method="POST">
            <input type="text" name="titulo" placeholder="Título" required>
            <textarea name="descricao" placeholder="Descrição"></textarea>
            <label><input type="checkbox" name="anonimo" value="1"> Anônimo</label>
            <button type="submit">Publicar</button>
        </form>
    </section>

    <!-- Contatar síndico -->
    <section class="card">
        <h2>Contatar Síndico</h2>
        <form action="mensagem.php" method="POST">
            <input type="text" name="assunto" placeholder="Assunto" required>
            <textarea name="descricao" placeholder="Descrição"></textarea>
            <button type="submit">Enviar</button>
        </form>
    </section>
</main>

<!-- Popup reserva -->
<div class="overlay" id="popup">
  <div class="popup">
    <h2>Reservar</h2>
    <form action="reserve_morador.php" method="POST">
        <label>Local</label>
        <select name="espaco" required>
            <option value="">Selecione</option>
            <option value="Churrasqueira">Churrasqueira</option>
            <option value="Salão de Festas">Salão de Festas</option>
            <option value="Piscina">Piscina</option>
        </select>

        <label>Data</label>
        <input type="date" name="data" required>

        <label>Hora início</label>
        <input type="time" name="hora_inicio" required>

        <label>Hora fim</label>
        <input type="time" name="hora_fim" required>

        <br>
        <button type="submit" class="btn">Confirmar</button>
        <button type="button" class="btn cancelar" onclick="fecharPopup()">Cancelar</button>
    </form>
  </div>
</div>


<script>
function abrirPopup() {
  document.getElementById("popup").style.display = "flex";
}
function fecharPopup() {
  document.getElementById("popup").style.display = "none";
}
</script>

</body>
</html>
