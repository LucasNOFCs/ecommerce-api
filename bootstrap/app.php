<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: [__DIR__.'/../routes/v1/v1_api.php'],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('api/*')) {
                return null;
            }

            return '/login';
        });

        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') ||
                $request->expectsJson(),
        );

        $exceptions->respond(function ($response) {
            if (! request()->is('api/v1/*')) {
                return $response;
            }

            $messages = [
                500 => 'Internal Server Error',
                404 => 'Not Found',
                401 => 'Unauthorized',
                403 => 'Forbidden',
                400 => 'Bad Request',
                422 => 'Unprocessable Entity',
                405 => 'Method Not Allowed',
            ];

            $status = $response->getStatusCode();

            if (! isset($messages[$status])) {
                return $response;
            }

            return response()->json([
                'message' => $messages[$status],
            ], $status);
        });
    })
    ->create();
