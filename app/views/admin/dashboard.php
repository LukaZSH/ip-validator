<?php
// A variável $events é fornecida pelo AdminController.php
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Administração - Eventos</title>
    <style>
        body { font-family: sans-serif; margin: 40px; background-color: #f4f4f9; color: #333; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #5a5a5a; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .btn { display: inline-block; padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-bottom: 20px; }
        .status { padding: 5px 10px; border-radius: 15px; color: white; font-weight: bold; font-size: 0.9em; }
        .status-pendente { background-color: #ffc107; }
        .status-programado { background-color: #28a745; }
        .status-concluido { background-color: #6c757d; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Dashboard de Eventos</h1>
        <a href="/admin/events/create" class="btn">Adicionar Novo Evento</a>

        <table>
            <thead>
                <tr>
                    <th>Nome do Evento</th>
                    <th>URL (Slug)</th>
                    <th>Início</th>
                    <th>Fim</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="6">Nenhum evento encontrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?= htmlspecialchars($event['name']) ?></td>
                            <td>/evento/<?= htmlspecialchars($event['slug']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($event['start_time'])) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($event['end_time'])) ?></td>
                            <td>
                                <span class="status status-<?= strtolower($event['status']) ?>">
                                    <?= htmlspecialchars($event['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="/admin/events/edit?id=<?= $event['id'] ?>">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>