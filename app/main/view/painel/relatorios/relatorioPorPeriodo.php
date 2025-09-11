<?php
require_once '../../../config/db.php';

// Se o usuário solicitou o PDF
if (isset($_GET['acao']) && $_GET['acao'] === 'pdf') {
    $dataInicio = isset($_GET['inicio']) ? $_GET['inicio'] : '';
    $dataFim = isset($_GET['fim']) ? $_GET['fim'] : '';

    if (!$dataInicio || !$dataFim) {
        die('Período inválido. Informe datas de início e fim.');
    }

    // Sanitiza datas para o formato Y-m-d
    $dtIni = date('Y-m-d', strtotime($dataInicio));
    $dtFim = date('Y-m-d', strtotime($dataFim));

    // Ajuste de nomes de campos conforme SQL (banco.sql define `data` em movimentacoes)
    $sql = "SELECT m.id, m.id_item, m.tipo, m.quantidade, m.data AS data_movimentacao, i.nome AS nome_item
            FROM movimentacoes m
            LEFT JOIN itens i ON i.id = m.id_item
            WHERE m.data BETWEEN ? AND ?
            ORDER BY m.data ASC, m.id ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$dtIni, $dtFim]);
    $movs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Carrega FPDF
    require_once '../../../assets/vendor/fpdf/fpdf.php';

    class RelatorioMovimentacaoPDF extends FPDF {
        function Header() {
            // Barra superior com cor do sistema (#059669)
            $this->SetFillColor(5, 150, 105);
            $this->Rect(0, 0, 210, 20, 'F');

            // Logo (se existir)
            $logoPath = '../../../assets/images/logo.png';
            if (file_exists($logoPath)) {
                $this->Image($logoPath, 5, 6, 40);
            }

            // Título
            $this->SetY(5);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', 14);
            $this->Cell(0, 10, utf8_decode('Relatório de Movimentações de Estoque'), 0, 1, 'C');

            // Linha fina abaixo do header
            $this->SetDrawColor(230, 230, 230);
            $this->Line(10, 22, 200, 22);
            $this->Ln(6);
            $this->SetTextColor(0, 0, 0);
        }
        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo().'/{nb}', 0, 0, 'C');
        }
        function Tabela($dados, $periodoTexto) {
            $this->SetFont('Arial', '', 10);
            $this->Cell(0, 16, utf8_decode('Período: ') . $periodoTexto, 0, 1, 'L');
            $this->Ln(2);

            // Cabeçalho com cor do sistema
            $this->SetFillColor(5, 150, 105);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(20, 8, utf8_decode('ID'), 1, 0, 'C', true);
            $this->Cell(90, 8, utf8_decode('Item'), 1, 0, 'L', true);
            $this->Cell(25, 8, utf8_decode('Tipo'), 1, 0, 'C', true);
            $this->Cell(25, 8, utf8_decode('Quantidade'), 1, 0, 'C', true);
            $this->Cell(30, 8, utf8_decode('Data'), 1, 1, 'C', true);

            // Linhas alternadas
            $this->SetFont('Arial', '', 10);
            $this->SetTextColor(0, 0, 0);
            $fill = false;

            $totalEntradas = 0;
            $totalSaidas = 0;

            foreach ($dados as $linha) {
                $id = $linha['id'];
                $item = $linha['nome_item'] ?: ('Item #' . $linha['id_item']);
                $tipo = strtoupper($linha['tipo']);
                $qtd = (int)$linha['quantidade'];
                $data = date('d/m/Y', strtotime($linha['data_movimentacao']));

                if ($linha['tipo'] === 'entrada') { $totalEntradas += $qtd; }
                if ($linha['tipo'] === 'saida') { $totalSaidas += $qtd; }

                $this->SetFillColor($fill ? 245 : 255, $fill ? 250 : 255, $fill ? 247 : 255); // alterna branco e cinza muito claro
                $this->Cell(20, 8, $id, 1, 0, 'C', true);
                $this->Cell(90, 8, utf8_decode($item), 1, 0, 'L', true);
                $this->Cell(25, 8, utf8_decode($tipo), 1, 0, 'C', true);
                $this->Cell(25, 8, $qtd, 1, 0, 'C', true);
                $this->Cell(30, 8, $data, 1, 1, 'C', true);
                $fill = !$fill;
            }

            // Totais com borda destaque
            $this->Ln(4);
            $this->SetFont('Arial', 'B', 10);
            $this->SetDrawColor(5, 150, 105);
            $this->SetTextColor(5, 150, 105);
            $this->Cell(0, 8, utf8_decode('Totais no período'), 0, 1, 'L');
            $this->SetTextColor(0, 0, 0);
            $this->SetFont('Arial', '', 10);
            $this->Cell(50, 8, utf8_decode('Total de Entradas:'), 1, 0, 'L');
            $this->Cell(30, 8, (string)$totalEntradas, 1, 0, 'C');
            $this->Cell(50, 8, utf8_decode('Total de Saídas:'), 1, 0, 'L');
            $this->Cell(30, 8, (string)$totalSaidas, 1, 0, 'C');
            $this->Cell(16, 8, utf8_decode('Saldo:'), 1, 0, 'L');
            $this->Cell(0, 8, (string)($totalEntradas - $totalSaidas), 1, 1, 'C');
        }
    }

    $periodoTexto = date('d/m/Y', strtotime($dtIni)) . ' a ' . date('d/m/Y', strtotime($dtFim));

    $pdf = new RelatorioMovimentacaoPDF('P', 'mm', 'A4');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->Tabela($movs, $periodoTexto);

    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="relatorio_movimentacoes_'.$dtIni.'_a_'.$dtFim.'.pdf"');
    $pdf->Output('I');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório por Período</title>
    <link rel="stylesheet" href="/app/main/assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Relatório de Entradas e Saídas por Período</h2>
        <form method="get">
            <input type="hidden" name="acao" value="pdf">
            <label>Data início:
                <input type="date" name="inicio" required>
            </label>
            <label>Data fim:
                <input type="date" name="fim" required>
            </label>
            <button type="submit">Gerar PDF</button>
        </form>
    </div>
</body>
</html>


