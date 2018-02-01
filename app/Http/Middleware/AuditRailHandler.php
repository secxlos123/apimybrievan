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

        if ( !in_array($path, $exclude) && (strpos($request->path(), 'v1/eks') || strpos($request->path(), 'v1/int')) ) {
            \Log::info("-------create audit log-------------");
            $extraParams = array(
                'longitude' => number_format($request->header('long', env('DEF_LONG', '106.81350')), 5)
                , 'latitude' => number_format($request->header('lat', env('DEF_LAT', '-6.21670')), 5)
            );
            $action = $request->header('auditaction', 'Undefined Action');
            $auditable_id = $request->header('pn', 0);
            
            if($action !='Undefined Action'){
                if($action=='Verifikasi Data Nasabah' || $action == 'pengajuan kredit'){
                        $auditable_type = 'app\models\eform ';
                        if(!empty($request['eform_id'])){
                            $auditable_id = $request['eform_id'];
                        }
                }
                Audit::create([
                    'user_id' => $request->header('pn', 0)
                    , 'event' => 'action-log'
                    , 'auditable_id' =>$auditable_id
                    , 'auditable_type' => Audit::class
                    , 'old_values' => (object) array()
                    , 'new_values' => $request->except('long','lat','password','_method')
                    , 'url' => $request->fullUrl()
                    , 'ip_address' => $request->ip()
                    , 'user_agent' => $request->header('User-Agent')
                    , 'extra_params' => json_encode($extraParams)
                    , 'action' => $request->header('auditaction', 'Undefined Action')
                ]);
            }
        }
        return $next($request);
    }
}
