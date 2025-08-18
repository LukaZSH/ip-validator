<?php
// A variável $event é fornecida pelo HomeController.php
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Presença - <?= htmlspecialchars($event['name']) ?></title>
    <style>
        body, html { margin: 0; padding: 0; height: 100%; font-family: sans-serif; }
        .event-container { display: flex; flex-direction: column; height: 100%; }
        .event-header { padding: 15px; background-color: #003366; color: white; text-align: center; }
        .event-header h1 { margin: 0; font-size: 1.5em; }
        .iframe-wrapper { flex-grow: 1; border: 0; }
        iframe { width: 100%; height: 100%; border: 0; }
    </style>
</head>
<body>
    <div class="event-container">
        <div class="event-header">
            <h1><?= htmlspecialchars($event['name']) ?></h1>
        </div>
        <div class="iframe-wrapper">
            <?= $event['iframe_code'] ?>
        </div>
    </div>
</body>
</html>
