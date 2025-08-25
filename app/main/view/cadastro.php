<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cadastro</title>
</head>
<body>
    <!-- Cadastro -->
    <form action="../model/conexao.php" method="POST">
    <h2>Cadastro</h2>
    <div class="container">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" placeholder="Nome" required><br>
    </div>

    <div class="container">
        <label for="email">Email:</label>
        <input type="email" id="username" name="email" placeholder="nome@gmail.com" required><br>
    </div>

    <div class="container">
        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" placeholder="senha" required></input><br>
    
    <button type="submit" id="btn">Cadastrar</button>

    <?php
    require_once '../model/conexao.php';
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    if(isset($_POST['nome']) && isset($_POST['email']) && isset($_POST['password'])){
    $query = "INSERT INTO `usuario`(`id_usuario`, `nome`, `email`, `senha`, `TIPO`) VALUES ('','$nome','$email','$password','$tipo_usuario')";
    $result = mysqli_query($link, $query);
    }else{
        echo "Erro ao cadastrar";
    }
    
    ?>
</form>
</body>
</html>
