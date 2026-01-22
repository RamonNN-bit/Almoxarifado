-- Script de atualização do banco de dados
-- Execute este script no phpMyAdmin ou via linha de comando MySQL
-- para adicionar os campos faltantes

-- IMPORTANTE: Execute o script update_database.php via navegador ou linha de comando PHP
-- Ele verifica se os campos existem antes de adicionar

-- OU execute manualmente os comandos abaixo apenas se o campo não existir:

-- 1. Adicionar campo observacao na tabela movimentacoes
-- Verifique primeiro se existe: SHOW COLUMNS FROM movimentacoes LIKE 'observacao';
-- Se não existir, execute:
ALTER TABLE `movimentacoes` 
ADD COLUMN `observacao` TEXT DEFAULT NULL AFTER `id_usuario`;

-- Verificar e atualizar campo unidade na tabela itens
-- Se o campo unidade for INT, vamos convertê-lo para VARCHAR
-- Nota: MySQL não suporta IF NOT EXISTS para ALTER TABLE, então execute manualmente se necessário

-- Para verificar se precisa converter unidade, execute primeiro:
-- SHOW COLUMNS FROM itens WHERE Field = 'unidade';

-- Se o tipo for int, execute os comandos abaixo:
-- ALTER TABLE itens ADD COLUMN unidade_temp VARCHAR(50) DEFAULT NULL;
-- UPDATE itens SET unidade_temp = CASE 
--     WHEN unidade = 1 THEN 'unidades'
--     WHEN unidade = 2 THEN 'kg'
--     WHEN unidade = 3 THEN 'g'
--     WHEN unidade = 4 THEN 'litros'
--     WHEN unidade = 5 THEN 'ml'
--     WHEN unidade = 6 THEN 'metros'
--     WHEN unidade = 7 THEN 'cm'
--     WHEN unidade = 8 THEN 'mm'
--     WHEN unidade = 9 THEN 'caixas'
--     WHEN unidade = 10 THEN 'pacotes'
--     WHEN unidade = 11 THEN 'frascos'
--     WHEN unidade = 12 THEN 'tubos'
--     WHEN unidade = 13 THEN 'pares'
--     WHEN unidade = 14 THEN 'conjuntos'
--     ELSE CAST(unidade AS CHAR)
-- END;
-- ALTER TABLE itens DROP COLUMN unidade;
-- ALTER TABLE itens CHANGE COLUMN unidade_temp unidade VARCHAR(50) DEFAULT NULL;

-- Verificar estrutura final
-- DESCRIBE movimentacoes;
-- DESCRIBE itens;

