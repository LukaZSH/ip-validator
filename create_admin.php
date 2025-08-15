<?php
// -----------------------------------------------------------------------------
// ATENÇÃO: ESTE É UM SCRIPT TEMPORÁRIO.
// ACESSE-O UMA VEZ PELO NAVEGADOR E DEPOIS APAGUE-O IMEDIATAMENTE DO PROJETO.
// -----------------------------------------------------------------------------
require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Database;

$db = Database::getInstance()->getConnection();

$username = 'suporte';
$password = '1n&$p@r';

// Gera um hash seguro da senha
$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // 1. Cria a tabela 'users' se ela ainda não existir
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );");
    echo "<p>Tabela 'users' verificada/criada com sucesso.</p>";

    // 2. Insere o usuário administrador
    $stmt = $db->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)");
    $stmt->execute([
        'username' => $username,
        'password_hash' => $password_hash
    ]);
    echo "<p>Usuário '{$username}' criado com sucesso!</p>";
    echo "<h3>LEMBRE-SE DE APAGAR O ARQUIVO 'create_admin.php' AGORA!</h3>";

} catch (\Exception $e) {
    // Trata o erro caso o usuário já exista (evita duplicatas)
    if ($e->getCode() == 23000) {
        echo "<p>Erro: O usuário '{$username}' já existe no banco de dados.</p>";
    } else {
        echo "<p>Erro ao executar o script: " . $e->getMessage() . "</p>";
    }
}