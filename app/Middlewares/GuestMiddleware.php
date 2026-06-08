<?php

declare(strict_types=1);

namespace App\Middlewares;

use Core\Middleware;
use Core\Request;
use Core\Response;
use Core\Session;
use Core\Config;

final class GuestMiddleware implements Middleware
{
    public function handle(Request $request, Response $response, callable $next): void
    {
        $sessionUser = Session::get('user');
        if (!empty($sessionUser['id'])) {
            $path = 'admin/certificaciones';
            $response->redirect(Config::get('app.url') . $path);
            return;
        }

        $next($request, $response);
    }
}
