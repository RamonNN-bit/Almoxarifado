<?php
/**
 * Script para atualizar a estrutura da tabela movimentacoes
 * Adiciona campos de timestamp, status e observacao
 */

require_once 'db.php';

try {
    echo "Iniciando atualização da tabela movimentacoes...\n\n";
    
    // Verificar se os campos já existem
    $check_timestamp = $pdo->query("SHOW COLUMNS FROM movimentacoes LIKE 'timestamp'");
    $check_status = $pdo->query("SHOW COLUMNS FROM movimentacoes LIKE 'status'");
    $check_observacao = $pdo->query("SHOW COLUMNS FROM movimentacoes LIKE 'observacao'");
    
    // Adicionar campo timestamp se não existir
    if ($check_timestamp->rowCount() == 0) {
        echo "Adicionando campo timestamp...\n";
        $pdo->exec("ALTER TABLE movimentacoes ADD COLUMN timestamp DATETIME DEFAULT CURRENT_TIMESTAMP AFTER data");
        echo "✓ Campo timestamp adicionado com sucesso!\n\n";
    } else {
        echo "✓ Campo timestamp já existe.\n";
    }
    
    // Adicionar campo status se não existir
    if ($check_status->rowCount() == 0) {
        echo "Adicionando campo status...\n";
        $pdo->exec("ALTER TABLE movimentacoes ADD COLUMN status ENUM('em espera', 'aprovado', 'recusado') DEFAULT 'em espera' AFTER timestamp");
        echo "✓ Campo status adicionado com sucesso!\n\n";
    } else {
        echo "✓ Campo status já existe.\n";
    }
    
    // Adicionar campo observacao se não existir
    if ($check_observacao->rowCount() == 0) {
        echo "Adicionando campo observacao...\n";
        $pdo->exec("ALTER TABLE movimentacoes ADD COLUMN observacao TEXT DEFAULT NULL AFTER id_usuario");
        echo "✓ Campo observacao adicionado com sucesso!\n\n";
    } else {
        echo "✓ Campo observacao já existe.\n";
    }
    
    // Atualizar registros existentes para ter timestamp baseado na data
    if ($check_timestamp->rowCount() == 0) {
        echo "Atualizando registros existentes com timestamp...\n";
        $pdo->exec("UPDATE movimentacoes SET timestamp = CONCAT(data, ' 00:00:00') WHERE timestamp IS NULL");
        echo "✓ Registros atualizados com sucesso!\n\n";
    }
    
    // Verificar e atualizar campo unidade na tabela itens (de INT para VARCHAR)
    echo "Verificando campo unidade na tabela itens...\n";
    $check_unidade = $pdo->query("SHOW COLUMNS FROM itens WHERE Field = 'unidade'");
    if ($check_unidade->rowCount() > 0) {
        $unidade_info = $check_unidade->fetch(PDO::FETCH_ASSOC);
        $tipo_unidade = strtolower($unidade_info['Type']);
        
        if (strpos($tipo_unidade, 'int') !== false || strpos($tipo_unidade, 'tinyint') !== false) {
            echo "Convertendo campo unidade de INT para VARCHAR...\n";
            try {
                $pdo->beginTransaction();
                
                // Verificar se já existe coluna temporária
                $check_temp = $pdo->query("SHOW COLUMNS FROM itens WHERE Field = 'unidade_temp'");
                if ($check_temp->rowCount() == 0) {
                    // Criar uma coluna temporária
                    $pdo->exec("ALTER TABLE itens ADD COLUMN unidade_temp VARCHAR(50) DEFAULT NULL");
                    
                    // Copiar dados - se já for texto, manter; se for número, tentar converter
                    $pdo->exec("UPDATE itens SET unidade_temp = CASE 
                        WHEN unidade IS NULL THEN NULL
                        WHEN unidade = 1 THEN 'unidades'
                        WHEN unidade = 2 THEN 'kg'
                        WHEN unidade = 3 THEN 'g'
                        WHEN unidade = 4 THEN 'litros'
                        WHEN unidade = 5 THEN 'ml'
                        WHEN unidade = 6 THEN 'metros'
                        WHEN unidade = 7 THEN 'cm'
                        WHEN unidade = 8 THEN 'mm'
                        WHEN unidade = 9 THEN 'caixas'
                        WHEN unidade = 10 THEN 'pacotes'
                        WHEN unidade = 11 THEN 'frascos'
                        WHEN unidade = 12 THEN 'tubos'
                        WHEN unidade = 13 THEN 'pares'
                        WHEN unidade = 14 THEN 'conjuntos'
                        ELSE CAST(unidade AS CHAR)
                    END");
                    
                    // Remover coluna antiga
                    $pdo->exec("ALTER TABLE itens DROP COLUMN unidade");
                    
                    // Renomear coluna temporária
                    $pdo->exec("ALTER TABLE itens CHANGE COLUMN unidade_temp unidade VARCHAR(50) DEFAULT NULL");
                    
                    $pdo->commit();
                    echo "✓ Campo unidade convertido com sucesso!\n\n";
                } else {
                    $pdo->rollBack();
                    echo "⚠ Coluna temporária já existe. Pulando conversão.\n";
                }
            } catch (Exception $e) {
                $pdo->rollBack();
                echo "⚠ Erro ao converter campo unidade: " . $e->getMessage() . "\n";
                echo "   Você pode converter manualmente se necessário.\n\n";
            }
        } else {
            echo "✓ Campo unidade já está como VARCHAR ou outro tipo texto.\n";
        }
    } else {
        echo "⚠ Campo unidade não encontrado na tabela itens.\n";
    }
    
    echo "\n========================================\n";
    echo "Atualização concluída com sucesso!\n";
    echo "========================================\n";
    echo "Campos disponíveis na tabela movimentacoes:\n";
    echo "- timestamp (DATETIME)\n";
    echo "- status (ENUM: 'em espera', 'aprovado', 'recusado')\n";
    echo "- observacao (TEXT)\n";
    echo "\nAgora as solicitações podem ser aprovadas/recusadas com observações!\n";
    
} catch (PDOException $e) {
    echo "\n❌ Erro ao atualizar a tabela: " . $e->getMessage() . "\n";
    echo "Detalhes: " . $e->getTraceAsString() . "\n";
    exit(1);
}
?>


