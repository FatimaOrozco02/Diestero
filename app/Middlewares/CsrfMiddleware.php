<?php

declare(strict_types=1);

namespace App\Middlewares;

use Core\Middleware;
use Core\Request;
use Core\Response;
use Core\Session;

final class CsrfMiddleware implements Middleware
{
    public function handle(Request $request, Response $response, callable $next): void
    {
        // Solo proteger métodos que cambian estado
        if (in_array($request->method(), ['POST', 'PUT', 'DELETE', 'PATCH'], true)) {
            $sessionToken = Session::get('_csrf_token') ?? '';
            $token = (string)($request->input('_token', '') ?: $request->header('x-csrf-token', ''));

            if ($sessionToken === '' || $token === '' || !hash_equals($sessionToken, $token)) {
                $response->text('CSRF token mismatch.', 419);
                return;
            }
        }

        $next($request, $response);
    }
}