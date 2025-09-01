<?php

namespace app\controllers;

use App\Config\Database;
use App\SessionHelper;
use PDO;

class AuthController
{
    public function showLoginForm()
    {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function login()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute(['username' => $_POST['username']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($_POST['password'], $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                header('Location: /admin');
                exit;
            } else {
                
                SessionHelper::setFlashMessage('error', 'Usuário ou senha inválidos.');
                header('Location: /login');
                exit;
            }
        } catch (
Exception $e) {
            
            SessionHelper::setFlashMessage('error', 'Ocorreu um erro no servidor. Tente novamente.');
            header('Location: /login');
            exit;
        }
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: /login');
        exit;
    }
}
