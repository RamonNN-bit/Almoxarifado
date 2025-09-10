<?php
function getRelatorios() {
    global $conn;
    if ($conn === null) {
        die("");
    }   
    $query = "SELECT id_item, tipo, quantidade, data, id_usuario FROM movimentacao ORDER BY data DESC";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
    $relatorios = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $relatorios[] = $row;
    }
    return $relatorios;
}

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
    <title>Relatórios</title>
    <style>
       * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {  
    font-family: 'Roboto', Arial, sans-serif;
    background-color: #ffffff; /* White background */
    color: #333333; /* Dark text for contrast */
    padding: 20px;
    line-height: 1.6;
}

nav {
    background: #1565c0; /* Blue navigation background */
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

nav a {
    color: #ffffff; /* White text for nav links */
    text-decoration: none;
    font-weight: 500;
    padding: 10px 20px;
    border-radius: 5px;
    transition: background-color 0.3s ease, color 0.3s ease;
}

nav a:hover {
    background-color: #003087; /* Darker blue on hover */
    color: #ffffff;
}

h1 {
    text-align: center;
    color: #1565c0; /* Blue heading */
    margin: 20px 0;
    font-size: 2.2rem;
}

table {
    width: 80%;
    margin: 20px auto;
    border-collapse: collapse;
    background: #f5f5f5; /* Light grayish-white table background */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
}

th, td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0; /* Light gray border */
}

th {
    background: #1565c0; /* Blue header background */
    color: #ffffff; }

    </style> 
</head>
<body>
    <nav>
        <a href="itenscadastro.php">Itens</a> |
        <a href="usuarios.php">Usuários</a> |
        <a href="requisicoes.php">Requisições</a> |
        <a href="dashboard_Admin.php">Voltar</a>
    </nav>
    <h1>Relatórios</h1>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID Item</th>
                <th>Tipo</th>
                <th>Quantidade</th>
                <th>Data</th>
                <th>Usuário</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $relatorios = getRelatorios();
            foreach ($relatorios as $relatorio): ?>
                <tr>
                    <td><?php echo htmlspecialchars($relatorio['id_item']); ?></td>
                    <td><?php echo htmlspecialchars($relatorio['tipo']); ?></td>
                    <td><?php echo htmlspecialchars($relatorio['quantidade']); ?></td>
                    <td><?php echo htmlspecialchars($relatorio['data']); ?></td>
                    <td><?php echo htmlspecialchars($relatorio['id_usuario']); ?></td>
                    <td>
                        <a href="download_relatorio.php?id=<?php echo $relatorio['id_item']; ?>">Download</a> |
                        <a href="visualizar_relatorio.php?id=<?php echo $relatorio['id_item']; ?>">Visualizar</a>
                        
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
