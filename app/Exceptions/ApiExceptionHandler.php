<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class ApiExceptionHandler
{
    public function handle(Throwable $e, Request $request): ?JsonResponse
    {
        if (! $request->is('api/*') && ! $request->expectsJson()) {
            return null;
        }

        return match (true) {
            $e instanceof ValidationException => $this->validationException($e),
            $e instanceof AuthenticationException => $this->authenticationException($e),
            $e instanceof ModelNotFoundException => $this->modelNotFoundException($e),
            $e instanceof NotFoundHttpException => $this->notFoundException($e),
            $e instanceof MethodNotAllowedHttpException => $this->methodNotAllowedException($e),
            $e instanceof TooManyRequestsHttpException => $this->tooManyRequestsException($e),
            $e instanceof HttpException => $this->httpException($e),
            default => $this->genericException($e),
        };
    }

    protected function validationException(ValidationException $e): JsonResponse
    {
        return response()->json([
            'error' => 'Validation failed',
            'errors' => $e->errors(),
        ], 422);
    }

    protected function authenticationException(AuthenticationException $e): JsonResponse
    {
        return response()->json([
            'error' => 'Unauthorized',
        ], 401);
    }

    protected function modelNotFoundException(ModelNotFoundException $e): JsonResponse
    {
        return response()->json([
            'error' => 'Resource not found',
        ], 404);
    }

    protected function notFoundException(NotFoundHttpException $e): JsonResponse
    {
        return response()->json([
            'error' => 'Not found',
        ], 404);
    }

    protected function methodNotAllowedException(MethodNotAllowedHttpException $e): JsonResponse
    {
        return response()->json([
            'error' => 'Method not allowed',
        ], 405);
    }

    protected function tooManyRequestsException(TooManyRequestsHttpException $e): JsonResponse
    {
        return response()->json([
            'error' => 'Too many requests',
            'retry_after' => $e->getHeaders()['Retry-After'] ?? null,
        ], 429);
    }

    protected function httpException(HttpException $e): JsonResponse
    {
        return response()->json([
            'error' => $e->getMessage() ?: 'An error occurred',
        ], $e->getStatusCode());
    }

    protected function genericException(Throwable $e): JsonResponse
    {
        $isDebug = config('app.debug');

        return response()->json([
            'error' => $isDebug ? $e->getMessage() : 'Internal server error',
        ], 500);
    }
}
