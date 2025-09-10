<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha</title>
<style>
    body{
        background: url(../assets/images/backgroundsenha.png) no-repeat center fixed;
        font-family: Arial, sans-serif;
        background-size: cover;
        width: 100%;
        height: 100%;

    }
    form{
        display: block;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        background-color: white;
        box-shadow:gray 5px 5px 15px;
        padding: 50px;
        border-radius: 10px;
        height: 250px;
        width: 350px;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        position: absolute;
    }
    input{
        padding: 10px;
        border-radius: 10px;
        width: 260px;
        height: auto;
        border: 1px solid lightgray;
        margin-top: 5px;
        font-size: 17px;
    }
    input:active, input:focus{
        outline: none;
        border-color: orange;
    }
    #btn{
            margin-top: 20px;
            background: blue;
            color: white;           
            border: none;
            width: 35%;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 15px;
            margin-bottom: 20px;
            }
</style>
</head>
<body>

<form action="senha.php" method="POST">
<h2>Recuperar Senha</h2>
<input type="email" placeholder="Digite seu email para recuperar a senha" name="email" required>
<button id="btn">Recuperar</button>
</form>

<?php
require '../config/db.php';

isset($_POST['email']) ? $email = $_POST['email'] : $email = '';

if (empty($email)) {
    function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    alert("<p>Por favor, insira um email válido.</p>");
    }
    exit;
}
$stmt = $pdo->prepare("UPDATE `usuario` SET `senha`='' WHERE 'id';");
$stmt->execute();

echo "<p>Senha redefinida com sucesso. Verifique seu email para a nova senha.</p>";
?>
</body>
</html>

