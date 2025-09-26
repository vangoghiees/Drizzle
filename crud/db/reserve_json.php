<?php
require "connection.php";

// agora só pega confirmadas
$stmt = $pdo->prepare("SELECT id, espaco, data, hora_inicio, hora_fim, status FROM reservas WHERE status='Confirmada'");
$stmt->execute();
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$eventos = [];
foreach ($reservas as $r) {
    $eventos[] = [
        'id' => $r['id'],
        'title' => $r['espaco'],
        'start' => $r['data'] . "T" . $r['hora_inicio'],
        'end'   => $r['data'] . "T" . $r['hora_fim'],
        'color' => '#2ecc71' // verde para confirmadas
    ];
}

header('Content-Type: application/json');
echo json_encode($eventos);
