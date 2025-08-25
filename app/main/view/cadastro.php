<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cadastro</title>
</head>
<body>
    <!-- Cadastro -->
    <form action="../model/cadastro.php" method="POST">
    <h2>Cadastro</h2>
    <div class="container">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" placeholder="Nome" required><br>
    </div>

    <div class="container">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="nome@gmail.com" required><br>
    </div>

    <div class="container">
        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" placeholder="senha" required></input><br>
    
    <button type="submit" id="btn">Cadastrar</button>

</form>
</body>
</html>
