<?php

ini_set('session.gc_maxlifetime', 604800);
session_set_cookie_params(604800); // cookie 7 dias
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "drizzle";
$port = "3306";

try{
    $pdo = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(Exception $e){
    echo "Erro: ".$e->getMessage();
}

?>