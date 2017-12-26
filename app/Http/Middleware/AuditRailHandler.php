<?php

namespace App\Http\Middleware;

use App\Models\Audit;
use Closure;

class AuditRailHandler
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $path = str_replace("api/v1/int/", "", $request->path());
        $exclude = array("password/reset", "check-token");

        if ( !in_array($path, $exclude) ) {
            \Log::info("-------create audit log-------------");

            $extraParams = array(
                'longitude' => number_format($request->header('long', env('DEF_LONG', '106.81350')), 5)
                , 'latitude' => number_format($request->header('lat', env('DEF_LAT', '-6.21670')), 5)
            );

            Audit::create([
                'user_id' => $request->header('pn')
                , 'event' => 'action-log'
                , 'auditable_id' => $request->header('pn')
                , 'auditable_type' => Audit::class
                , 'old_values' => (object) array()
                , 'new_values' => (object) array()
                , 'url' => $request->fullUrl()
                , 'ip_address' => $request->ip()
                , 'user_agent' => $request->header('User-Agent')
                , 'extra_params' => json_encode($extraParams)
                , 'action' => $request->header('auditaction', 'Undefined Action')
            ]);
        }
        return $next($request);
    }
}
