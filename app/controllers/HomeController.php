<?php

namespace app\controllers;

use App\Config\Database;
use PDO;
use PDOException;
use DateTime;
use DateTimeZone;

class HomeController
{
    private const GRACE_PERIOD_SECONDS = 300;

    public function showHome()
    {
        require_once __DIR__ . '/../views/home.php';
    }

    public function showEventPage($slug)
    {
        $userIp = $_SERVER['REMOTE_ADDR'] === '::1' ? '127.0.0.1' : $_SERVER['REMOTE_ADDR'];
        if (!$this->isIPInRange($userIp, '192.168.3.47', '192.168.8.255')) {
            die("Acesso negado. Seu IP (" . $userIp . ") não está na faixa permitida. Você precisa estar conectado à rede da UNESPAR.");
        }

        try {
            $db = Database::getInstance()->getConnection();
            $now = new DateTime("now", new DateTimeZone('America/Sao_Paulo'));

            $stmt = $db->prepare("SELECT * FROM events WHERE slug = :slug AND status = 'Programado' AND :now BETWEEN start_time AND end_time");
            $stmt->execute(['slug' => $slug, 'now' => $now->format('Y-m-d H:i:s')]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                die("Evento não encontrado, indisponível ou fora do horário de registro.");
            }

            $today = $now->format('Y-m-d');
            $stmt = $db->prepare("INSERT INTO presences (user_ip, registration_date) VALUES (:user_ip, :registration_date)");

            try {
                $stmt->execute(['user_ip' => $userIp, 'registration_date' => $today]);
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    if ($this->isWithinGracePeriod($db, $userIp, $today)) {
                        require_once __DIR__ . '/../views/event.php';
                        exit;
                    } else {
                        die("Sua presença para os eventos de hoje já foi registrada. Obrigado!");
                    }
                }
                throw $e;
            }

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
            // Garante que ambos os objetos DateTime usem o mesmo fuso horário
            $lastTime = new DateTime($lastRegistration['created_at'], new DateTimeZone('America/Sao_Paulo'));
            $currentTime = new DateTime("now", new DateTimeZone('America/Sao_Paulo'));
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
