<?php

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