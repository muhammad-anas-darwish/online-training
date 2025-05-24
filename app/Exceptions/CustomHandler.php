<?php

namespace App\Exceptions;

use App\Traits\ApiResponses;
use Exception;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\JsonResponse;
use Throwable;

class CustomHandler // extends Handler
{
    use ApiResponses;
    
    public function __invoke(Throwable $e)
    {
        return $this->handleApiException( $e);
    }

    protected function handleApiException(Throwable $exception): JsonResponse
    {
        if ($exception instanceof \Illuminate\Http\Exceptions\HttpResponseException) {
            return $exception->getResponse();
        }

        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return $this->unauthorizedResponse(__('exceptions.unauthenticated'));
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return $this->failedResponse(
                $exception->getMessage(),
                422,
                $exception->errors()
            );
        }

        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->failedResponse(__('Resource not found'), 404);
        }

        return config('app.debug') ? response()->json([
            'message' => $exception->getMessage(),
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace()
        ]) : null;
    }
}