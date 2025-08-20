<?php
use App\SessionHelper;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF--8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Administração - Eventos</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 40px; color: #333; background-image: url('https://i.postimg.cc/HL8yYDwG/bg-unespar-page.png'); background-size: cover; background-position: center; background-attachment: fixed; min-height: 100vh; }
        .container { max-width: 1200px; margin: auto; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); background-color: rgba(255, 255, 255, 0.95); }
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
        .actions-form button { color: red; background: none; border: none; padding: 0; font: inherit; cursor: pointer; text-decoration: underline; }
        .actions-links a, .actions-form button { margin: 0 5px; }
        .flash-message { padding: 15px; margin-bottom: 20px; border-radius: 5px; color: white; font-weight: bold; }
        .flash-success { background-color: #28a745; }
        .flash-error { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        
        <!-- Local para exibir as mensagens de flash -->
        <?php SessionHelper::displayFlashMessage(); ?>

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
                            <td class="actions-links">
                                <a href="/admin/events/qrcode?id=<?= $event['id'] ?>" target="_blank">QR Code</a> |
                                <a href="/admin/events/edit?id=<?= $event['id'] ?>">Editar</a> |
                                <form action="/admin/events/delete" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este evento? Esta ação não pode ser desfeita.');" class="actions-form">
                                    <input type="hidden" name="id" value="<?= $event['id'] ?>">
                                    <button type="submit">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
