<?php
// Inicia a sessão em todas as páginas que incluírem este arquivo
session_start();

// Se a variável de sessão 'loggedin' não existir ou não for 'true',
// redireciona o usuário para a página de login e encerra o script.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('location: /login.html');
    exit;
}