<?php
namespace app\routes;

class Routes
{
    public static function get()
    {
        return [
            'get' => [
                '/' => 'HomeController@index',
                '/ip-validator/' => 'HomeController@index',
                '/ip-validator/forms.html' => fn() => include 'forms.html',
                '/ip-validator/admin.html' => fn() => include 'admin.html',
                '/ip-validator/config/iframe_config.json' => function () {
                    $filePath = __DIR__ . '/../config/iframe_config.json';

                    if (file_exists($filePath)) {
                        header('Content-Type: application/json');
                        echo file_get_contents($filePath);
                    } else {
                        http_response_code(404);
                        echo json_encode(['error' => 'Arquivo não encontrado']);
                    }
                },
            ],
            'post' => [
                '/ip-validator/validate' => 'HomeController@validateIP',
                '/save_iframe.php' => fn() => include 'save_iframe.php',
            ]
        ];
    }
}
