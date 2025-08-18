<?php

namespace app\controllers;

use App\Config\Database;
use PDO;

class AdminController
{
    //Busca todos os eventos no banco de dados e carrega a view do dashboard.
    public function dashboard()
    {
        try {
            $db = Database::getInstance()->getConnection();

            // Ordena por 'start_time' em vez de 'event_date'
            $stmt = $db->query("SELECT * FROM events ORDER BY start_time DESC");
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Passa a variável $events para a view
            require_once __DIR__ . '/../views/admin/dashboard.php';

        } catch (\Exception $e) {
            // Em caso de erro, exibe uma mensagem simples
            die("Erro ao carregar o dashboard: " . $e->getMessage());
        }
    }

    public function createEventForm()
    {
        require_once __DIR__ . '/../views/admin/create.php';
    }

    public function storeEvent()
    {
        $name = $_POST['name'];
        $slug = $_POST['slug'];
        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];
        $iframeCode = !empty($_POST['iframe_code']) ? $_POST['iframe_code'] : null;

        // Define o status com base na presença do iframe
        $status = ($iframeCode === null) ? 'Pendente' : 'Programado';

        try {
            $db = Database::getInstance()->getConnection();

            $sql = "INSERT INTO events (name, slug, start_time, end_time, iframe_code, status) 
                    VALUES (:name, :slug, :start_time, :end_time, :iframe_code, :status)";

            $stmt = $db->prepare($sql);

            // Associa os valores aos parâmetros da query para prevenir SQL Injection
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':slug', $slug);
            $stmt->bindParam(':start_time', $startTime);
            $stmt->bindParam(':end_time', $endTime);
            $stmt->bindParam(':iframe_code', $iframeCode);
            $stmt->bindParam(':status', $status);

            $stmt->execute();

            // Redireciona de volta para o dashboard após o sucesso
            header('Location: /admin');
            exit;

        } catch (\Exception $e) {
            die("Erro ao salvar o evento: " . $e->getMessage());
        }
    }
}