<?php

namespace app\middleware;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use Pecee\SimpleRouter\SimpleRouter;

class AuthMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            SimpleRouter::response()->redirect(SimpleRouter::getUrl('login'));
        }
    }
}