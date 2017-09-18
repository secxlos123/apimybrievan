<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;

class GetUserFromToken extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        if( $request->segment( 3 ) == 'int' ) {
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
        } else {
            if (! $token = $this->auth->setRequest($request)->getToken()) {
                return $this->respond('tymon.jwt.absent', 'token_not_provided', 400);
            }

            try {
                $user = $this->auth->authenticate($token);
            } catch (TokenExpiredException $e) {
                return $this->respond('tymon.jwt.expired', 'token_expired', $e->getStatusCode(), [$e]);
            } catch (JWTException $e) {
                return $this->respond('tymon.jwt.invalid', 'token_invalid', $e->getStatusCode(), [$e]);
            }

            if (! $user) {
                return $this->respond('tymon.jwt.user_not_found', 'user_not_found', 404);
            }

            $this->events->fire('tymon.jwt.valid', $user);

            return $next($request);
        }
    }
}
