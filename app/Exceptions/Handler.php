<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Exceptions\ApiAuthorizationException;
use Illuminate\Support\Facades\Mail;

use DB;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class
        , \Illuminate\Auth\Access\AuthorizationException::class
        , \Symfony\Component\HttpKernel\Exception\HttpException::class
        , \Illuminate\Database\Eloquent\ModelNotFoundException::class
        , \Illuminate\Session\TokenMismatchException::class
        , \Illuminate\Validation\ValidationException::class
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report( Exception $exception )
    {
        parent::report( $exception );
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render( $request, Exception $exception )
    {
        // send email if error
        $applicationPosition = ENV( "APPLICATION_POSITION", "local" );
        if ( $applicationPosition == "production" ) {
            if ( ! $exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException ) {
                Mail::send( "mails.ErrorException", array( "exception" => $exception ), function( $message )
                    {
                        $message->subject(ENV("APPLICATION_POSITION", "development") . " API myBRI Error Exception");
                        $message->from("error@mybri.bri.co.id", "Error Exception");
                        $message->to("rangga.darmajati@wgs.co.id");
                    }
                );
            }
        }

        \DB::rollback();
        if( $exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ) {
            return response()->error( [ "message" => "Data tidak ditemukan" ], 404 );

        } else if( $exception instanceof \App\Exceptions\BRIServiceException ) {
            return response()->error( [ "message" => "BRI Request error" ], 404 );

        } else if( $exception instanceof ApiAuthorizationException ) {
            return $exception->render( $request );

        } else if( $exception instanceof \Swift_TransportException ) {
            return response()->error( [ "message" => "Gagal mengirim email" ], 404 );

        } else if ($exception instanceof NotFoundHttpException) {
            return response()->error( [ "message" => "Url Tidak Valid" ], 404 );

        } else if ($exception instanceof \GuzzleHttp\Exception\ConnectException) {
            return response()->error( [ "message" => "Request Time Out" ], 408 );

        }

        return parent::render( $request, $exception );
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated( $request, AuthenticationException $exception )
    {
        if ( $request->expectsJson() ) {
            return response()->json( [ "error" => "Unauthenticated." ], 401 );
        }

        return redirect()->guest( "login" );
    }
}
