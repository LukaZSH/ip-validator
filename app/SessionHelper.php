<?php

namespace app;

class SessionHelper
{
    /**
     * Define uma mensagem de flash que será exibida na próxima requisição.
     * @param string $type 'success' ou 'error'
     * @param string $message A mensagem a ser exibida.
     */
    public static function setFlashMessage(string $type, string $message): void
    {
        // Inicia a sessão se ainda não estiver ativa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    /**
     * Retorna e remove a mensagem de flash de um tipo específico.
     * @param string $type 'success' ou 'error'
     * @return string|null A mensagem ou null se não existir
     */
    public static function getFlashMessage(string $type): ?string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['flash_message']) && $_SESSION['flash_message']['type'] === $type) {
            $message = $_SESSION['flash_message']['message'];
            unset($_SESSION['flash_message']);
            return $message;
        }

        return null;
    }

    public static function displayFlashMessage(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['flash_message'])) {
            $type = $_SESSION['flash_message']['type'] === 'success' ? 'flash-success' : 'flash-error';
            $message = htmlspecialchars($_SESSION['flash_message']['message']);

            echo "<div class='flash-message {$type}'>{$message}</div>";

            // Apaga a mensagem da sessão para que não seja exibida novamente
            unset($_SESSION['flash_message']);
        }
    }
}