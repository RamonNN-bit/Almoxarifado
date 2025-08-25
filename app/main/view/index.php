<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>

<body>
    <!-- Login -->
    <form action="../model/teste.php" method="POST">
    <h2>Login</h2>
    <div class="container">
        <label for="email">Email:</label>
        <input type="email" id="username" name="email" placeholder="nome@gmail.com" required><br>
    </div> 
    <div class="password-container">
        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" placeholder="senha" required><i class="bi-eye-fill" id="mostrarSenhaBtn" onclick="mostrarSenha()"></i></input><br>
    </div>
        <label id="remember">
        <input type="checkbox" id="remember" name="remember">Lembrar-me</input> </label>
        <a href="senha.php" id="forgotpassword">Esqueceu a Senha</a>

        <button type="submit" id="btn">Entrar</button>
    </form>

    <footer>
    <img src="../assets/logosemfundo.png" alt="Logo" class="logo">
    </footer>

</body>
<script src="script.js"></script>
</html>
