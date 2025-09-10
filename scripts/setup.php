<?php
// Script para configurar o schema inicial do banco de dados.
// Só pode ser executado via linha de comando (CLI).

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;

if (php_sapi_name() !== 'cli') {
    die("Acesso negado. Este script é para uso exclusivo via CLI.");
}

try {
    echo "Iniciando setup do banco de dados...\n";
    $db = Database::getInstance()->getConnection();

    // Tabela de Eventos 
    $sqlEvents = "
    CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        iframe_code TEXT,
        start_time DATETIME NOT NULL,
        end_time DATETIME NOT NULL,
        status ENUM('Pendente', 'Programado', 'Concluído') NOT NULL DEFAULT 'Pendente',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );";

    $db->exec($sqlEvents);
    echo "Tabela 'events' verificada/criada com sucesso.\n";

    // Tabela de Presenças (Trava anti espertinhos)
    $sqlPresences = "
    CREATE TABLE IF NOT EXISTS presences (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_ip VARCHAR(45) NOT NULL,
        registration_date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_ip_per_day (user_ip, registration_date)
    );";

    $db->exec($sqlPresences);
    echo "Tabela 'presences' verificada/criada com sucesso.\n";

    $sqlUsers = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );";

    $db->exec($sqlUsers);
    echo "Tabela 'users' verificada/criada com sucesso.\n";

    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
    $stmt->execute(['username' => 'suporte']);
    $userExists = $stmt->fetchColumn();

    if ($userExists == 0) {
        $defaultPassword = password_hash('1n&$p@r', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->execute([
            'username' => 'suporte',
            'password' => $defaultPassword
        ]);
        echo "Usuário administrativo 'suporte' criado com sucesso.\n";
    } else {
        echo "Usuário administrativo 'suporte' já existe.\n";
    }

    echo "Setup do banco de dados concluído com sucesso!\n";

} catch (\Exception $e) {
    echo "[ERRO] Falha no setup do banco de dados: " . $e->getMessage() . "\n";
    exit(1);
}