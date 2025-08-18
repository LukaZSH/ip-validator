<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Novo Evento</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 40px; color: #333; background-image: url('https://i.postimg.cc/HL8yYDwG/bg-unespar-page.png'); background-size: cover; background-position: center; background-attachment: fixed; min-height: 100vh; }
        .container { max-width: 800px; margin: auto; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); background-color: rgba(255, 255, 255, 0.95); }
        h1 { color: #5a5a5a; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="datetime-local"], textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 150px; }
        .btn { display: inline-block; padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; }
        .btn-secondary { background-color: #6c757d; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Adicionar Novo Evento</h1>
        <form action="/admin/events/store" method="POST">
            <div class="form-group">
                <label for="name">Nome do Evento</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="slug">URL (Slug)</label>
                <input type="text" id="slug" name="slug" required>
            </div>
            <div class="form-group">
                <label for="start_time">Início do Evento</label>
                <input type="datetime-local" id="start_time" name="start_time" required>
            </div>
            <div class="form-group">
                <label for="end_time">Fim do Evento</label>
                <input type="datetime-local" id="end_time" name="end_time" required>
            </div>
            <div class="form-group">
                <label for="iframe_code">Código do Iframe (Opcional)</label>
                <textarea id="iframe_code" name="iframe_code"></textarea>
            </div>
            <button type="submit" class="btn">Salvar Evento</button>
            <a href="/admin" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>