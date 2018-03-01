<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ThrottleRequests as BaseReq;


class ThrottleCustom extends BaseReq
{
    /**
     * Create a 'too many attempts' response.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function buildResponse($key, $maxAttempts)
    {

        $retryAfter = $this->limiter->availableIn($key);

        return response()->error( [
                'message' => 'Anda Telah Mencoba Login Sebanyak '.$maxAttempts.',Silahkan Tunggu Beberapa Saat Untuk Login'
            ], 422 );
    }
}
