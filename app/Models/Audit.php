<?php
/**
 * This file is part of the Laravel Auditing package.
 *
 * @author     Antério Vieira <anteriovieira@gmail.com>
 * @author     Quetzy Garcia  <quetzyg@altek.org>
 * @author     Raphael França <raphaelfrancabsb@gmail.com>
 * @copyright  2015-2017
 *
 * For the full copyright and license information,
 * please view the LICENSE.md file that was distributed
 * with this source code.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Audit as AuditTrait;
use OwenIt\Auditing\Contracts\Audit as AuditContract;
use Illuminate\Http\Request;
use DB;

class Audit extends Model implements AuditContract
{
    use AuditTrait;

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
    ];

    /**
     * Scope a query to get lists of roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetLists($query, Request $request)
    {

        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['id', 'asc'];

        return $query
                ->from('auditrail_type_one')
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for search by field search ref_number.
                *
                * @param $request->search
                * @return \Illuminate\Database\Eloquent\Builder
                */ 

                  $eform = 'app\\models\\eform';
                  
                  if ($request->has('search')){
                        $auditrail->where(\DB::raw('LOWER(ref_number)'), 'like', '%'.strtolower($request->input('search')).'%');
                        $auditrail->where('auditable_type', $eform);
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by tanggal aksi.
                *
                * @param $request->created_at
                * @return \Illuminate\Database\Eloquent\Builder
                */

                   $eform = 'app\\models\\eform';

                    if ($request->has('created_at')){
                        $auditrail->where(\DB::raw('DATE(created_at)'), $request->input('created_at'));
                        $auditrail->where('auditable_type', $eform);
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama User.
                *
                * @param $request->username
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    $eform = 'app\\models\\eform';

                    if($request->has('username')){
                        $auditrail->where(\DB::raw('LOWER(username)'), 'like', '%'.strtolower($request->input('username')).'%');
                        $auditrail->where('auditable_type', $eform);
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama Modul.
                *
                * @param $request->modul_name
                * @return \Illuminate\Database\Eloquent\Builder
                */
                    $eform = 'app\\models\\eform';

                    if($request->has('modul_name')){
                        $auditrail->where(\DB::raw('LOWER(modul_name)'), 'like', '%'.strtolower($request->input('modul_name')).'%');
                        $auditrail->where('auditable_type', $eform);
                    }
                })
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for search by ref_number.
                *
                * @param $request->ref_number
                * @return \Illuminate\Database\Eloquent\Builder
                */ 
                    $eform = 'app\\models\\eform';

                  if ($request->has('ref_number')){
                        $auditrail->where(\DB::raw('LOWER(ref_number)'), 'like', '%'.strtolower($request->input('ref_number')).'%');
                        $auditrail->where('auditable_type', $eform);
                    }
                })
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for Auditrail Pengajuan Kredit
                */
                 $eform = 'app\\models\\eform';
                 $event = 'created';

                 $auditrail->where('auditable_type', $eform);
                 $auditrail->where('event', $event);
                })
                ->orderBy($sort[0], $sort[1]);
            
    }

    /**
     * Scope a query to get lists of roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetListsAppointment($query, Request $request)
    {

        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['id', 'asc'];

        return $query
                ->from('auditrail_appointment')
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by tanggal aksi.
                *
                * @param $request->created_at
                * @return \Illuminate\Database\Eloquent\Builder
                */

                   $appointment = 'app\models\appointment';

                    if ($request->has('created_at')){
                        $auditrail->where(\DB::raw('DATE(created_at)'), $request->input('created_at'));
                        $auditrail->where('auditable_type', $appointment);
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama User.
                *
                * @param $request->username
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    $appointment = 'app\models\appointment';

                    if($request->has('username')){
                        $auditrail->where(\DB::raw('LOWER(username)'), 'like', '%'.strtolower($request->input('username')).'%');
                        $auditrail->where('auditable_type', $appointment);
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama Modul.
                *
                * @param $request->modul_name
                * @return \Illuminate\Database\Eloquent\Builder
                */
                    $appointment = 'app\models\appointment';

                    if($request->has('modul_name')){
                        $auditrail->where(\DB::raw('LOWER(modul_name)'), 'like', '%'.strtolower($request->input('modul_name')).'%');
                        $auditrail->where('auditable_type', $appointment);
                    }
                })
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for search by ref_number.
                *
                * @param $request->ref_number
                * @return \Illuminate\Database\Eloquent\Builder
                */ 
                     $appointment = 'app\models\appointment';

                  if ($request->has('ref_number')){
                        $auditrail->where(\DB::raw('LOWER(ref_number)'), 'like', '%'.strtolower($request->input('ref_number')).'%');
                        $auditrail->where('auditable_type', $appointment);
                    }
                })
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for Auditrail Penjadwalan
                */
                 $appointment = 'app\models\appointment';

                 $auditrail->where('auditable_type', $appointment);
               
                })
                ->orderBy($sort[0], $sort[1]);
            
    }
    /*
     save data action dan long lat before save
    */
     public static   function boot(  )
    {
        parent::boot();
        self::created(function($model){
           $request = request();
           $extraParams = array(
                'longitude' => number_format($request->header('long', env('DEF_LONG', '106.81350')), 5)
                , 'latitude' => number_format($request->header('lat', env('DEF_LAT', '-6.21670')), 5)
            );
           $model->extra_params =  json_encode($extraParams);
           $model->action = $request->header('auditaction', 'Undefined Action');
           $model->save();
        });   
    }
}
