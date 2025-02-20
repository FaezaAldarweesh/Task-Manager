<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Support\Facades\Log;
use App\Services\ApiResponseService;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
        });
    }

    public function render($request, Throwable $exception)
    {
            // Log all exceptions here as well, if desired
            Log::error('Exception occurred', [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'exception' => $exception,
            ]);

            if ($exception instanceof NotFoundHttpException) {
                return ApiResponseService::error(trans('general.resource_not_found'), 404);
            }

            if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                return ApiResponseService::error(trans('general.unauthenticated'), 401);
            }

            if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return ApiResponseService::error(trans('general.forbidden'), 403);
            }

            return ApiResponseService::error(trans('general.operation_failed'), 500);
        }
    }
