<?php

namespace app\controllers;

use App\Config\Database;
use PDO;

class AdminController
{ 
    // Busca todos os eventos no banco de dados e carrega a view do dashboard
    public function dashboard()
    {
        try {
            $db = Database::getInstance()->getConnection();

            // Busca todos os eventos, ordenando pelos mais recentes primeiro
            $stmt = $db->query("SELECT * FROM events ORDER BY event_date DESC");
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Passa a variável $events para a view
            require_once __DIR__ . '/../views/admin/dashboard.php';

        } catch (\Exception $e) {
            // Em caso de erro, exibe uma mensagem simples
            die("Erro ao carregar o dashboard: " . $e->getMessage());
        }
    }
}