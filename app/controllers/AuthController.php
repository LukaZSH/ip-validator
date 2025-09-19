<?php

namespace app\controllers;

use App\Config\Database;
use app\SessionHelper;
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

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $startTime = microtime(true);

        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                
                // Log successful authentication
                $this->logAuthenticationAttempt($username, $clientIp, 'success', $userAgent, $startTime);
                
                header('Location: /admin');
                exit;
            } else {
                // Log failed authentication
                $this->logAuthenticationAttempt($username, $clientIp, 'failed', $userAgent, $startTime);
                
                SessionHelper::setFlashMessage('error', 'Usuário ou senha inválidos.');
                header('Location: /login');
                exit;
            }
        } catch (\Exception $e) {
            // Log authentication error
            $this->logAuthenticationAttempt($username, $clientIp, 'error', $userAgent, $startTime, $e->getMessage());
            
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
        
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userId = $_SESSION['user_id'] ?? 'unknown';
        
        // Log logout event
        $this->logUserActivity('logout', $clientIp, $userId, 'success');
        
        session_unset();
        session_destroy();
        header('Location: /login');
        exit;
    }

    /**
     * Log authentication attempts with structured data for monitoring
     */
    private function logAuthenticationAttempt($username, $clientIp, $status, $userAgent, $startTime, $errorMessage = null)
    {
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        $logData = [
            'event' => 'authentication_attempt',
            'username' => $username,
            'client_ip' => $clientIp,
            'status' => $status, // success, failed, error
            'user_agent' => substr($userAgent, 0, 200), // Limit user agent length
            'duration_ms' => $duration,
            'timestamp' => time(),
            'datetime' => date('c'), // ISO 8601 format
            'service' => 'ip-validator',
            'component' => 'auth'
        ];

        if ($errorMessage) {
            $logData['error_message'] = $errorMessage;
        }

        // Output structured log for Promtail to collect
        error_log(json_encode($logData));
    }

    /**
     * Log general user activities (logout, etc.)
     */
    private function logUserActivity($action, $clientIp, $userId, $status)
    {
        $logData = [
            'event' => 'user_activity',
            'action' => $action,
            'user_id' => $userId,
            'client_ip' => $clientIp,
            'status' => $status,
            'timestamp' => time(),
            'datetime' => date('c'),
            'service' => 'ip-validator',
            'component' => 'auth'
        ];

        error_log(json_encode($logData));
    }
}
