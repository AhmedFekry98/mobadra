<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Log;

trait HandleServiceExceptions
{
    /**
     * Handle service exceptions and return appropriate responses
     */
    protected function handleServiceException(\Throwable $exception, string $context = '')
    {
        // Log the exception with appropriate level
        $this->logException($exception, $context);

        // Handle specific exception types
        if ($exception instanceof ModelNotFoundException) {
            return $this->notFoundResponse('Resource not found');
        }

        if ($exception instanceof ValidationException) {
            return $this->validationErrorResponse($exception->errors());
        }

        if ($exception instanceof AuthorizationException) {
            return $this->forbiddenResponse($exception->getMessage() ?: 'Access denied');
        }

        if ($exception instanceof QueryException) {
            return $this->handleDatabaseException($exception);
        }

        if ($exception instanceof HttpException) {
            return $this->handleHttpException($exception);
        }

        if ($exception instanceof \InvalidArgumentException) {
            return $this->badResponse($exception->getMessage());
        }

        // Handle general exceptions
        return $this->serverErrorResponse(
            app()->environment('production')
                ? 'An error occurred while processing your request'
                : $exception->getMessage()
        );
    }

    /**
     * Log exception with appropriate level
     */
    protected function logException(\Throwable $exception, string $context = ''): void
    {
        $logData = [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'context' => $context,
        ];

        // Log with different levels based on exception type
        if ($exception instanceof ValidationException ||
            $exception instanceof AuthorizationException ||
            $exception instanceof ModelNotFoundException) {
            Log::info("Service Exception in {$context}: " . $exception->getMessage(), $logData);
        } else {
            Log::error("Service Exception in {$context}: " . $exception->getMessage(), array_merge($logData, [
                'trace' => $exception->getTraceAsString()
            ]));
        }
    }

    /**
     * Handle database exceptions
     */
    protected function handleDatabaseException(QueryException $exception)
    {
        // Check for common database errors
        $errorCode = $exception->errorInfo[1] ?? null;

        switch ($errorCode) {
            case 1062: // Duplicate entry
                return $this->conflictResponse('Duplicate entry detected');
            case 1452: // Foreign key constraint
                return $this->badResponse('Cannot delete: resource is being used elsewhere');
            default:
                return $this->serverErrorResponse(
                    app()->environment('production')
                        ? 'Database error occurred'
                        : $exception->getMessage()
                );
        }
    }

    /**
     * Handle HTTP exceptions
     */
    protected function handleHttpException(HttpException $exception)
    {
        $statusCode = $exception->getStatusCode();
        $message = $exception->getMessage() ?: 'HTTP error occurred';

        return response()->json([
            'success' => false,
            'message' => $message
        ], $statusCode);
    }

    /**
     * Execute service method with exception handling
     */
    protected function executeService(callable $callback, string $context = '')
    {
        try {
            return $callback();
        } catch (\Throwable $exception) {
            return $this->handleServiceException($exception, $context);
        }
    }
}
