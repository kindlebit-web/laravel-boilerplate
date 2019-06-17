<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\FileUnacceptableForCollection;
use Illuminate\Session\TokenMismatchException;

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
     * Report or log an exception.
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if($request->wantsJson())
        {          
          if ($exception instanceof NotFoundHttpException) {

            return response()->json(['success' => false, 'message' => 'Route not found'], 404);

          } elseif($exception instanceof MethodNotAllowedHttpException) {

            return response()->json(['success' => false, 'message' => $request->method() . ' method is not allowed for this endpoint.'], 403);

          } elseif($exception instanceof ModelNotFoundException) {

            return response()->json(['success' => false, 'message' => 'Model not allowed'], 404);

          } elseif($exception instanceof FileUnacceptableForCollection) {

            return response()->json(['success' => false, 'message' => 'This file format is not allowed.'], 403);

          } 
        }

        if ($exception instanceof TokenMismatchException) {
           return redirect()->back()->with('error', 'Your page session has been expired. Please try again.');
        }

        return parent::render($request, $exception);
    }
}
