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

/**
 * Maps known exceptions to consistent JSON responses for API and JSON requests.
 */
class ApiExceptionHandler
{
    /**
     * Handles throwable values for API/JSON requests and returns a normalized JSON response.
     * Returns null when the request is not an API route and does not expect JSON.
     *
     * @param  Throwable  $e  Thrown exception to normalize.
     * @param  Request  $request  Current HTTP request.
     * @return JsonResponse|null Normalized API error response or null for non-API requests.
     */
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

    /**
     * Converts a validation exception to API error payload with field errors.
     *
     * @param  ValidationException  $e  Validation exception containing error bag.
     * @return JsonResponse JSON response with 422 status.
     */
    protected function validationException(ValidationException $e): JsonResponse
    {
        return response()->json([
            'error' => 'Validation failed',
            'errors' => $e->errors(),
        ], 422);
    }

    /**
     * Converts an authentication exception to an unauthorized API response.
     *
     * @param  AuthenticationException  $e  Authentication exception instance.
     * @return JsonResponse JSON response with 401 status.
     */
    protected function authenticationException(AuthenticationException $e): JsonResponse
    {
        return response()->json([
            'error' => 'Unauthorized',
        ], 401);
    }

    /**
     * Converts a missing model exception to a not found API response.
     *
     * @param  ModelNotFoundException  $e  Missing model exception instance.
     * @return JsonResponse JSON response with 404 status.
     */
    protected function modelNotFoundException(ModelNotFoundException $e): JsonResponse
    {
        return response()->json([
            'error' => 'Resource not found',
        ], 404);
    }

    /**
     * Converts a route-not-found exception to a not found API response.
     *
     * @param  NotFoundHttpException  $e  Route not found exception instance.
     * @return JsonResponse JSON response with 404 status.
     */
    protected function notFoundException(NotFoundHttpException $e): JsonResponse
    {
        return response()->json([
            'error' => 'Not found',
        ], 404);
    }

    /**
     * Converts a method-not-allowed exception to a 405 API response.
     *
     * @param  MethodNotAllowedHttpException  $e  Method not allowed exception instance.
     * @return JsonResponse JSON response with 405 status.
     */
    protected function methodNotAllowedException(MethodNotAllowedHttpException $e): JsonResponse
    {
        return response()->json([
            'error' => 'Method not allowed',
        ], 405);
    }

    /**
     * Converts a throttling exception to a 429 API response.
     *
     * @param  TooManyRequestsHttpException  $e  Throttling exception instance.
     * @return JsonResponse JSON response with optional retry_after and 429 status.
     */
    protected function tooManyRequestsException(TooManyRequestsHttpException $e): JsonResponse
    {
        return response()->json([
            'error' => 'Too many requests',
            'retry_after' => $e->getHeaders()['Retry-After'] ?? null,
        ], 429);
    }

    /**
     * Converts a generic HTTP exception to an API response with its status code.
     *
     * @param  HttpException  $e  HTTP exception instance.
     * @return JsonResponse JSON response with status from the exception.
     */
    protected function httpException(HttpException $e): JsonResponse
    {
        return response()->json([
            'error' => $e->getMessage() ?: 'An error occurred',
        ], $e->getStatusCode());
    }

    /**
     * Converts any unhandled exception to a safe API response.
     * In debug mode the original exception message is exposed; otherwise a generic message is returned.
     *
     * @param  Throwable  $e  Unhandled exception.
     * @return JsonResponse JSON response with 500 status.
     */
    protected function genericException(Throwable $e): JsonResponse
    {
        $isDebug = config('app.debug');

        return response()->json([
            'error' => $isDebug ? $e->getMessage() : 'Internal server error',
        ], 500);
    }
}
