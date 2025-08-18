<?php
// A variável $event é fornecida pelo AdminController.php
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Evento</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 40px; color: #333; background-image: url('https://i.postimg.cc/HL8yYDwG/bg-unespar-page.png'); background-size: cover; background-position: center; background-attachment: fixed; min-height: 100vh; }
        .container { max-width: 800px; margin: auto; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); background-color: rgba(255, 255, 255, 0.95); }
        h1 { color: #5a5a5a; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="datetime-local"], textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 150px; }
        .btn { display: inline-block; padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; }
        .btn-secondary { background-color: #6c757d; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Evento: <?= htmlspecialchars($event['name']) ?></h1>

        <form action="/admin/events/update" method="POST">
            <!-- Campo oculto para enviar o ID do evento -->
            <input type="hidden" name="id" value="<?= $event['id'] ?>">

            <div class="form-group">
                <label for="name">Nome do Evento</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($event['name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="slug">URL (Slug)</label>
                <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($event['slug']) ?>" required>
            </div>
            <div class="form-group">
                <label for="start_time">Início do Evento</label>
                <input type="datetime-local" id="start_time" name="start_time" value="<?= date('Y-m-d\TH:i', strtotime($event['start_time'])) ?>" required>
            </div>
            <div class="form-group">
                <label for="end_time">Fim do Evento</label>
                <input type="datetime-local" id="end_time" name="end_time" value="<?= date('Y-m-d\TH:i', strtotime($event['end_time'])) ?>" required>
            </div>
            <div class="form-group">
                <label for="iframe_code">Código do Iframe (Opcional)</label>
                <textarea id="iframe_code" name="iframe_code"><?= htmlspecialchars($event['iframe_code'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn">Atualizar Evento</button>
            <a href="/admin" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>