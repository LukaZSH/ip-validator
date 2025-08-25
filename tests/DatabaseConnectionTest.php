<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;

echo "Iniciar o teste de conexão com o banco de dados...\n";

try {
    $db = Database::getInstance()->getConnection();

    $stmt = $db->query("SELECT 1");

    if ($stmt->fetchColumn() == 1) {
        echo "SUCESSO: Conexão com o banco de dados estabelecida com sucesso!\n";
        exit(0);
    } else {
        echo "ERRO: A consulta de verificação falhou.\n";
        exit(1);
    }

} catch (\Exception $e) {
    echo "ERRO FATAL: Não foi possível conectar ao banco de dados.\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    exit(1);
}
