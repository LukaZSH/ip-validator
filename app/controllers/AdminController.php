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

    public function editEventForm()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /admin');
            exit;
        }

        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM events WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                die("Evento não encontrado.");
            }

            // Passa a variável $event para a view de edição
            require_once __DIR__ . '/../views/admin/edit.php';

        } catch (\Exception $e) {
            die("Erro ao carregar evento para edição: " . $e->getMessage());
        }
    }

    public function updateEvent()
    {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $slug = $_POST['slug'];
        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];
        $iframeCode = !empty($_POST['iframe_code']) ? $_POST['iframe_code'] : null;

        // Atualiza o status com base na presença do iframe
        $status = ($iframeCode === null) ? 'Pendente' : 'Programado';

        try {
            $db = Database::getInstance()->getConnection();

            $sql = "UPDATE events SET 
                        name = :name, 
                        slug = :slug, 
                        start_time = :start_time, 
                        end_time = :end_time, 
                        iframe_code = :iframe_code, 
                        status = :status 
                    WHERE id = :id";

            $stmt = $db->prepare($sql);

            $stmt->execute([
                ':name' => $name,
                ':slug' => $slug,
                ':start_time' => $startTime,
                ':end_time' => $endTime,
                ':iframe_code' => $iframeCode,
                ':status' => $status,
                ':id' => $id
            ]);

            // Redireciona de volta para o dashboard
            header('Location: /admin');
            exit;

        } catch (\Exception $e) {
            die("Erro ao atualizar o evento: " . $e->getMessage());
        }
    }
}