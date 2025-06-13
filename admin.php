<?php
session_start();

// Se a sessão 'loggedin' não existir ou não for true, redireciona para a página de login.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Alterar código do iframe</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        html {
            background-image: url(https://i.postimg.cc/HL8yYDwG/bg-unespar-page.png);
            background-size: cover;
            background-position: center;
        }
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: 'Helvetica Neue', Arial, sans-serif;
            color: #333;
        }
        .central-container {
            background-color: white;
            border-radius: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            padding: 40px;
            text-align: center;
            margin: 20px;
        }
        img{
           width: 170px;
        }
        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #555;
        }
        textarea {
            width: 100%;
            height: 200px;
            padding: 15px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            resize: none;
            outline: none;
            transition: border 0.3s ease, box-shadow 0.3s ease;
        }
        textarea:focus {
            border-color: #007BFF;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
        }
        button {
            padding: 12px 25px;
            margin-top: 15px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0, 123, 255, 0.3);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
            box-shadow: 0 6px 12px rgba(0, 86, 179, 0.4);
        }
        button:active {
            transform: scale(0.98);
        }

        /* --- ESTILO DO BOTÃO DE LOGOUT ATUALIZADO --- */
        .logout-button {
            display: block; /* Faz o link ocupar a linha inteira, movendo-o para baixo */
            width: fit-content; /* Faz a largura se ajustar ao conteúdo */
            margin: 15px auto 0; /* Centraliza e adiciona espaço no topo */
            padding: 8px 20px; /* Padding menor para um botão menor */
            background-color: #dc3545; /* Cor de fundo vermelha */
            color: white; /* Cor do texto branca */
            border-radius: 20px;
            text-decoration: none; /* Remove o sublinhado do link */
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(220, 53, 69, 0.3);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        .logout-button:hover {
            background-color: #c82333; /* Vermelho mais escuro no hover */
            box-shadow: 0 6px 12px rgba(200, 35, 51, 0.4);
        }
        /* --- FIM DO ESTILO ATUALIZADO --- */

        @media (max-width: 768px) {
            .central-container {
                width: 90%;
                padding: 20px;
            }
            button {
                width: 90%;
                text-align: center;
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="central-container">
        <img src="https://www.unespar.edu.br/sou-mais-unespar/arquivos/logo-unespar-original.png/@@images/47fd7595-1494-49d0-a856-d1b51cd6b460.png" alt="logo">
        <h2>Alterar código do iframe</h2>
        <textarea id="iframeCode" placeholder="Cole o novo código do iframe aqui..."></textarea>
        <button onclick="saveIframeCode()">Salvar Código</button>
        <a href="/logout" class="logout-button">Sair</a>
    </div>

    <script>
        // A função para carregar o código antigo foi removida.

        // Função para salvar o novo código do iframe
        function saveIframeCode() {
            var iframeCode = document.getElementById('iframeCode').value;

            // Verifica se a caixa de texto não está vazia
            if (iframeCode.trim() === '') {
                alert('Por favor, insira um código de iframe antes de salvar.');
                return;
            }

            fetch('/save_iframe.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ iframeCode: iframeCode })
            }).then(response => {
                if (response.ok) {
                    alert('Código do iframe salvo com sucesso!');
                    document.getElementById('iframeCode').value = '';
                } else {
                    alert('Erro ao salvar o código do iframe.');
                }
            });
        }
    </script>
</body>
</html>