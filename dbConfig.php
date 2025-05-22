<?php
$hostName = "localhost";
$dataBase = "newbank";
$user = "root";
$password = "";

header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Content-Security-Policy: default-src 'self';");

$conn = new mysqli($hostName, $user, $password, $dataBase);
$conn->set_charset("utf8");

if ($conn->connect_errno) {
    die("Erro ao conectar ao banco de dados. Por favor, tente novamente mais tarde.");
}   

?>