<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        ValidationException::class,
        CustomException::class
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
     * Report or log an exception.
     * @param Exception $exception
     * @return mixed|void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     * @param \Illuminate\Http\Request $request
     * @param Exception $exception
     * @return \Illuminate\Http\JsonResponse|Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception)
    {
        // Get exception message
        $message = $exception->getMessage();

        if ($exception instanceof CustomException) {
            return $exception->render($request, $exception);
        }


        if (\Illuminate\Support\Facades\Request::ajax()) {
            if ($exception instanceof ValidationException)  {
                $errors = $exception->errors();
                $error = array_shift($errors);
                return \Illuminate\Support\Facades\Response::json([
                    'status' => false,
                    'message' => $error[0]
                ]);
            } else if ($exception instanceof PostTooLargeException) {
                return \Illuminate\Support\Facades\Response::json([
                    'status' => false,
                    'message' => 'File size limit exceeded'
                ]);
            } else if($exception instanceof NotFoundHttpException) {
                return \Illuminate\Support\Facades\Response::json([
                    'status' => false,
                    'message' => 'Route not found'
                ]);
            } else if ($exception instanceof AuthenticationException) {
                return \Illuminate\Support\Facades\Response::json([
                    'status' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            } else if ($exception instanceof AccessDeniedException) {
                return \Illuminate\Support\Facades\Response::json([
                    'status' => false,
                    'message' => 'This action is unauthorized'
                ], 403);
            } else if ($exception instanceof AuthorizationException) {
                return \Illuminate\Support\Facades\Response::json([
                    'status' => false,
                    'message' => 'This action is unauthorized'
                ], 403);
            }  else if ($exception instanceof \Exception) {
                return \Illuminate\Support\Facades\Response::json([
                    'status' => false,
                    'message' => $message
                ]);
            }

            return Response::json([
                'status' => false,
                'message' => $message
            ]);
        }

        return parent::render($request, $exception);
    }
}
