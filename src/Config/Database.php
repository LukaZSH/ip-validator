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

        } catch (PDOException $e) {
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
}