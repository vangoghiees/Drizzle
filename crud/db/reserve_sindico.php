<?php
require "connection.php";

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'sindico') {
    header("Location: ../../login.html");
    exit;
}

$stmt = $pdo->prepare("
  SELECT r.id, r.espaco, u.nome_completo AS morador, r.data, r.hora_inicio, r.hora_fim, r.status
  FROM reservas r
  JOIN usuarios u ON u.id = r.morador_id
");
$stmt->execute();
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<script>let reservasDB = " . json_encode($reservas) . ";</script>";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Drizzle | Reservas</title>
  <link rel="stylesheet" href="../../css/reserve.css">

  <!-- FullCalendar -->
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet'>
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
  
  <!-- Box Icons -->
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  <style>
    /* Ajuste de altura para o calendário */
    #calendar {
      width: 100%;
      min-height: 500px; /* garante que apareça */
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="logo">
  <img src="../../assets/logo.png" alt="Logo Drizzle">
  <span>Drizzle</span>
</div>

    <nav>
      <a href="home_sindico.php"><i class='bx bx-home'></i> Home</a>
      <a href="avisos.php"><i class='bx bx-bell'></i> Avisos</a>
      <a href="reserve_sindico.php" class="active"><i class='bx bx-calendar'></i> Reservas</a>
      <a href="manutencoes.php"><i class='bx bx-wrench'></i> Manutenções</a>
      <a href="reclamacoes.php"><i class='bx bx-message-square-dots'></i> Reclamações</a>
    </nav>
  </aside>

  <!-- Main -->
  <main class="content">
    <h1>Reservas</h1>

    <!-- Filtros -->
    <div class="filtros">
      <button class="todas" onclick="filtrarReservas('Todas')">Todas</button>
      <button class="confirmada" onclick="filtrarReservas('Confirmada')">Confirmadas</button>
      <button class="pendente" onclick="filtrarReservas('Pendente')">Pendentes</button>
      <button class="recusada" onclick="filtrarReservas('Recusada')">Recusadas</button>
    </div>

    <!-- Tabela de Reservas -->
    <div class="tabela-container">
      <table class="tabela">
        <thead>
          <tr>
            <th>Espaço</th>
            <th>Morador</th>
            <th>Data</th>
            <th>Horário</th>
            <th>Status</th>
            <th>Ação</th>
          </tr>
        </thead>
        <tbody id="lista-reservas">
          <!-- JS vai preencher -->
        </tbody>
      </table>
    </div>

    <!-- Calendário e Legenda -->
<!-- Calendário + Detalhes juntos -->
<div class="calendario-wrapper">
  <div id="calendar"></div>
  <div class="detalhes-evento" id="detalhes-evento">
    <strong>Dia:</strong> selecione um evento<br>
  </div>
</div>


  </main>

  <script>
document.addEventListener('DOMContentLoaded', function() {
  let calendarEl = document.getElementById('calendar');
  let detalhesEl = document.getElementById('detalhes-evento');

  fetch('reserve_json.php')
    .then(res => res.json())
    .then(eventos => {
      let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        height: 180, // altura fixa
        headerToolbar: {
          left: '',
          center: 'title',
          right: 'prev,next today'
        },
        buttonText: {
          today: 'Hoje',
        },
        events: eventos,
        eventClick: function(info) {
          const dia = info.event.start.toLocaleDateString('pt-BR');
          detalhesEl.innerHTML = `
            <strong>Dia ${dia}</strong><br>
            <strong>${info.event.title}            ${info.event.start.toLocaleTimeString('pt-BR',{hour:'2-digit',minute:'2-digit'})}
            -
            ${info.event.end.toLocaleTimeString('pt-BR',{hour:'2-digit',minute:'2-digit'})}</strong><br>
          `;
        }
      });

      calendar.render();
    });
});


  let reservasFiltradas = reservasDB;

  function getStatusColor(status) {
    switch(status) {
      case 'Confirmada': return '#415b94';
      case 'Pendente': return '#f8c96d';
      case 'Recusada': return '#e07c7c';
      default: return '#6c757d';
    }
  }

  function renderizarTabela() {
    let tbody = document.getElementById("lista-reservas");
    tbody.innerHTML = "";

    reservasFiltradas.forEach(r => {
      let tr = document.createElement("tr");

      let botoes = "-";
     if (r.status === "Pendente") {
  botoes = `
    <button class="btn-aceitar" onclick="atualizarReserva(${r.id}, 'aceitar')">Aceitar</button>
    <button class="btn-recusar" onclick="atualizarReserva(${r.id}, 'recusar')">Recusar</button>
  `;
}


      tr.innerHTML = `
        <td>${r.espaco}</td>
        <td>${r.morador}</td>
        <td>${new Date(r.data).toLocaleDateString('pt-BR')}</td>
        <td>${r.hora_inicio} - ${r.hora_fim}</td>
        <td class="status-${r.status.toLowerCase()}">${r.status}</td>
        <td>${botoes}</td>
      `;
      tbody.appendChild(tr);
    });
  }

  function filtrarReservas(status) {
    reservasFiltradas = (status === "Todas") 
      ? reservasDB 
      : reservasDB.filter(r => r.status === status);
    renderizarTabela();
  }

function atualizarReserva(id, acao) {
  if (confirm('Tem certeza que deseja ' + (acao === 'aceitar' ? 'aceitar' : 'recusar') + ' esta reserva?')) {
    fetch("atualizar_reserva.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `id=${id}&acao=${acao}` // <<< agora envia acao
    })
    .then(res => res.text())
    .then(res => {
      if (res === "ok") {
        alert("Reserva atualizada!");
        location.reload();
      } else {
        alert("Erro: " + res);
      }
    });
  }
}

  </script>
</body>
</html>
