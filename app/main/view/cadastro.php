<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <title>Cadastro</title>

<style>
@media (max-width: 768px) {
    form {
        padding: 20px;
        width: 95%;
    }
    .notificacao {
        right: 10px;
        max-width: 90%;
    }
}

h2 {
    text-align: center;
    font-size: 2.5em;
    margin-top: 20px;
    margin-bottom: 20px;
}

body {
    background: url(../assets/images/backgroundcadst.png) no-repeat center center fixed;
    background-size: cover;
    min-height: 100vh;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: Arial, sans-serif;
}

/* formulario de cadastro */
form {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: rgba(255, 255, 255, 0.95);
    border: 2px solid #e0e0e0;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    padding: 30px;
    border-radius: 10px;
    width: 90%;
    max-width: 400px;
    min-height: 350px;
    position: relative;
}

label {
    display: block;
    margin-bottom: 5px;
    font-size: 16px; 
    color: #333;
    font-weight: 500;
}

input[type="text"],
input[type="email"],
input[type="password"] {
    display: block;
    margin-bottom: 2px;
    border-radius: 25px;
    border: 2px solid #e0e0e0;
    background: #f9f9f9;
    font-size: 16px;
    box-sizing: border-box;
    width: 280px;
    height: 50px;
    padding: 0 40px 0 15px;
    transition: all 0.3s ease;
    font-family: Arial, sans-serif;
}

input:focus {
    border-color: #ff9500;
    outline: none;
}

input:invalid:not(:placeholder-shown) {
    border-color: #ff4444;
}

#btn {
    width: 100%;
    max-width: 275px;
    margin-top: 5px;
    margin-bottom: 20px;
    background: rgb(50, 190, 0);
    color: white;
    border: none;
    height: auto;
    padding: 12px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 15px;
    transition: transform 0.1s, box-shadow 0.1s, background-image 0.1s;
}

#btn:hover {
    background: linear-gradient(to right, rgb(50, 190, 0), rgb(0, 150, 0));
}

#btn:active {
    transform: scale(1.05);
    background: linear-gradient(to right, rgb(50, 170, 0), rgb(0, 140, 0));
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}

p {
    font-size: 17px;
    text-align: center;
}

a {
    color: gray;
    outline: none;
    text-decoration: underline;
}

#mostrarSenhaBtn {
    position: absolute;
    top: 50%;
    right: 15px;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 18px;
    color: #555;
    border: none;
    background: none;
    padding: 0;
    transition: color 0.3s ease;
}

#mostrarSenhaBtn:hover {
    color: #ff7700;
}

.container {
    position: relative;
    margin-bottom: 20px;
}
</style>
</head>

<body style="opacity:0; transition: opacity 1s;" onload="document.body.style.opacity='1'">
   
    <!-- Cadastro -->
    <form action="../model/cadastrousuarios.php" method="POST">
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
        <input type="password" id="password" name="password" placeholder="senha" required><i class="bi-eye-fill" id="mostrarSenhaBtn" onclick="mostrarSenha()"></i></input><br>
    </div>
    <button type="submit" id="btn">Cadastrar</button>
    <p>Já tem uma conta? <a href="index.php">Entrar</a></p>
    </form>


<script src="../js/mostrarSenha.js"></script> 
</body>
</html>
