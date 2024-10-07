<!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Validação de Presença</title>
        <style>
        html {
            background-image: url(https://i.postimg.cc/HL8yYDwG/bg-unespar-page.png);
            background-size: cover;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Configurações do body e fundo */
        body {
            height: 100vh;
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            background-size: cover;
            background-position: center;
        }

        /* Container centralizado para todo o conteúdo */
        .central-container {
            background-color: white;
            border-radius: 30px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px; /* Ajustado para melhor responsividade */
            padding: 40px 20px; /* Espaçamento interno aumentado */
            text-align: center;
            margin: 20px;
        }
        
        /* Logo Unespar */
        img{
            width: 121px;
            }
        /* Estilização do texto */
        h1 {
            font-family: 'Roboto', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        p {
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            color: #555;
            margin-bottom: 25px;
        }

        /* Estilização do botão */
        button {
            padding: 12px 25px;
            margin: 15px;
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
        </style>
    </head>
    <body>
        <div class="central-container">
            <img src="https://www.unespar.edu.br/sou-mais-unespar/arquivos/logo-unespar-original.png/@@images/47fd7595-1494-49d0-a856-d1b51cd6b460.png" alt="logo">
            <h1>Valide sua presença</h1>
            <p>Clique no botão abaixo para validar sua presença:</p>
            <form action="/ip-validator/validate" method="POST">
                <button type="submit">Validar</button>
            </form>
        </div>
    </body>
    </html>
