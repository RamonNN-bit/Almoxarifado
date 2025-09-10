<?php
$pdo = new PDO('mysql:host=localhost;dbname=almoxarifado;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$host = 'localhost';
$usuario = 'root';
$senha = '';
$nome_banco = 'almoxarifado';

$link = mysqli_connect($host, $usuario, $senha, $nome_banco);
mysqli_set_charset($link, 'utf8');
if (!$link) {
    die("A Conexão falhou:" . mysqli_connect_error());
}
mysqli_set_charset($link, 'utf8');




date_default_timezone_set('America/Sao_Paulo');
?>