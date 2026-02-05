<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\ConnectionException as DatabaseConnectionException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Redis\Connections\ConnectionException as RedisConnectionException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Base API controller that standardizes safe GET execution and error mapping.
 */
abstract class ApiController extends Controller
{
    /**
     * Run a GET callback and translate known failures to JSON responses.
     *
     * @param callable(): (JsonResponse|array<mixed>|mixed) $callback
     */
    protected function safeGet(callable $callback): JsonResponse
    {
        try {
            $result = $callback();

            if ($result instanceof JsonResponse) {
                return $result;
            }

            if (is_array($result)) {
                return response()->json($result, 200);
            }

            return response()->json(['data' => $result], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException|NotFoundHttpException $e) {
            return response()->json([
                'error' => 'Resource not found',
            ], 404);
        } catch (HttpException $e) {
            $status = $e->getStatusCode();

            if ($status === 404) {
                return response()->json(['error' => 'Resource not found'], 404);
            }

            if ($status === 422) {
                return response()->json(['error' => $e->getMessage() ?: 'Invalid request'], 422);
            }

            if ($status === 503) {
                report($e);

                return response()->json(['error' => 'Service unavailable'], 503);
            }

            return response()->json([
                'error' => $e->getMessage() ?: 'Request failed',
            ], $status);
        } catch (Throwable $e) {
            if ($this->isServiceUnavailable($e)) {
                report($e);

                return response()->json([
                    'error' => 'Service unavailable',
                ], 503);
            }

            report($e);

            return response()->json([
                'error' => 'Internal server error',
            ], 500);
        }
    }

    /**
     * Determine whether the exception indicates a service outage.
     */
    protected function isServiceUnavailable(Throwable $e): bool
    {
        return $e instanceof QueryException
            || $e instanceof DatabaseConnectionException
            || $e instanceof RedisConnectionException
            || $e instanceof LockTimeoutException
            || $e instanceof \PDOException
            || $e instanceof \RedisException;
    }
}
