<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            $statusCode = $exception->getStatusCode();
            $message = $this->getErrorMessage($statusCode);
            
            return response()->view('errors.generic', ['message' => $message, 'statusCode' => $statusCode], $statusCode);
        }

        return parent::render($request, $exception);
    }

    private function getErrorMessage($statusCode)
    {
        switch ($statusCode) {
            case 404:
                return 'The page you requested was not found.';
            case 500:
                return 'An internal server error has occurred.';
            case 403:
                return 'You don\'t have permission to access this page.';
            case 401:
                return 'Unauthorized access.';
            default:
                return 'An error occurred.';
        }
    }
}
