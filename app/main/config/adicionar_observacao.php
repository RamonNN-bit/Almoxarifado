<?php
/**
 * Script simples para adicionar o campo observacao na tabela movimentacoes
 * Execute este arquivo via navegador ou linha de comando
 */

require_once 'db.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Banco de Dados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #059669;
            margin-bottom: 20px;
        }
        .success {
            background: #d1fae5;
            border: 1px solid #10b981;
            color: #065f46;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .error {
            background: #fee2e2;
            border: 1px solid #ef4444;
            color: #991b1b;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .info {
            background: #dbeafe;
            border: 1px solid #3b82f6;
            color: #1e40af;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        code {
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Atualização do Banco de Dados</h1>
        
        <?php
        try {
            echo '<div class="info">Verificando estrutura do banco de dados...</div>';
            
            // Verificar se o campo observacao já existe
            $check = $pdo->query("SHOW COLUMNS FROM movimentacoes LIKE 'observacao'");
            
            if ($check->rowCount() > 0) {
                echo '<div class="success">✓ O campo <code>observacao</code> já existe na tabela movimentacoes!</div>';
                echo '<div class="info">Nenhuma ação necessária. O sistema está pronto para uso.</div>';
            } else {
                echo '<div class="info">O campo <code>observacao</code> não foi encontrado. Adicionando...</div>';
                
                // Adicionar o campo observacao
                $sql = "ALTER TABLE movimentacoes ADD COLUMN observacao TEXT DEFAULT NULL AFTER id_usuario";
                $pdo->exec($sql);
                
                echo '<div class="success">✓ Campo <code>observacao</code> adicionado com sucesso!</div>';
                echo '<div class="success">✓ O banco de dados foi atualizado. Agora você pode aprovar e recusar solicitações.</div>';
            }
            
            // Verificar estrutura atual
            echo '<div class="info"><strong>Estrutura atual da tabela movimentacoes:</strong></div>';
            $columns = $pdo->query("SHOW COLUMNS FROM movimentacoes");
            echo '<table style="width: 100%; border-collapse: collapse; margin-top: 10px;">';
            echo '<tr style="background: #f3f4f6;"><th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Campo</th><th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Tipo</th><th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Nulo</th></tr>';
            while ($col = $columns->fetch(PDO::FETCH_ASSOC)) {
                $highlight = ($col['Field'] === 'observacao') ? 'background: #d1fae5;' : '';
                echo '<tr style="' . $highlight . '">';
                echo '<td style="padding: 8px; border: 1px solid #ddd;"><code>' . htmlspecialchars($col['Field']) . '</code></td>';
                echo '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($col['Type']) . '</td>';
                echo '<td style="padding: 8px; border: 1px solid #ddd;">' . ($col['Null'] === 'YES' ? 'Sim' : 'Não') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            
        } catch (PDOException $e) {
            echo '<div class="error">❌ Erro ao atualizar o banco de dados: ' . htmlspecialchars($e->getMessage()) . '</div>';
            echo '<div class="info">Tente executar manualmente no phpMyAdmin:</div>';
            echo '<pre style="background: #f3f4f6; padding: 15px; border-radius: 5px; overflow-x: auto;">';
            echo "ALTER TABLE movimentacoes ADD COLUMN observacao TEXT DEFAULT NULL AFTER id_usuario;";
            echo '</pre>';
        }
        ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p><strong>Próximos passos:</strong></p>
            <ol>
                <li>Teste aprovar uma solicitação</li>
                <li>Teste recusar uma solicitação</li>
                <li>Verifique se as observações estão sendo salvas</li>
            </ol>
        </div>
    </div>
</body>
</html>

