<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <title>Login</title>
    <style>
    body{
    background: url(../assets/images/backgroundlogin.png) no-repeat center center fixed;
    font-family: Arial, sans-serif;
    background-size: cover;
    background-attachment: fixed;
    min-height: 100vh;
    width: 100vw;
}
@media (max-width: 600px) {
body {
        background-size: cover;
        background-attachment: scroll;
        min-height: 100vh;
        width: 100vw;
    }
form {
        max-width: 95vw;
        padding: 18px ;
        height: auto;
    }
input {
        width: 100%;
    }
#btn {
        width: 100%;
    }
footer {
        width: 100%;
 }}

/*Nome Login */
h2{
    text-align: center;
    font-size: 2.5em;
    margin-top: 20px;
    margin-bottom: 7px;

}
/* formulario do login */
form{
    display:flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    border: 2px solid white;
    background: rgba(255, 255, 255, 0.94);
    box-shadow:gray 5px 5px 15px;
    padding: 60px;
    border-radius: 15px;
    height: 410px;
    width: 350px;
    position: absolute;
    top: 50%;
    left: 50%;
    gap: 20px;
    transform: translate(-50%, -50%);
}
.foco{
    display: block;
    margin-bottom: 5px;
    font-size: 16px; 
    color: #333;
    font-weight: bold;
}

#email, #password{
    display: block;
    margin-bottom: 2px;
    border-radius: 25px;
    border: 2px solid #e0e0e0;
    background: #f9f9f9;
    font-size: 16px;
    box-sizing: border-box;
    width: 280px;
    height: 50px;
    padding: 0 15px;
    transition: all 0.3s ease;
    font-family: Arial, sans-serif;
}

input:focus{
    border: 2px solid lightgray;
    border-color: linear-gradient(255, 119, 0),;
    outline: none;
}


/* lembrar-me e esqueciasenha */
.options{
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 280px;
}

#btn{
    width: 100%;
    max-width: 200px;
    margin-top: 10px;
    background: rgb(50, 190, 0);
    color: white;           
    border: none;
    height: auto;
    padding: 12px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 17px;
    transition: transform 0.1s, box-shadow 0.1s, background-image 0.1s;
}
#btn:hover{
    background-image: linear-gradient(to right, rgb(50, 190, 0), rgb(0, 150, 0));
    
}
#btn:active{
    transform: scale(1.05);
    background-image: linear-gradient(to right, rgb(50, 170, 0), rgb(0, 140, 0));
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}


/* olho da senha */
.password-container {
    position: relative;
    width: 280px;
}

#mostrarSenhaBtn {
    position: absolute;
    top: 50%;
    right: 15px;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 19px;
    color: #555;
    border: none;
    background: none;
    padding: 0;
    transition: color 0.3s ease;
}

#mostrarSenhaBtn:hover {
    color: #ff7700;
}
#password {
    padding-right: 40px;
}


/* redirecionamento */
a{
    color:gray;
    outline: none;  
    text-decoration: underline;
}


/* rodapé */
footer {
    background-color: rgb(255, 255, 255);
    padding: 10px;
    border-top-left-radius: 100%;
    border-top-right-radius: 100%;
    text-align: center;
    position: fixed;
    bottom: 0;
    width: 100%;
    z-index: 1000;
    

}

.logo {
    display: inline-block;
    height: 70px;
    background-size: contain;
}
    </style>
</head>
<body style="opacity:0; transition: opacity 1s;" onload="document.body.style.opacity='1'">
    <!-- Login -->
    <form id="loginForm" action="../config/rota.php" method="POST">
    <h2>Login</h2>
    <div class="container">
        <label for="email" class="foco">Email:</label>
        <input type="email" id="email" name="email" placeholder="nome@gmail.com" required autocomplete="off"><br>
    </div> 
    <div class="password-container">
        <label for="password" class="foco">Senha:</label>
        <input type="password" id="password" name="password" placeholder="senha" required autocomplete="off"><i class="bi-eye-fill" id="mostrarSenhaBtn" onclick="mostrarSenha()"></i></input><br>
    </div>
    <div class="options">
        <label id="remember"><input type="checkbox" name="rememberMe" id="remember"> Lembrar-me</label>
        <a href="senha.php" id="forgotpassword">Esqueci a Senha</a>
    </div>
        <button type="submit" id="btn">Entrar</button>
        <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
    </form>

    <footer>
    <img src="../assets/images/logo.png" alt="Logo" class="logo">
    </footer>

</body>
<script src="../assets/js/script.js"></script>
<script src="../assets/js/mostrarSenha.js"></script>
</html>
