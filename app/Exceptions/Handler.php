<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    { 
        if ($exception instanceof TokenMismatchException){
            // Redirect to a form. Here is an example of how I handle mine
            return redirect($request->fullUrl());
        }
        elseif($exception instanceOf MethodNotAllowedHttpException) {
            abort(404);
        }
        else if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            return redirect()->back()->withInput($request->except('password'))->with(['message' => 'Validation Token was expired. Please try again','alert-class' => 'alert-danger']);
        }
        else {
            if(request()->wantsJson()) {
                return response()->json([
                    'status' => 'failed','error_message'   =>  __('messages.api.something_went_wrong_try_later'),
                ]);
            }
            // Handle all 500 exceptions
            if(method_exists('getStatusCode', $exception) && $exception->getStatusCode() == 500 && url()->previous() && url()->previous() != url()->current()) {
                return redirect()->back()->with(['message' => __('messages.api.something_went_wrong_try_later'),'alert-class' => 'alert-danger']);
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }

    /**
     * Get the Whoops handler for the application.
     *
     * @return \Whoops\Handler\Handler
     */
    protected function whoopsHandler()
    {
        try {
            return app(\Whoops\Handler\HandlerInterface::class);
        }
        catch (\Illuminate\Contracts\Container\BindingResolutionException $e) {
            return parent::whoopsHandler();
        }
    }
}
