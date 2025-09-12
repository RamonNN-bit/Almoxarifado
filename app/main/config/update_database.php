<?php
/**
 * Script para atualizar a estrutura da tabela movimentacoes
 * Adiciona campos de timestamp e status
 */

require_once 'db.php';

try {
    echo "Iniciando atualização da tabela movimentacoes...\n";
    
    // Verificar se os campos já existem
    $check_timestamp = $pdo->query("SHOW COLUMNS FROM movimentacoes LIKE 'timestamp'");
    $check_status = $pdo->query("SHOW COLUMNS FROM movimentacoes LIKE 'status'");
    
    if ($check_timestamp->rowCount() == 0) {
        echo "Adicionando campo timestamp...\n";
        $pdo->exec("ALTER TABLE movimentacoes ADD COLUMN timestamp DATETIME DEFAULT CURRENT_TIMESTAMP AFTER data");
        echo "Campo timestamp adicionado com sucesso!\n";
    } else {
        echo "Campo timestamp já existe.\n";
    }
    
    if ($check_status->rowCount() == 0) {
        echo "Adicionando campo status...\n";
        $pdo->exec("ALTER TABLE movimentacoes ADD COLUMN status ENUM('em espera', 'aprovado', 'recusado') DEFAULT 'em espera' AFTER timestamp");
        echo "Campo status adicionado com sucesso!\n";
    } else {
        echo "Campo status já existe.\n";
    }
    
    // Atualizar registros existentes para ter timestamp baseado na data
    echo "Atualizando registros existentes...\n";
    $pdo->exec("UPDATE movimentacoes SET timestamp = CONCAT(data, ' 00:00:00') WHERE timestamp IS NULL");
    echo "Registros atualizados com sucesso!\n";
    
    echo "\nAtualização concluída com sucesso!\n";
    echo "Agora as solicitações serão criadas com data e hora completas.\n";
    
} catch (PDOException $e) {
    echo "Erro ao atualizar a tabela: " . $e->getMessage() . "\n";
    exit(1);
}
?>
