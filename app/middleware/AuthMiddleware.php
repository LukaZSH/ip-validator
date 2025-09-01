<?php

namespace app\middleware;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use Pecee\SimpleRouter\SimpleRouter;
use App\SessionHelper;

class AuthMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        // A sessão já é iniciada em public/index.php

        if (SessionHelper::get('user_id') === null) {
            SimpleRouter::response()->redirect(url('login'));
        }
    }
}