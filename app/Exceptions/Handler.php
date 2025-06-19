<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            $status = 500;
            $message = 'Error inesperado';
            $debug = config('app.debug') ? $exception->getMessage() : null;

            switch (true) {
                case $exception instanceof ModelNotFoundException:
                case $exception instanceof NotFoundHttpException:
                    $status = 404;
                    $message = 'Recurso no encontrado';
                    break;

                case $exception instanceof AuthenticationException:
                    $status = 401;
                    $message = 'No autenticado';
                    break;

                case $exception instanceof AuthorizationException:
                    $status = 403;
                    $message = 'No autorizado';
                    break;

                case $exception instanceof ValidationException:
                    return response()->json([
                        'success' => false,
                        'message' => 'Error de validación',
                        'errors' => $exception->errors(),
                    ], 422);

                case $exception instanceof QueryException:
                    $status = 500;
                    $message = 'Error de base de datos';
                    break;

                case $exception instanceof MethodNotAllowedHttpException:
                    $status = 405;
                    $message = 'Método HTTP no permitido';
                    break;

                case $exception instanceof ThrottleRequestsException:
                    $status = 429;
                    $message = 'Demasiadas peticiones';
                    break;

                case $exception instanceof HttpException:
                    $status = $exception->getStatusCode();
                    $message = $exception->getMessage() ?: 'Error HTTP';
                    break;
            }

            $response = [
                'success' => false,
                'message' => $message,
            ];

            if ($debug) {
                $response['error'] = $debug;
            }

            return response()->json($response, $status);
        }

        return parent::render($request, $exception);
    }
}
