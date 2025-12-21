<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait ApiResponses
{
    /**
     * Return Ok 200 response with data
     *
     * @param $data
     *
     * @return JsonResponse
     */
    protected function okResponse($data = null, $message = null): JsonResponse
    {
        $data = array_merge(
            [
                'success' => true,
                'message' => $message ?? "ok response."
            ],
            $this->resolveData($data)
        );

        return response()->json(
            $this->resolveKeys($data),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * Return Bad Request 400 response with data
     *
     * @param $data
     *
     * @return JsonResponse
     */
    protected function badResponse($message = null): JsonResponse
    {
        $data = array_merge(
            [
                'success' => false,
                'message' => $message ?? "bad request response."
            ]
        );

        return response()->json(
            $this->resolveKeys($data),
            JsonResponse::HTTP_BAD_REQUEST
        );
    }

    /**
     * Return Created 201 response with data
     *
     * @param $data

     *
     * @return JsonResponse
     */
    protected function createdResponse($data = null, $message = null): JsonResponse
    {
        $data = array_merge(
            [
                'success' => true,
                'message' => $message ?? "created response."
            ],
            $this->resolveData($data)
        );

        return response()->json(
            $this->resolveKeys($data),
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * Return unauthenticated 401 response with data
     *
     * @param $data
     *
     * @return JsonResponse
     */
    protected function unauthorizedResponse($data = null, $message = null): JsonResponse
    {
        $data = array_merge(
            [
                'success' => false,
                'message' => $message ?? "unauthorized response"
            ],
            $this->resolveData($data)
        );

        return response()->json(
            $this->resolveKeys($data),
            JsonResponse::HTTP_UNAUTHORIZED
        );
    }

    /**
     * Return Not Found 404 response
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function notFoundResponse($message = null): JsonResponse
    {
        $data = [
            'success' => false,
            'message' => $message ?? 'Resource not found'
        ];

        return response()->json(
            $this->resolveKeys($data),
            JsonResponse::HTTP_NOT_FOUND
        );
    }

    /**
     * Return Validation Error 422 response
     *
     * @param array $errors
     * @param string|null $message
     * @return JsonResponse
     */
    protected function validationErrorResponse(array $errors, $message = null): JsonResponse
    {
        $data = [
            'success' => false,
            'message' => $message ?? 'Validation failed',
            'errors' => $errors
        ];

        return response()->json(
            $this->resolveKeys($data),
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * Return Server Error 500 response
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function serverErrorResponse($message = null): JsonResponse
    {
        $data = [
            'success' => false,
            'message' => $message ?? 'Internal server error'
        ];

        return response()->json(
            $this->resolveKeys($data),
            JsonResponse::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    /**
     * Return No Content 204 response
     *
     * @return JsonResponse
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Return Conflict 409 response
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function conflictResponse($message = null): JsonResponse
    {
        $data = [
            'success' => false,
            'message' => $message ?? 'Resource conflict'
        ];

        return response()->json(
            $this->resolveKeys($data),
            JsonResponse::HTTP_CONFLICT
        );
    }

    /**
     * Return Forbidden 403 response
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function forbiddenResponse($message = null): JsonResponse
    {
        $data = [
            'success' => false,
            'message' => $message ?? 'Access denied'
        ];

        return response()->json(
            $this->resolveKeys($data),
            JsonResponse::HTTP_FORBIDDEN
        );
    }

    /**
     * Resolving api response data
     *
     * @param mixed $data
     *
     * @return array
     */
    public function resolveData(mixed $data): array
    {
        if ($data === null) {
            return [];
        }

        if (is_array($data)) {
            return ['data' => $data];
        }

        if (is_object($data) && method_exists($data, 'toArray')) {
            return ['data' => $data->toArray(request())];
        }

        return ['data' => $data];
    }

    /**
     * Resolving the api response keys.
     *
     * @param array $data
     * @return array
     */
    public function resolveKeys(array $data): array
    {
        return collect($data)->reduce(function ($items, $value, $key) {

            if (is_array($value)) {
                $value = $this->resolveKeys($value);
            } else if ($value instanceof JsonResource || $value instanceof Collection) {
                $value = $this->resolveKeys($value->toArray(request()));
            }

            $items[Str::camel($key)] = $value;

            return $items;
        }, []);
    }

    /**
     * Return generic error response with custom status code
     *
     * @param string|null $message
     * @param int $statusCode
     * @param array|null $errors
     * @return JsonResponse
     */
    public function errorResponse($message = null, $statusCode = 400, $errors = null): JsonResponse
    {
        $data = [
            'success' => false,
            'message' => $message ?? 'An error occurred'
        ];

        if ($errors !== null) {
            $data['errors'] = $errors;
        }

        return response()->json(
            $this->resolveKeys($data),
            $statusCode
        );
    }

}
