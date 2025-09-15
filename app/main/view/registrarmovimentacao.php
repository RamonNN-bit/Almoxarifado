<?php
require_once __DIR__ . '/../config/db.php';

session_start();
if (!isset($_SESSION ["usuariologado"])) {
header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Movimentação de Estoque</title>
  <style>
    body { font-family: Arial;
         max-width: 600px; 
         margin: 20px auto; 
         padding: 20px; 
         background: #e0e0e0 }
    h1 { text-align: center; }
    .form-container { background: #fff;
         padding: 15px;
          border-radius: 8px;
           box-shadow: 0 0 10px #0001; }
    .form-group { margin-bottom: 10px; }
    label { display: block;
        margin-bottom: 5px;
         font-weight: bold; }
    input, select, button {
      width: 100%;
       padding: 8px; 
       border-radius: 4px; 
       border: 1px solid #bbb;
        box-sizing: border-box;
    }
    button { background: #0057d8;
         color: #fff;
          border: none; 
          font-size: 16px;
           cursor: pointer; }
    button:hover { background: #003087; }
     { display: none; 
        margin-top: 10px;
         padding: 8px;
          border-radius: 4px; }
    .sucesso { background: #d4edda;
         color: #155724; }
    .erro { background: #f8d7da;
         color: #721c24; }
  </style>
</head>
<body>
  <h1>Movimentação de Estoque</h1>
  <div class="form-container">
    <form id="form-mov">
      <div class="form-group">
        <label>Tipo:</label>
        <select id="tipo" required>
          <option value="">Selecione</option>
          <option value="entrada">Entrada</option>
          <option value="saida">Saída</option>
        </select>
      </div>
      <div class="form-group">
        <label>Quantidade:</label>
        <input type="number" id="quantidade" min="1" required>
      </div>
      <div class="form-group">
        <label>Data:</label>
        <input type="date" id="data" required>
      </div>
      <button type="submit">Registrar</button>
    </form>
    <div id="mensagem"></div>
  </div>

  <script>
    document.getElementById('form-mov').addEventListener('submit', async e => {
      e.preventDefault();
      const tipo = tipo.value, qtd = quantidade.value, dataVal = data.value, msg = mensagem;
      if (!tipo || !qtd || !dataVal || qtd <= 0) {
        msg.textContent = 'Preencha os campos corretamente.';
        msg.className = 'erro';
        msg.style.display = 'block';
        return;
      }
      try {
        const res = await fetch('/movimentacao/registrar', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ tipo, quantidade: qtd, data: dataVal })
        });
        const txt = await res.text();
        msg.textContent = txt;
        msg.className = res.ok ? 'sucesso' : 'erro';
        msg.style.display = 'block';
        if (res.ok) form.reset();
      } catch {
        msg.textContent = 'Erro ao conectar com o servidor!';
        msg.className = 'erro';
        msg.style.display = 'block';
      }
    });
  </script>
    <!-- Footer -->
    <footer class="mt-12 py-6 w-full">
        <div class="flex flex-col sm:flex-row items-center justify-center text-sm text-gray-500 gap-2">
            <div class="text-center">
                Copyright &copy; Sistema de Almoxarifado 2025
            </div>
            <span class="hidden sm:inline mx-2">&middot;</span>
            <div class="text-center">
                Desenvolvido por: Fulano, Beltrano e Ciclano
            </div>
            <span class="hidden sm:inline mx-2">&middot;</span>
            <div class="mt-2 sm:mt-0">
                <a href="#" class="hover:text-green-primary">Política de Privacidade</a>
                <span class="mx-2">&middot;</span>
                <a href="#" class="hover:text-green-primary">Termos de Uso</a>
            </div>
        </div>
    </footer>
</body>
</html>
?>
