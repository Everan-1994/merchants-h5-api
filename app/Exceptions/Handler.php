<?php

namespace App\Exceptions;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Exception $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
        $className = class_basename($exception);
        $message = $exception->getMessage();
        $code = $exception->getCode();

        if ($className == 'ValidationException') {
            $code = PARAM_ERROR;
            $message = $exception->validator->errors()->first();
        } elseif (in_array($className, ['NotFoundHttpException', 'MethodNotAllowedHttpException'])) {
            $code = NOT_FOUND;
        } elseif ($className == 'TokenExpiredException') {
            $code = TOKEN_EXPIRED;
        } elseif (in_array($className, ['UnauthorizedHttpException', 'UnauthorizedException'])) {
            $code = UNAUTHORIZED;
        } elseif ($className == 'QueryException') {
            $code = SYSTEM_ERROR;
        } else {
            $code = $code ? $code : SYSTEM_ERROR;
        }

        return (new Controller())->outPut($code, $message, []);
    }
}
