<?php
require_once '../includes/db.php';

if (!empty($_POST['nome']) && !empty($_POST['email']) && !empty($_POST['password'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    

    $query = "INSERT INTO usuario (nome, email, senha, TIPO) 
              VALUES ('$nome', '$email', '$password', null)";
    
    $result = mysqli_query($link, $query);

    if ($result) {
        header("Location: ../view/index.php?sucesso=1");
        exit;
    } else {
        echo "Erro ao cadastrar: " . mysqli_error($link);
    }
} else {
    header("Location: ../view/cadastro.php?erro=1");
    exit;
}
