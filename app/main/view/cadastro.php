<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
</head>
<style>
    @media (max-width: 768px) {}
body {
    background: url(../assets/backgroundcadst.png) no-repeat center center fixed;
    background-size: cover;
    min-height: 100vh;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: Arial, sans-serif;
}

form {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: rgba(255, 255, 255, 0.95); /* Fundo com leve transparência */
    border: 2px solid #e0e0e0;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    padding: 30px;
    border-radius: 10px;
    width: 90%;
    max-width: 400px;
    min-height: 350px;
    position: relative;
}

h2 {
    text-align: center;
    font-size: 1.8em; /* Um pouco menor para telas menores */
    margin: 15px 0 25px;
    color: #333;
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
input[type="password"],
#username,
#password {
    width: 100%;
    max-width: 300px;
    padding: 10px;
    margin-bottom: 15px;
    border: 2px solid #d0d0d0;
    border-radius: 10px;
    font-size: 16px;
    box-sizing: border-box;
    transition: border-color 0.2s;
}

input:focus {
    border-color: #ff9500; /* Laranja mais vibrante */
    outline: none;
}

input:invalid:not(:placeholder-shown) {
    border-color: #ff4444; /* Borda vermelha para campos inválidos */
}

#btn {
    margin-top: 20px;
    background: rgb(50, 190, 0);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    width: 100%;
    max-width: 200px;
    transition:transform 0.1s, box-shadow 0.1s, background-image 0.1s;
    
}

#btn:hover {
    background: linear-gradient(to right, rgb(50, 190, 0), rgb(0, 150, 0));
}

#btn:active {
    transform: scale(1.05);
    background: linear-gradient(to right, rgb(50, 170, 0), rgb(0, 140, 0));
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}
</style>
<body>
    <!-- Cadastro -->
    <form action="../model/cadastrousuario.php" method="POST">
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
