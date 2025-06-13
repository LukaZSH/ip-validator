<?php

namespace app\controllers;

class HomeController {
    // Função para carregar a página home
    public function index() {
        require_once 'app/views/home.php';
    }

    // Função para validar o IP
    public function validateIP() {
        // Pega o IP do usuário
        $IPUser = $_SERVER['REMOTE_ADDR'];

        // Se o IP for "::1", altera para "127.0.0.1" (testes em localhost)
        if ($IPUser === '::1') {
            $IPUser = '127.0.0.1';
        }

        // Ranges permitidos
        $allowedIPRangeStart = '192.168.3.47';
        $allowedIPRangeEnd = '192.168.8.255';

        // Verificar se o IP está na faixa permitida
        if ($this->isIPInRange($IPUser, $allowedIPRangeStart, $allowedIPRangeEnd)) {
            // Redirecionar para forms.html se o IP estiver dentro da faixa
            header('Location: /forms.html');
            exit;
        } else {
            echo "IP não permitido. Tente novamente.";
        }
    }

    // Função para verificar se o IP está dentro da faixa
    private function isIPInRange($userIP, $startIP, $endIP) {
        // Converter os endereços IP em números inteiros
        $userIPNum = ip2long($userIP);
        $startIPNum = ip2long($startIP);
        $endIPNum = ip2long($endIP);

        return ($userIPNum >= $startIPNum && $userIPNum <= $endIPNum);
    }
}
