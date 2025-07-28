<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Daftarkan handler untuk exception JWT di sini
        $exceptions->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                if ($e instanceof TokenExpiredException) {
                    return response()->json(['message' => 'Token has expired'], 401);
                }
                if ($e instanceof TokenInvalidException) {
                    return response()->json(['message' => 'Token is invalid'], 401);
                }
                if ($e instanceof JWTException) {
                    return response()->json(['message' => 'Token is absent'], 401);
                }
            }
        });
    })->create();
