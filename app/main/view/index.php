<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="/js/script.js"></script>
    <title>Login</title>

</head>
<body>
    <div>
 
    <form action="/login" method="POST">
   <h2>Login</h2>
        <label for="username">Usuário:</label>
        <input type="text" id="username" name="username" required><br>
        
        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" required><br>
        <label for="remember">
            <input type="checkbox" id="remember" name="remember"> Lembrar-me
        </label>
        <a href=""></a>

        <button type="submit" id="btn">Entrar</button>
    </form>
    </div>
    <footer>
    <img src="../assets/logo.png" alt="Logo" class="logo">
    </footer>
</body>
</html>