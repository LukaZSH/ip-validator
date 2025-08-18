<?php

namespace app\controllers;

use App\Config\Database;
use PDO;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

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
        $name = $_POST['name'];
        $slug = $_POST['slug'];
        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];
        $iframeCode = !empty($_POST['iframe_code']) ? $_POST['iframe_code'] : null;

        $status = ($iframeCode === null) ? 'Pendente' : 'Programado';

        try {
            $db = Database::getInstance()->getConnection();
            $sql = "INSERT INTO events (name, slug, start_time, end_time, iframe_code, status) 
                    VALUES (:name, :slug, :start_time, :end_time, :iframe_code, :status)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':slug', $slug);
            $stmt->bindParam(':start_time', $startTime);
            $stmt->bindParam(':end_time', $endTime);
            $stmt->bindParam(':iframe_code', $iframeCode);
            $stmt->bindParam(':status', $status);
            $stmt->execute();

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

            header('Location: /admin');
            exit;
        } catch (\Exception $e) {
            die("Erro ao atualizar o evento: " . $e->getMessage());
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

            header('Location: /admin');
            exit;

        } catch (\Exception $e) {
            die("Erro ao excluir o evento: " . $e->getMessage());
        }
    }

    public function generateQrCode()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            die("ID do evento não fornecido.");
        }

        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT slug FROM events WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                die("Evento não encontrado.");
            }

            $url = "http://192.168.3.2/evento/" . $event['slug'];

            // Cria o QR Code
            $qrCode = QrCode::create($url)
                ->setSize(400) // Aumenta o tamanho para acomodar melhor o logo
                ->setMargin(10);

            // Cria o objeto do Logo a partir da URL
            $logoUrl = 'https://www.unespar.edu.br/sou-mais-unespar/arquivos/logo-unespar-original.png/@@images/47fd7595-1494-49d0-a856-d1b51cd6b460.png';
            $logo = Logo::create($logoUrl)
                ->setResizeToWidth(100); // Define a largura do logo

            $writer = new PngWriter();
            // Passa tanto o QR Code quanto o Logo para o escritor
            $result = $writer->write($qrCode, $logo);

            // Envia a imagem diretamente para o navegador
            header('Content-Type: '.$result->getMimeType());
            echo $result->getString();
            exit;

        } catch (\Exception $e) {
            die("Erro ao gerar o QR Code: " . $e->getMessage());
        }
    }
}
