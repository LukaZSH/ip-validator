<?php
session_start();

// --- ATENÇÃO: Troque este usuário e senha por algo seguro! ---
$valid_username = 'root';
$valid_password = 'Asenhaehroot!';

// Verifica se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Verifica se as credenciais são válidas
    if ($username === $valid_username && $password === $valid_password) {
        // Credenciais corretas, cria a sessão
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;

        // Redireciona para a página de administração
        header('Location: /admin');
        exit;
    } else {
        // Credenciais incorretas, redireciona de volta para o login com um erro
        header('Location: /login?error=1');
        exit;
    }
}

// Se alguém tentar acessar auth.php diretamente, redireciona para o login
header('Location: /ip-validator/login');
exit;