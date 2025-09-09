<?php
session_start();
if (!isset($_SESSION ["usuariologado"])) {
header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Itens</title>
    <style>
    table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 20px;
        }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
    body{
        font-family: Arial, sans-serif;
        height: 100vh;  
    }
    h2{
        text-align: center;
    }
    form {
        display:flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;

        gap: 7px;
        }
    input{
        padding: 8px;
        border: 1px solid;
        border-radius: 10px;
        }
    #btn{
      font-size: 16px;
      background-color: blue;
      color: white;
      border: 1px solid blue;
      border-radius: 15px;
      padding: 10px;
      cursor: pointer;
      transition: background-color 0.3s ease-in-out;
    }

    #btn:hover {
      transform: scale(1.2);
      transition: transform 0.2s ease-out;
      background-color: darkblue;
    }
    </style>
</head>
<body>
    <form method="POST" action="">
        <h1>Cadastro de Itens</h1>
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required><br><br>
        
        <label for="quantidade">Quantidade:</label>
        <input type="number" id="quantidade" name="quantidade" required><br><br>
        
        <label for="unidade">Unidade:</label>
        <input type="text" id="unidade" name="unidade" required><br><br>
        
        <label for="marca">Marca:</label>
        <input type="text" id="marca" name="marca"><br><br>
        
        <label for="modelo">Modelo:</label>
        <input type="text" id="modelo" name="modelo"><br><br>
        
        <button type="submit" id="btn">Cadastrar Item</button>
    </form>

    <?php if (empty($itens)): ?>
        <p>Nenhum item encontrado.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Quantidade</th>
                <th>Unidade</th>
                <th>Marca</th>
                <th>Modelo</th>
            </tr>
            <?php foreach ($itens as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($item['quantidade'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($item['unidade'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($item['marca'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($item['modelo'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?> 
