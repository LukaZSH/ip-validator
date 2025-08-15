<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Database;

// Apenas permite que este script seja acessado via método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /login.html');
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

try {
    $db = Database::getInstance()->getConnection();

    // Usa Prepared Statements para prevenir SQL Injection
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o usuário foi encontrado
    // Compara a senha enviada com o hash salvo no banco usando a função segura password_verify
    if ($user && password_verify($password, $user['password_hash'])) {
        // Login bem-sucedido!
        
        // Regenera o ID da sessão para prevenir ataques de Session Fixation
        session_regenerate_id(true); 
        
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        header('Location: /admin.php');
        exit;
    } else {
        // Credenciais inválidas (usuário ou senha errados)
        header('Location: /login.html?error=1');
        exit;
    }
} catch (PDOException $e) {
    // Em caso de erro no banco de dados, redireciona com uma mensagem genérica
    error_log("Erro de autenticação: " . $e->getMessage());
    header('Location: /login.html?error=2');
    exit;
}