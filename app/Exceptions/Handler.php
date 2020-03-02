<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

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
     * Generic response format.
     *
     * @var array
     */
    protected $format = [
        'code' => ':code',
        'message' => ':message',
        'errors' => ':errors',
        'debug' => ':debug',
    ];

    /**
     * User defined replacements to merge with defaults.
     *
     * @var array
     */
    protected $replacements = [];

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
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * Convert the given exception to an array.
     * Note: this is override parent's method for custom json format
     *
     * @param  \Exception  $exception
     * @return array
     */
    protected function convertExceptionToArray(Exception $exception)
    {
        $replacements = $this->prepareReplacements($exception);
        $response = $this->newResponseArray();

        array_walk_recursive($response, function (&$value) use ($exception, $replacements) {
            if (Str::startsWith($value, ':') && isset($replacements[$value])) {
                $value = $replacements[$value];
            }
        });

        return $this->recursivelyRemoveEmptyReplacements($response);
    }

    /**
     * Custom json format
     *
     * @param Exception $exception
     * @return array
     */
    protected function prepareReplacements(Exception $exception)
    {
        $statusCode = $this->getStatusCode($exception);
        $message = $exception->getMessage();
        if (! $message ) {
            $message = sprintf('%d %s', $statusCode, Response::$statusTexts[$statusCode]);
        }

        $replacements = [
            ':code' => $exception->getCode() ?: $statusCode,
            ':message' => $message,
            ':errors' => [],
        ];

        if ($exception instanceof ModelNotFoundException) {
            $replacements[':message'] =
                'No query results for model ['
                . Str::after($exception->getModel(), 'Models\\')
                . '] with ' .  implode(',', $exception->getIds());
        }

        if(config('app.debug')) {
            $replacements[':debug'] = [
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => collect($exception->getTrace())->map(function ($trace) {
                    return Arr::except($trace, ['args']);
                })->all(),
            ];
        }

        return array_merge($replacements, $this->replacements);
    }

    /**
     * Get the status code from the exception.
     *
     * @param \Exception $exception
     *
     * @return int
     */
    protected function getStatusCode(Exception $exception)
    {
        $statusCode = 500;
        if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
            $statusCode = 419;
        } else if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
            $statusCode = $exception->getCode();
        }else if ($exception instanceof \Tymon\JWTAuth\Exceptions\JWTException) {
            $statusCode = $exception->getCode();
        }
        return $this->getExceptionStatusCode($exception, $statusCode);
    }

    /**
     * Get the exception status code.
     *
     * @param \Exception $exception
     * @param int        $defaultStatusCode
     *
     * @return int
     */
    protected function getExceptionStatusCode(Exception $exception, $defaultStatusCode = 500)
    {
        return $this->isHttpException($exception) ? $exception->getStatusCode() : $defaultStatusCode;
    }

    /**
     * Get the headers from the exception.
     *
     * @param \Exception $exception
     *
     * @return array
     */
    protected function getHeaders(Exception $exception)
    {
        return $this->isHttpException($exception) ? $exception->getHeaders() : [];
    }

    /**
     * Recursively remove any empty replacement values in the response array.
     *
     * @param array $input
     *
     * @return array
     */
    protected function recursivelyRemoveEmptyReplacements(array $input)
    {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = $this->recursivelyRemoveEmptyReplacements($value);
            }
        }

        return array_filter($input, function ($value) {
            if (is_string($value)) {
                return ! Str::startsWith($value, ':');
            }

            return true;
        });
    }

    /**
     * Create a new response array with replacement values.
     *
     * @return array
     */
    protected function newResponseArray()
    {
        return $this->format;
    }
}
