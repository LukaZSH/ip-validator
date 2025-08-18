<?php
// app/controllers/HomeController.php

namespace app\controllers;

use App\Config\Database;
use PDO;
use PDOException;
use DateTime;

class HomeController
{
    // Define o período de carência em segundos (ex: 5 minutos)
    private const GRACE_PERIOD_SECONDS = 300;

    public function showHome()
    {
        require_once __DIR__ . '/../views/home.php';
    }

    public function showEventPage($slug)
    {
        // Validação de IP
        $userIp = $_SERVER['REMOTE_ADDR'] === '::1' ? '127.0.0.1' : $_SERVER['REMOTE_ADDR'];
        if (!$this->isIPInRange($userIp, '192.168.3.47', '192.168.8.255')) {
            die("Acesso negado. Você precisa estar conectado à rede da UNESPAR.");
        }

        try {
            $db = Database::getInstance()->getConnection();
            $now = new DateTime();

            // Validação do Evento e Tempo
            $stmt = $db->prepare("SELECT * FROM events WHERE slug = :slug AND status = 'Programado' AND :now BETWEEN start_time AND end_time");
            $stmt->execute(['slug' => $slug, 'now' => $now->format('Y-m-d H:i:s')]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                die("Evento não encontrado, indisponível ou fora do horário de registro.");
            }

            // Validação Anti-Fraude com Período de Carência
            $today = $now->format('Y-m-d');
            $stmt = $db->prepare("INSERT INTO presences (user_ip, registration_date) VALUES (:user_ip, :registration_date)");

            try {
                $stmt->execute(['user_ip' => $userIp, 'registration_date' => $today]);
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // Violação de chave única (já se registrou hoje)
                    // Verifica se está dentro do período de carência
                    if ($this->isWithinGracePeriod($db, $userIp, $today)) {
                        // Se estiver, mostra o formulário novamente
                        require_once __DIR__ . '/../views/event.php';
                        exit;
                    } else {
                        // Se não estiver, bloqueia o acesso
                        die("Sua presença para os eventos de hoje já foi registrada. Obrigado!");
                    }
                }
                throw $e; // Lança outros erros de DB
            }

            // Acesso Concedido: Exibe a página com o formulário
            require_once __DIR__ . '/../views/event.php';

        } catch (\Exception $e) {
            die("Ocorreu um erro no sistema: " . $e->getMessage());
        }
    }

    private function isWithinGracePeriod(PDO $db, string $userIp, string $today): bool
    {
        $stmt = $db->prepare("SELECT created_at FROM presences WHERE user_ip = :user_ip AND registration_date = :registration_date ORDER BY created_at DESC LIMIT 1");
        $stmt->execute(['user_ip' => $userIp, 'registration_date' => $today]);
        $lastRegistration = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($lastRegistration) {
            $lastTime = new DateTime($lastRegistration['created_at']);
            $currentTime = new DateTime();
            $interval = $currentTime->getTimestamp() - $lastTime->getTimestamp();

            return $interval < self::GRACE_PERIOD_SECONDS;
        }

        return false;
    }

    private function isIPInRange($userIP, $startIP, $endIP)
    {
        $userIPNum = ip2long($userIP);
        $startIPNum = ip2long($startIP);
        $endIPNum = ip2long($endIP);

        return ($userIPNum >= $startIPNum && $userIPNum <= $endIPNum);
    }
}
