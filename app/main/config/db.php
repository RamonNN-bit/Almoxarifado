<?php
// Configuração para Windows (XAMPP)
$host = 'localhost';
$usuario = 'root';
$senha = '';
$nome_banco = 'almoxarifado';

// Conexão PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$nome_banco;charset=utf8", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão PDO: " . $e->getMessage());
}

// Conexão MySQLi (para compatibilidade)
$link = mysqli_connect($host, $usuario, $senha, $nome_banco);
mysqli_set_charset($link, 'utf8');
if (!$link) {
    die("A Conexão falhou: " . mysqli_connect_error());
}

date_default_timezone_set('America/Sao_Paulo');
?>