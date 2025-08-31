<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Itens</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WA1hnk4A7mNKw2Vwg3Xw3g+Yx/5Y/rtP9dud/sSABzIWvo0rbk2" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body{background:#eff6ff;font-family:'Segoe UI',sans-serif;min-height:100vh;display:flex;justify-content:center}
        .container{max-width:1100px;margin:20px auto;padding:20px;background:#fff;border-radius:15px;box-shadow:0 4px 15px rgba(0,0,0,0.1)}
        h1{color:#1e40af;font-weight:600;margin-bottom:20px;text-align:center;font-size:1.8rem}
        .btn-primary{background:#3b82f6;border-color:#3b82f6;border-radius:10px;padding:8px 16px;font-weight:500;transition:all .3s}
        .btn-primary:hover{background:#2563eb;border-color:#2563eb;transform:translateY(-2px)}
        .btn-warning{background:#f59e0b;border-color:#f59e0b;border-radius:8px;padding:5px 10px;transition:all .3s}
        .btn-warning:hover{background:#d97706;border-color:#d97706;transform:translateY(-2px)}
        .btn-danger{background:#dc2626;border-color:#dc2626;border-radius:8px;padding:5px 10px;transition:all .3s}
        .btn-danger:hover{background:#b91c1c;border-color:#b91c1c;transform:translateY(-2px)}
        .table{margin-top:15px;border-radius:12px;overflow:hidden;background:#eff6ff;box-shadow:0 2px 10px rgba(0,0,0,0.1)}
        .table th{background:#1e40af;color:#fff;font-weight:500;text-transform:uppercase;border:none}
        .table td,.table th{vertical-align:middle;text-align:center;padding:10px;border:none}
        .table tr{transition:background-color .2s}.table tr:hover{background:#dbeafe}
        .no-items{text-align:center;color:#1e40af;padding:20px;font-size:1.1rem;font-style:italic}
        .modal-content,.modal-header{border-radius:12px;border:none}
        .modal-header{background:#1e40af;color:#fff}
        .modal-footer{border:none}
        @media (max-width:768px){.container{margin:10px;padding:15px;border-radius:10px}h1{font-size:1.5rem}.btn{font-size:.85rem;padding:6px 12px}.table{font-size:.9rem}.table td,.table th{padding:8px}}
    </style>
</head>
<body>
    <div class="container">
        <h1>Cadastro de Itens</h1>
        <a href="?controller=ItensController&method=create" class="btn btn-primary mb-3"><i class="bi bi-plus-circle me-1"></i> Adicionar</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th><th>Nome</th><th>Quantidade</th><th>Unidade</th><th>Marca</th><th>Modelo</th><th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($itens)): ?>
                    <?php foreach ($itens as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($item['quantidade'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($item['unidade'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($item['marca'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($item['modelo'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <a href="?controller=ItensController&method=edit&id=<?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-warning btn-sm me-1"><i class="bi bi-pencil"></i></a>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-trash"></i></button>
                                <div class="modal fade" id="deleteModal<?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel<?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?>">Confirmar Exclusão</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Excluir <strong><?php echo htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8'); ?>?</strong>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <a href="?controller=ItensController&method=delete&id=<?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-danger">Excluir</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="no-items">Nenhum item encontrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>

