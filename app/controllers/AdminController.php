<?php

namespace app\controllers;

use App\Config\Database;
use app\SessionHelper;
use PDO;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;

class AdminController
{
    public function dashboard()
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT * FROM events ORDER BY start_time DESC");
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            require_once __DIR__ . '/../views/admin/dashboard.php';
        } catch (\Exception $e) {
            die("Erro ao carregar o dashboard: " . $e->getMessage());
        }
    }

    public function createEventForm()
    {
        require_once __DIR__ . '/../views/admin/create.php';
    }

    public function storeEvent()
    {
        // 1. Validação de Dados
        $name = $_POST['name'];
        $slug = $_POST['slug'];
        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];
        $iframeCode = !empty($_POST['iframe_code']) ? $_POST['iframe_code'] : null;

        if (empty($name) || empty($slug) || empty($startTime) || empty($endTime)) {
            SessionHelper::setFlashMessage('error', 'Todos os campos, exceto o iframe, são obrigatórios.');
            header('Location: /admin/events/create');
            exit;
        }

        if (strtotime($endTime) <= strtotime($startTime)) {
            SessionHelper::setFlashMessage('error', 'A data de fim deve ser posterior à data de início.');
            header('Location: /admin/events/create');
            exit;
        }

        $status = ($iframeCode === null) ? 'Pendente' : 'Programado';

        try {
            $db = Database::getInstance()->getConnection();
            
            // Verifica se o slug já existe
            $stmt = $db->prepare("SELECT id FROM events WHERE slug = :slug");
            $stmt->execute(['slug' => $slug]);
            if ($stmt->fetch()) {
                SessionHelper::setFlashMessage('error', 'Erro: A URL (Slug) já está em uso por outro evento.');
                header('Location: /admin/events/create');
                exit;
            }

            // 2. Inserção no Banco
            $sql = "INSERT INTO events (name, slug, start_time, end_time, iframe_code, status) 
                    VALUES (:name, :slug, :start_time, :end_time, :iframe_code, :status)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':slug' => $slug,
                ':start_time' => $startTime,
                ':end_time' => $endTime,
                ':iframe_code' => $iframeCode,
                ':status' => $status
            ]);

            // Log successful event creation
            $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userId = $_SESSION['user_id'] ?? 'unknown';
            $this->logAdminActivity('create', 'event', $db->lastInsertId(), 'success', $clientIp, $userId, "Event '{$name}' created");

            SessionHelper::setFlashMessage('success', "Evento '{$name}' criado com sucesso!");
            header('Location: /admin');
            exit;

        } catch (\Exception $e) {
            SessionHelper::setFlashMessage('error', 'Ocorreu um erro ao salvar o evento: ' . $e->getMessage());
            header('Location: /admin/events/create');
            exit;
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
            if (!$event) { die("Evento não encontrado."); }
            require_once __DIR__ . '/../views/admin/edit.php';
        } catch (\Exception $e) {
            die("Erro ao carregar evento para edição: " . $e->getMessage());
        }
    }

    public function updateEvent()
    {
        // 1. Validação de Dados
        $id = $_POST['id'];
        $name = $_POST['name'];
        $slug = $_POST['slug'];
        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];
        $iframeCode = !empty($_POST['iframe_code']) ? $_POST['iframe_code'] : null;

        if (empty($id) || empty($name) || empty($slug) || empty($startTime) || empty($endTime)) {
            SessionHelper::setFlashMessage('error', 'Todos os campos, exceto o iframe, são obrigatórios.');
            header("Location: /admin/events/edit?id={$id}");
            exit;
        }

        if (strtotime($endTime) <= strtotime($startTime)) {
            SessionHelper::setFlashMessage('error', 'A data de fim deve ser posterior à data de início.');
            header("Location: /admin/events/edit?id={$id}");
            exit;
        }

        $status = ($iframeCode === null) ? 'Pendente' : 'Programado';

        try {
            $db = Database::getInstance()->getConnection();
            
            // Verifica se o slug já existe em OUTRO evento
            $stmt = $db->prepare("SELECT id FROM events WHERE slug = :slug AND id != :id");
            $stmt->execute(['slug' => $slug, 'id' => $id]);
            if ($stmt->fetch()) {
                SessionHelper::setFlashMessage('error', 'Erro: A URL (Slug) já está em uso por outro evento.');
                header("Location: /admin/events/edit?id={$id}");
                exit;
            }

            // 2. Atualização no Banco
            $sql = "UPDATE events SET name = :name, slug = :slug, start_time = :start_time, end_time = :end_time, iframe_code = :iframe_code, status = :status WHERE id = :id";
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

            // Log successful event update
            $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userId = $_SESSION['user_id'] ?? 'unknown';
            $this->logAdminActivity('update', 'event', $id, 'success', $clientIp, $userId, "Event '{$name}' updated");

            SessionHelper::setFlashMessage('success', "Evento '{$name}' atualizado com sucesso!");
            header('Location: /admin');
            exit;

        } catch (\Exception $e) {
            SessionHelper::setFlashMessage('error', 'Ocorreu um erro ao atualizar o evento: ' . $e->getMessage());
            header("Location: /admin/events/edit?id={$id}");
            exit;
        }
    }

    public function deleteEvent()
    {
        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: /admin');
            exit;
        }
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM events WHERE id = :id");
            $stmt->execute(['id' => $id]);
            
            // Log successful event deletion
            $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userId = $_SESSION['user_id'] ?? 'unknown';
            $this->logAdminActivity('delete', 'event', $id, 'success', $clientIp, $userId, "Event ID {$id} deleted");
            
            SessionHelper::setFlashMessage('success', 'Evento excluído com sucesso!');
            header('Location: /admin');
            exit;
        } catch (\Exception $e) {
            SessionHelper::setFlashMessage('error', 'Erro ao excluir o evento.');
            header('Location: /admin');
            exit;
        }
    }

    public function generateQrCode()
    {
        $id = $_GET['id'] ?? null;
        $startTime = microtime(true);
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userId = $_SESSION['user_id'] ?? 'unknown';
        
        if (!$id) { 
            $this->logQRCodeGeneration(null, null, 'failed', $clientIp, $userId, $startTime, 'ID do evento não fornecido');
            die("ID do evento não fornecido."); 
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT slug FROM events WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$event) { 
                $this->logQRCodeGeneration($id, null, 'failed', $clientIp, $userId, $startTime, 'Evento não encontrado');
                die("Evento não encontrado."); 
            }
            
            $url = "http://192.168.3.2/evento/" . $event['slug'];
            $qrCode = QrCode::create($url)
                ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
                ->setSize(400)
                ->setMargin(10)
                ->setBackgroundColor(new Color(255, 255, 255));
            
            $logoPath = '/var/www/html/public/Logo/logo-unespar.jpeg';
            $logo = Logo::create($logoPath)->setResizeToWidth(100);
            $writer = new PngWriter();
            $result = $writer->write($qrCode, $logo);
            
            // Log successful QR code generation
            $this->logQRCodeGeneration($id, $event['slug'], 'success', $clientIp, $userId, $startTime);
            
            header('Content-Type: ' . $result->getMimeType());
            echo $result->getString();
            exit;
        } catch (\Exception $e) {
            // Log QR code generation error
            $this->logQRCodeGeneration($id, $event['slug'] ?? null, 'error', $clientIp, $userId, $startTime, $e->getMessage());
            die("Erro ao gerar o QR Code: " . $e->getMessage());
        }
    }

    /**
     * Log QR code generation activities with structured data for monitoring
     */
    private function logQRCodeGeneration($eventId, $eventSlug, $status, $clientIp, $userId, $startTime, $errorMessage = null)
    {
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        $logData = [
            'event' => 'qr_code_generation',
            'event_id' => $eventId,
            'event_slug' => $eventSlug,
            'status' => $status, // success, failed, error
            'user_id' => $userId,
            'client_ip' => $clientIp,
            'duration_ms' => $duration,
            'timestamp' => time(),
            'datetime' => date('c'),
            'service' => 'ip-validator',
            'component' => 'admin'
        ];

        if ($errorMessage) {
            $logData['error_message'] = $errorMessage;
        }

        // Output structured log for Promtail to collect
        error_log(json_encode($logData));
    }

    /**
     * Log general admin activities (CRUD operations, etc.)
     */
    private function logAdminActivity($action, $resourceType, $resourceId, $status, $clientIp, $userId, $details = null)
    {
        $logData = [
            'event' => 'admin_activity',
            'action' => $action, // create, read, update, delete
            'resource_type' => $resourceType, // event, user, etc.
            'resource_id' => $resourceId,
            'status' => $status,
            'user_id' => $userId,
            'client_ip' => $clientIp,
            'timestamp' => time(),
            'datetime' => date('c'),
            'service' => 'ip-validator',
            'component' => 'admin'
        ];

        if ($details) {
            $logData['details'] = $details;
        }

        error_log(json_encode($logData));
    }
}
