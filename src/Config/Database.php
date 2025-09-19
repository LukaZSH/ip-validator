<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $conn;

    private function __construct()
    {
        // Pega as credenciais das variáveis de ambiente definidas no docker-compose
        $host = getenv('DB_HOST');
        $db_name = getenv('DB_NAME');
        $username = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');

        try {
            // Cria a conexão PDO
            $this->conn = new PDO("mysql:host={$host};dbname={$db_name}", $username, $password);
            // Configura o PDO para lançar exceções em caso de erro, facilitando o debug
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Força a sessão do MySQL a usar o fuso horário correto.
            $this->conn->exec("SET time_zone='America/Sao_Paulo';");

            // Log successful database connection
            self::logConnectionAttempt('success');

        } catch (PDOException $e) {
            // Log failed database connection
            self::logConnectionAttempt('failed', $e->getMessage());
            
            // Em um ambiente de produção, o ideal seria logar este erro em um arquivo
            die("Erro de Conexão com o Banco de Dados: " . $e->getMessage());
        }
    }

    // Padrão Singleton: garante que teremos apenas uma instância da conexão por requisição
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * Log database operations with structured data for monitoring
     */
    public static function logDatabaseOperation($operation, $startTime, $status, $errorMessage = null, $affectedRows = null)
    {
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        $logData = [
            'event' => 'db_operation',
            'operation' => $operation, // select, insert, update, delete, connection
            'duration_ms' => $duration,
            'status' => $status, // success, failed, error
            'timestamp' => time(),
            'datetime' => date('c'),
            'service' => 'ip-validator',
            'component' => 'database'
        ];

        if ($affectedRows !== null) {
            $logData['affected_rows'] = $affectedRows;
        }

        if ($errorMessage) {
            $logData['error_message'] = $errorMessage;
        }

        // Output structured log for Promtail to collect
        error_log(json_encode($logData));
    }

    /**
     * Log database connection events
     */
    public static function logConnectionAttempt($status, $errorMessage = null)
    {
        $logData = [
            'event' => 'db_connection',
            'status' => $status, // success, failed
            'timestamp' => time(),
            'datetime' => date('c'),
            'service' => 'ip-validator',
            'component' => 'database'
        ];

        if ($errorMessage) {
            $logData['error_message'] = $errorMessage;
        }

        error_log(json_encode($logData));
    }
}