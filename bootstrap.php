#!/usr/bin/env php
<?php
// bootstrap.php
// ATENÇÃO: ESTE É UM SCRIPT TEMPORÁRIO PARA INICIALIZAÇÃO.
// EXECUTE-O UMA VEZ E DEPOIS APAGUE-O DO SERVIDOR.

require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Database;

if (php_sapi_name() !== 'cli') {
    die("Acesso negado. Este script é para uso exclusivo via CLI.");
}

try {
    echo "🚀 Iniciando a inicialização completa do banco de dados...\n";
    $db = Database::getInstance()->getConnection();

    // --- Tabela de Usuários ---
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );");
    echo "✅ Tabela 'users' verificada/criada com sucesso.\n";

    // --- Tabela de Eventos ---
    $db->exec("CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        iframe_code TEXT,
        start_time DATETIME NOT NULL,
        end_time DATETIME NOT NULL,
        status ENUM('Pendente', 'Programado', 'Concluído') NOT NULL DEFAULT 'Pendente',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );");
    echo "✅ Tabela 'events' verificada/criada com sucesso.\n";

    // --- Tabela de Presenças ---
    $db->exec("CREATE TABLE IF NOT EXISTS presences (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_ip VARCHAR(45) NOT NULL,
        registration_date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_ip_per_day (user_ip, registration_date)
    );");
    echo "✅ Tabela 'presences' verificada/criada com sucesso.\n";

    // --- Criação do Usuário Administrador ---
    $username = 'admin';
    $password = '1n&$p@r';

    $stmt_check = $db->prepare("SELECT id FROM users WHERE username = :username");
    $stmt_check->execute(['username' => $username]);

    if ($stmt_check->fetch()) {
        echo "ℹ️  Usuário '{$username}' já existe. Nenhuma ação necessária.\n";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt_insert = $db->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)");
        $stmt_insert->execute([
            'username' => $username,
            'password_hash' => $password_hash
        ]);
        echo "✅ Usuário '{$username}' criado com sucesso!\n";
    }

    echo "\n🎉 Inicialização concluída! Lembre-se de apagar este script do servidor.\n";

} catch (\Exception $e) {
    echo "[ERRO] Falha na inicialização: " . $e->getMessage() . "\n";
    exit(1);
}
