<?php
// Caminho do arquivo JSON
$jsonFilePath = __DIR__ . '/config/iframe_config.json';

// Verifica se o método de requisição é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém os dados JSON enviados no corpo da requisição
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Verifica se o dado do iframeCode está presente
    if (isset($data['iframeCode'])) {
        // Prepara o novo conteúdo do JSON
        $newConfig = ['iframeCode' => $data['iframeCode']];

        // Salva o novo código do iframe no arquivo JSON
        if (file_put_contents($jsonFilePath, json_encode($newConfig, JSON_PRETTY_PRINT))) {
            // Responde com sucesso
            http_response_code(200);
            echo json_encode(['message' => 'Código do iframe salvo com sucesso.']);
        } else {
            // Responde com erro se houver falha na gravação
            http_response_code(500);
            echo json_encode(['message' => 'Erro ao salvar o arquivo.']);
        }
    } else {
        // Responde com erro se os dados estiverem faltando
        http_response_code(400);
        echo json_encode(['message' => 'Dados inválidos.']);
    }
}
?>
