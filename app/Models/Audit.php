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
                ->from('auditrail_type_two')
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for search by field search ref_number.
                *
                * @param $request->search
                * @return \Illuminate\Database\Eloquent\Builder
                */ 

                 // $eform = 'app\\models\\eform';
                  
                  if ($request->has('search')){
                        $auditrail->where(\DB::raw('LOWER(ref_number)'), 'like', '%'.strtolower($request->input('search')).'%');
                       // $auditrail->where('auditable_type', $eform);
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by tanggal aksi.
                *
                * @param $request->created_at
                * @return \Illuminate\Database\Eloquent\Builder
                */

                   //$eform = 'app\\models\\eform';

                    if ($request->has('created_at')){
                        $auditrail->where(\DB::raw('DATE(created_at)'), $request->input('created_at'));
                       // $auditrail->where('auditable_type', $eform);
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama User.
                *
                * @param $request->username
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    //$eform = 'app\\models\\eform';

                    if($request->has('username')){
                        $auditrail->where(\DB::raw('LOWER(username)'), 'like', '%'.strtolower($request->input('username')).'%');
                        //$auditrail->where('auditable_type', $eform);
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama Modul.
                *
                * @param $request->modul_name
                * @return \Illuminate\Database\Eloquent\Builder
                */
                    //$eform = 'app\\models\\eform';

                    if($request->has('modul_name')){
                        $auditrail->where(\DB::raw('LOWER(modul_name)'), 'like', '%'.strtolower($request->input('modul_name')).'%');
                        //$auditrail->where('auditable_type', $eform);
                    }
                })
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for search by ref_number.
                *
                * @param $request->ref_number
                * @return \Illuminate\Database\Eloquent\Builder
                */ 
                    //$eform = 'app\\models\\eform';

                  if ($request->has('ref_number')){
                        $auditrail->where(\DB::raw('LOWER(ref_number)'), 'like', '%'.strtolower($request->input('ref_number')).'%');
                        //$auditrail->where('auditable_type', $eform);
                    }
                })
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for Auditrail Pengajuan Kredit
                */
                 $eform = 'app\\models\\eform';
                 //$eform = 'app\models\eform';
                 //$event = 'created';
 
                 $auditrail->Orwhere(DB::raw('lower(modul_name)'), 'like', '%peng%');
                 $auditrail->Orwhere(DB::raw('lower(modul_name)'), 'like', '%veri%');
                 $auditrail->Orwhere(DB::raw('lower(modul_name)'), 'like', '%lkn%');
                 $auditrail->Orwhere(DB::raw('lower(modul_name)'), 'like', '%leads%');
                 $auditrail->Orwhere(DB::raw('lower(modul_name)'), 'like', '%si kredit%');
                 // $auditrail->Orwhere(DB::raw('lower(modul_name)'), 'not like', '%collateral%');
                })
                // ->orderBy($sort[0], $sort[1]);
                ->orderBy('created_at', 'desc');
            
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
                ->from('auditrail_type_one')
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
                // ->orderBy($sort[0], $sort[1]);
                ->orderBy('created_at', 'desc');
            
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
            \Log::info("-------boot audit -------------");
            \Log::info($model->auditable_type);
           $model->extra_params =  json_encode($extraParams);
           $model->action = $request->header('auditaction', 'Undefined Action');
           $model->save();
        });   
    }

    /**
     * Scope a query to get lists of roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetListsAdmindev($query, Request $request)
    {

        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['id', 'asc'];

        return $query
                ->from('auditrail_admin_developer')
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by tanggal aksi.
                *
                * @param $request->created_at
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    if ($request->has('created_at')){
                        $auditrail->where(\DB::raw('DATE(created_at)'), $request->input('created_at'));
                       
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama User.
                *
                * @param $request->username
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    if($request->has('username')){
                        $auditrail->where(\DB::raw('LOWER(username)'), 'like', '%'.strtolower($request->input('username')).'%');
                  
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama Modul.
                *
                * @param $request->modul_name
                * @return \Illuminate\Database\Eloquent\Builder
                */
               
                    if($request->has('modul_name')){
                        $auditrail->where(\DB::raw('LOWER(modul_name)'), 'like', '%'.strtolower($request->input('modul_name')).'%');
      
                    }
                })
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for search by Nama Perusahaan Mitra.
                *
                * @param $request->company_name
                * @return \Illuminate\Database\Eloquent\Builder
                */ 
              
                  if ($request->has('company_name')){
                        $auditrail->where(\DB::raw('LOWER(company_name)'), 'like', '%'.strtolower($request->input('company_name')).'%');
            
                    }
                })
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for Auditrail Admin Developer
                */

                 $slug = 'developer';
                 $action = 'undefined action';
                 $auditrail->where('role', $slug);
                 $auditrail->where(\DB::raw('LOWER(modul_name)'), '!=', $action);
               
                })
                // ->orderBy($sort[0], $sort[1]);
                ->orderBy('created_at', 'desc');
            }

    /**
     * Scope a query to get lists of roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetListsLogin($query, Request $request)
    {

        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['created_at', 'desc'];

        return $query
                ->from('auditrail_admin_developer')
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by tanggal aksi.
                *
                * @param $request->created_at
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    if ($request->has('created_at')){
                        $auditrail->where(\DB::raw('DATE(created_at)'), $request->input('created_at'));
                       
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama User.
                *
                * @param $request->username
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    if($request->has('username')){
                        $auditrail->where(\DB::raw('LOWER(username)'), 'like', '%'.strtolower($request->input('username')).'%');
                  
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama Modul.
                *
                * @param $request->modul_name
                * @return \Illuminate\Database\Eloquent\Builder
                */
               
                    if($request->has('modul_name')){
                        $auditrail->where(\DB::raw('LOWER(modul_name)'), 'like', '%'.strtolower($request->input('modul_name')).'%');
      
                    }
                })
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for search by Nama Perusahaan Mitra.
                *
                * @param $request->company_name
                * @return \Illuminate\Database\Eloquent\Builder
                */ 
              
                  if ($request->has('company_name')){
                        $auditrail->where(\DB::raw('LOWER(company_name)'), 'like', '%'.strtolower($request->input('company_name')).'%');
            
                    }
                })
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for Auditrail Login
                */

                $auditrail->where(\DB::raw('LOWER(modul_name)'), 'like', '%log%');
               
                })
                ->orderBy('created_at', 'desc');
            }

    /**
     * Scope a query to get lists of roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetListsAgenDev($query, Request $request)
    {

        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['id', 'asc'];

        return $query
                ->from('auditrail_admin_developer')
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by tanggal aksi.
                *
                * @param $request->created_at
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    if ($request->has('created_at')){
                        $auditrail->where(\DB::raw('DATE(created_at)'), $request->input('created_at'));
                       
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama User.
                *
                * @param $request->username
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    if($request->has('username')){
                        $auditrail->where(\DB::raw('LOWER(username)'), 'like', '%'.strtolower($request->input('username')).'%');
                  
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama Modul.
                *
                * @param $request->modul_name
                * @return \Illuminate\Database\Eloquent\Builder
                */
               
                    if($request->has('modul_name')){
                        $auditrail->where(\DB::raw('LOWER(modul_name)'), 'like', '%'.strtolower($request->input('modul_name')).'%');
      
                    }
                })
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for search by Nama Perusahaan Mitra.
                *
                * @param $request->company_name
                * @return \Illuminate\Database\Eloquent\Builder
                */ 
              
                  if ($request->has('company_name')){
                        $auditrail->where(\DB::raw('LOWER(company_name)'), 'like', '%'.strtolower($request->input('company_name')).'%');
            
                    }
                })
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for Auditrail Login
                */

                 $slug = 'developer-sales';
                 $action = 'undefined action';
                 $auditrail->where('role', $slug);
                 $auditrail->where(\DB::raw('LOWER(modul_name)'), '!=', $action);
                 $auditrail->where(\DB::raw('LOWER(modul_name)'), '!=', 'login');
                 $auditrail->where(\DB::raw('LOWER(modul_name)'), '!=', 'logout');
               
                })
                // ->orderBy($sort[0], $sort[1]);
                ->orderBy('created_at', 'desc');
            }

            /**
     * Scope a query to get lists of roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetListsEdit($query, Request $request)
    {

        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['id', 'asc'];

        return $query
                ->from('auditrail_admin_developer')
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by tanggal aksi.
                *
                * @param $request->created_at
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    if ($request->has('created_at')){
                        $auditrail->where(\DB::raw('DATE(created_at)'), $request->input('created_at'));
                       
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama User.
                *
                * @param $request->username
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    if($request->has('username')){
                        $auditrail->where(\DB::raw('LOWER(username)'), 'like', '%'.strtolower($request->input('username')).'%');
                  
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama Modul.
                *
                * @param $request->modul_name
                * @return \Illuminate\Database\Eloquent\Builder
                */
               
                    if($request->has('modul_name')){
                        $auditrail->where(\DB::raw('LOWER(modul_name)'), 'like', '%'.strtolower($request->input('modul_name')).'%');
      
                    }
                })
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for search by Nama Perusahaan Mitra.
                *
                * @param $request->company_name
                * @return \Illuminate\Database\Eloquent\Builder
                */ 
              
                  if ($request->has('company_name')){
                        $auditrail->where(\DB::raw('LOWER(company_name)'), 'like', '%'.strtolower($request->input('company_name')).'%');
            
                    }
                })
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for Auditrail Login
                */

                 $user = 'app\models\user';

                 $auditrail->where('auditable_type', $user);
                 $auditrail->where(\DB::raw('LOWER(modul_name)'), 'like', '%p%');
                 $auditrail->where(\DB::raw('LOWER(modul_name)'), 'like', '%u%');
                 $auditrail->where(\DB::raw('LOWER(modul_name)'), 'not like', '%pengajuan%');
                 $auditrail->where(\DB::raw('LOWER(modul_name)'), 'not like', '%tambah unit property%');
               
                })
                // ->orderBy($sort[0], $sort[1]);
                ->orderBy('created_at', 'desc');
            }

            /**
     * Scope a query to get lists of roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetListsCollateral($query, Request $request)
    {

        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['id', 'asc'];

        return $query
                ->from('auditrail_collaterals')
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by tanggal aksi.
                *
                * @param $request->created_at
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    if ($request->has('created_at')){
                        $auditrail->where(\DB::raw('DATE(created_at)'), $request->input('created_at'));
                       
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama User.
                *
                * @param $request->username
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    if($request->has('username')){
                        $auditrail->where(\DB::raw('LOWER(username)'), 'like', '%'.strtolower($request->input('username')).'%');
                  
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama Modul.
                *
                * @param $request->modul_name
                * @return \Illuminate\Database\Eloquent\Builder
                */
               
                    if($request->has('modul_name')){
                        $auditrail->where(\DB::raw('LOWER(modul_name)'), 'like', '%'.strtolower($request->input('modul_name')).'%');
      
                    }
                })
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for search by Nama Perusahaan Mitra.
                *
                * @param $request->developer
                * @return \Illuminate\Database\Eloquent\Builder
                */ 
              
                  if ($request->has('developer')){
                        $auditrail->where(\DB::raw('LOWER(developer)'), 'like', '%'.strtolower($request->input('developer')).'%');
            
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama User.
                *
                * @param $request->staff_penilai
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    if($request->has('staff_penilai')){
                        $auditrail->where(\DB::raw('LOWER(staff_penilai)'), 'like', '%'.strtolower($request->input('staff_penilai')).'%');
                  
                    }
                })
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for Auditrail Login
                */
               // $auditrail->where(\DB::raw('LOWER(auditable_type)'), 'like', '%collateral%');
                 $auditrail->Orwhere(\DB::raw('LOWER(modul_name)'), 'like', '%agu%');
                 $auditrail->Orwhere(\DB::raw('LOWER(modul_name)'), 'like', '%col%');
                 $auditrail->Orwhere(\DB::raw('LOWER(modul_name)'), 'like', '%ung%');

               
                })
                // ->orderBy($sort[0], $sort[1]);
                ->orderBy('created_at', 'desc');
            }

            /**
     * Scope a query to get lists of roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetListsProperty($query, Request $request)
    {

        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['id', 'asc'];

        return $query
                ->from('auditrail_admin_developer')
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by tanggal aksi.
                *
                * @param $request->created_at
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    if ($request->has('created_at')){
                        $auditrail->where(\DB::raw('DATE(created_at)'), $request->input('created_at'));
                       
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama User.
                *
                * @param $request->username
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    if($request->has('username')){
                        $auditrail->where(\DB::raw('LOWER(username)'), 'like', '%'.strtolower($request->input('username')).'%');
                  
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama Proyek.
                *
                * @param $request->project_name
                * @return \Illuminate\Database\Eloquent\Builder
                */
               
                    if($request->has('project_name')){
                        $auditrail->where(\DB::raw('LOWER(project_name)'), 'like', '%'.strtolower($request->input('project_name')).'%');
      
                    }
                })
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for search by Nama Perusahaan Mitra.
                *
                * @param $request->developer
                * @return \Illuminate\Database\Eloquent\Builder
                */ 
              
                  if ($request->has('developer')){
                        $auditrail->where(\DB::raw('LOWER(developer)'), 'like', '%'.strtolower($request->input('developer')).'%');
            
                    }
                })
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for Auditrail Login
                */
                 $auditrail->where(\DB::raw('LOWER(auditable_type)'), 'like', '%property%');
                 $auditrail->where(\DB::raw('LOWER(modul_name)'), 'not like', '%undefined action%');
                 $auditrail->where(\DB::raw('LOWER(modul_name)'), 'not like', '%pengajuan%');
               
                })
                // ->orderBy($sort[0], $sort[1]);
                ->orderBy('created_at', 'desc');
            }

    public function getuser(Request $request)
    {
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['id', 'asc'];
        $useractivity = \DB::table('auditrail_admin_developer')
                            ->select('user_id', 'username')
                            ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama User.
                *
                * @param $request->username
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    if($request->has('username')){
                        $auditrail->where(\DB::raw('LOWER(username)'), 'like', '%'.strtolower($request->input('username')).'%');
                  
                    }
                })
                            ->whereNotNull('username')
                            ->groupBy('user_id', 'username');
                            //->orderBy($sort[0], $sort[1]);              
                            
        return $useractivity;
    }

        /**
     * Scope a query to get list detail activity.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetListsDetailActivity($query, Request $request, $id)
    {


        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['id', 'asc'];


        return $query
                ->from('auditrail_admin_developer')
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by tanggal aksi.
                *
                * @param $request->created_at
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    if ($request->has('created_at')){
                        $auditrail->where(\DB::raw('DATE(created_at)'), $request->input('created_at'));
                       
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama User.
                *
                * @param $request->username
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    if($request->has('username')){
                        $auditrail->where(\DB::raw('LOWER(username)'), 'like', '%'.strtolower($request->input('username')).'%');
                  
                    }
                })
                ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama Modul.
                *
                * @param $request->modul_name
                * @return \Illuminate\Database\Eloquent\Builder
                */
               
                    if($request->has('modul_name')){
                        $auditrail->where(\DB::raw('LOWER(modul_name)'), 'like', '%'.strtolower($request->input('modul_name')).'%');
      
                    }
                })
                ->where(function ($auditrail) use (&$request, &$query){
                /**
                * This query for search by Nama Perusahaan Mitra.
                *
                * @param $request->company_name
                * @return \Illuminate\Database\Eloquent\Builder
                */ 
              
                if ($request->has('user_id')){
                        $auditrail->where('user_id',$request->input('user_id'));
            
                    }
                })
                ->where(function ($auditrail) use (&$request, &$query,&$id){
                /**
                * This query for Auditrail Admin Developer
                */

                 $action = 'undefined action';
                 $auditrail->where(\DB::raw('LOWER(modul_name)'), '!=', $action);
                 $auditrail->where('user_id', '=', $id);
               
                })
                // ->orderBy($sort[0], $sort[1]);
                ->orderBy('created_at', 'desc');
  }
}
