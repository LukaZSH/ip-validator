<?php
$error = \app\SessionHelper::getFlashMessage('error') ?? null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin</title>
    <style>
        html { background-image: url(https://i.postimg.cc/HL8yYDwG/bgw-unespar-page.png); background-size: cover; }
        body { height: 100vh; font-family: 'Arial', sans-serif; display: flex; justify-content: center; align-items: center; }
        .central-container { background-color: white; border-radius: 30px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); width: 100%; max-width: 400px; padding: 40px 20px; text-align: center; margin: 20px; }
        img { width: 121px; margin-bottom: 20px; }
        h1 { font-size: 24px; color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; text-align: left; }
        label { display: block; margin-bottom: 5px; color: #555; font-weight: bold; }
        input[type="text"], input[type="password"] { width: 95%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; font-size: 16px; }
        button { width: 100%; padding: 12px; background-color: #007BFF; color: white; border: none; border-radius: 20px; cursor: pointer; font-size: 16px; transition: background-color 0.3s ease; }
        button:hover { background-color: #0056b3; }
        .error-message { color: red; margin-top: 10px; min-height: 1.2em; }
    </style>
</head>
<body>
    <div class="central-container">
        <img src="/Logo/logo-unespar.jpeg" alt="Logo UNESPAR Apucarana">
        <h1>Acesso Administrativo</h1>
        <form action="/login" method="post">
            <div class="form-group">
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Entrar</button>
            <p class="error-message">
                <?php if ($error): ?>
                    <?= htmlspecialchars($error) ?>
                <?php endif; ?>
            </p>
        </form>
    </div>
</body>
</html>