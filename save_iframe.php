<?php

session_start();

// 1. Verificação de Segurança
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403);
    echo json_encode(['message' => 'Acesso negado.']);
    exit;
}

// 2. Define o caminho para o arquivo de configuração
$jsonFilePath = __DIR__ . '/config/iframe_config.json';

// --- NOVAS VERIFICAÇÕES DE DEPURAÇÃO ---

// Verifica se o caminho para o arquivo realmente existe
if (file_exists($jsonFilePath) === false) {
    http_response_code(500);
    echo json_encode(['message' => 'ERRO DE DEBUG: O arquivo de configuração não foi encontrado no caminho esperado: ' . $jsonFilePath]);
    exit;
}

// Verifica se o PHP tem permissão para escrever no arquivo
if (is_writable($jsonFilePath) === false) {
    http_response_code(500);
    echo json_encode(['message' => 'ERRO DE DEBUG: O PHP não tem permissão para escrever no arquivo. Verifique as permissões de escrita para: ' . $jsonFilePath]);
    exit;
}

// --- FIM DAS VERIFICAÇÕES DE DEPURAÇÃO ---


// 3. Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (isset($data['iframeCode'])) {
        $iframeCode = $data['iframeCode'];

        // 4. Validação do Iframe
        $dom = new DOMDocument();
        @$dom->loadHTML($iframeCode, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $iframes = $dom->getElementsByTagName('iframe');

        if ($iframes->length !== 1) {
            http_response_code(400);
            echo json_encode(['message' => 'Erro: O código fornecido não parece ser um iframe válido.']);
            exit;
        }

        $iframe = $iframes->item(0);
        $src = $iframe->getAttribute('src');

        $allowed_sources = [
            'https://docs.google.com/forms/',
            'https://forms.office.com/',
            'https://forms.microsoft.com/'
        ];

        $is_source_allowed = false;
        foreach ($allowed_sources as $allowed_source) {
            if (strpos($src, $allowed_source) === 0) {
                $is_source_allowed = true;
                break;
            }
        }

        if (!$is_source_allowed) {
            http_response_code(400);
            echo json_encode(['message' => 'Erro: Apenas iframes do Google Forms ou Microsoft Forms são permitidos.']);
            exit;
        }
        
        // 5. Preparação e Salvamento do Arquivo
        $newConfig = ['iframeCode' => $iframeCode];
        $jsonData = json_encode($newConfig, JSON_PRETTY_PRINT);

        if (file_put_contents($jsonFilePath, $jsonData) !== false) {
            http_response_code(200);
            echo json_encode(['message' => 'Código do iframe salvo com sucesso.']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Erro interno ao salvar o arquivo.']);
        }

    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Dados inválidos.']);
    }
}
?>