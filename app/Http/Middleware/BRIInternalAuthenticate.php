<?php

namespace App\Http\Middleware;

use Closure;

class BRIInternalAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle( $request, Closure $next )
    {
        $token = $request->header( 'Authorization' );
        $pn = $request->header( 'pn' );
        if( ! empty( $token ) && ! empty( $token ) ) {
            $check_token = \RestwsHc::setHeaders( [
                'Authorization' => $token
            ] )->setBody( [
                'request' => json_encode( [
                'requestMethod' => 'is_session_valid',
                'requestData' => [
                    'user' => $pn
                    ]
                ] )
            ] )->post( 'form_params' );
            if( $check_token[ 'responseCode' ] == '00' ) {
                return $next( $request );
            } else if( $check_token[ 'responseCode' ] == '99' ) {
                return response()->success( [
                    'message' => 'Token telah di refresh',
                    'contents' => [
                        'refreshed' => true,
                        'token' => 'Bearer ' . $check_token[ 'responseData' ]
                    ]
                ], 200 );
            } else {
                return response()->error( [
                    'message' => 'Session tidak ditemukan',
                    'contents' => []
                ], 404 );
            }
        } else {
            return response()->error( [
                'message' => 'Terlarang',
                'contents' => []
            ], 403 );
        }
    }
}
