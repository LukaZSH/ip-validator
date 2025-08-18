<?php

namespace app\controllers;

use App\Config\Database;
use PDO;

class HomeController
{
    /**
     * Exibe a página inicial de validação de IP.
     */
    public function showHome()
    {
        require_once __DIR__ . '/../views/home.php';
    }

    /**
     * Controla o acesso à página de um evento específico.
     * @param string $slug A URL do evento.
     */

    public function showEventPage($slug)
    {
        // Validação de IP 
        $userIp = $_SERVER['REMOTE_ADDR'];
        if ($userIp === '::1') {
            $userIp = '127.0.0.1';
        }

        $allowedIPRangeStart = '192.168.3.47';
        $allowedIPRangeEnd = '192.168.8.255';

        if (!$this->isIPInRange($userIp, $allowedIPRangeStart, $allowedIPRangeEnd)) {
            die("Acesso negado. Você precisa estar conectado à rede da UNESPAR.");
        }

        try {
            $db = Database::getInstance()->getConnection();
            $now = date('Y-m-d H:i:s');

            // Validação do Evento e Tempo
            $stmt = $db->prepare("SELECT * FROM events WHERE slug = :slug AND status = 'Programado' AND :now BETWEEN start_time AND end_time");
            $stmt->execute(['slug' => $slug, 'now' => $now]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                die("Evento não encontrado, indisponível ou fora do horário de registro.");
            }

            // Validação Anti-Fraude
            $today = date('Y-m-d');
            $stmt = $db->prepare("INSERT INTO presences (user_ip, registration_date) VALUES (:user_ip, :registration_date)");

            try {
                $stmt->execute(['user_ip' => $userIp, 'registration_date' => $today]);
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // Código de erro para violação de chave única
                    die("Sua presença para os eventos de hoje já foi registrada. Obrigado!");
                }
                throw $e;
            }

            // Acesso Concedido: Exibe a página com o formulário
            require_once __DIR__ . '/../views/event.php';

        } catch (\Exception $e) {
            die("Ocorreu um erro no sistema: " . $e->getMessage());
        }
    }

    /**
     * Função para verificar se o IP está dentro da faixa permitida.
     * agora como um método privado da classe
     */
    private function isIPInRange($userIP, $startIP, $endIP)
    {
        $userIPNum = ip2long($userIP);
        $startIPNum = ip2long($startIP);
        $endIPNum = ip2long($endIP);

        return ($userIPNum >= $startIPNum && $userIPNum <= $endIPNum);
    }
}