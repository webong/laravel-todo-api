<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
        });

        $this->renderable(function (Throwable $e, Request $request) {
            // This will replace our 404 response with a JSON response.
            if ($e instanceof ModelNotFoundException && $request->wantsJson()) {
                return response()->json([
                    'message' => 'Resource item not found.'
                ], 404);
            }

            if ($e instanceof NotFoundHttpException && $request->wantsJson()) {
                return response()->json([
                    'message' => 'Resource not found.'
                ], 404);
            }

            if ($e instanceof MethodNotAllowedHttpException && $request->wantsJson()) {
                return response()->json([
                    'message' => 'Method not allowed.'
                ], 405);
            }
        });
    }
}
