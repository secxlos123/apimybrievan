<?php

namespace App\Http\Middleware;

use Closure;

class FileMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $ip = env('ACCESS_CLAS_IP','127.0.0.1');
        $access = env('ACCESS_FILE', '10.35.65.156');
        $header = $request->header('Authorization');
        $device = $request->header('Device-Id') ? $request->header('Device-Id') : "NULL";
        \Log::info("=================ACCESS_CLAS_IP============");
        \Log::info($ip);
        \Log::info("=================CLIENT_IP============");
        \Log::info($request->ip());
        \Log::info("=================DEVICE-ID============");
        \Log::info($device);
        \Log::info("=================HOST============");
        \Log::info($request->server());
        \Log::info("=================ACCESS_FILE============");
        \Log::info($access);
        \Log::info("=================Authorization============");
        \Log::info($header);
        if($device == "NULL"){
            if ($request->ip() !== $ip) {
                \Log::info("============IP-FALSE-CLOSE-ACCESS===================");
                return response()->json(['error' => 401, 'message' => 'Unauthorized action. <Permision Denied>'], 401);
                // chmod(public_path('uploads'), 0644);
            }
            // else if(!$device){
            //     \Log::info("============DEVICE-FALSE===================");
            //     // return response()->json(['error' => 401, 'message' => 'Unauthorized action. <Permision Denied>'], 401);
            //     chmod(public_path('uploads'), 0644);
            // }
            else{
            \Log::info("=============SUCCESS-OPEN-ACCESS-VIA-IP-ADDRESS=====================");
            // chmod(public_path('uploads'), 0755);
                return $next($request);    
            }
        }else{
            \Log::info("=============SUCCESS-OPEN-ACCESS-VIA-DEVICE-ID=====================");
            // chmod(public_path('uploads'), 0755);
            return $next($request);
        }
        //return $next($request);
    }
}
