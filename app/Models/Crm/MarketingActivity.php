<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class MarketingActivity extends Model
{
    protected $fillable = [
      'pn',
      'object_activity',
      'action_activity',
      'start_date',
      'end_date',
      'longitude',
      'latitude',
      'address',
      'marketing_id',
      'pn_join',
      'desc'
    ];

    protected $hidden = ['created_at','updated_at'];

    public function marketing()
    {
      return $this->belongsTo('App\Models\Crm\Marketing');
    }

    public function reSchedule()
    {
      return $this->hasMany('App\Models\Crm\rescheduleActivity');
    }

    public function fu_result()
    {
      return $this->belongsTo('App\Models\Crm\MarketingActivityFollowup');
    }

    public function scopeGetReports($query, Request $request)
    {
      $userFill = [];
      foreach ($this->fillable as $fillable) {
          $userFill[] = "marketing_activities.{$fillable}";
      }


      return $query
              ->orderBy('marketing_activities.id', 'asc')
              ->join('marketings', 'marketings.id', '=', 'marketing_activities.marketing_id')
              ->where('marketing_activities.desc', '!=', 'first')
              ->select('marketing_activities.id', 'marketing_activities.pn', 'marketings.branch', 'marketing_activities.object_activity', 'marketing_activities.action_activity', 'marketing_activities.start_date', 'marketing_activities.end_date', 'marketing_activities.address', 'marketings.activity_type', 'marketings.nama')

              ->where( function($activities) use($request){
                if ($request->header('role') != 'fo') {
                  if($request->has('region')){
                   if($request->input('branch')=='all' || $request->input('branch')==''){
                     $activities->whereIn('marketings.branch', $request->input('list_branch'));
                   }else{
                     $activities->where('marketings.branch', $request->input('branch'));
                   }
                   if($request->has('pn')){
                     $activities->where( 'marketings.pn', '=', $request->input( 'pn' ) );
                   }
                  }
                }else{
                  $activities->where( 'marketings.pn', '=', $request->header( 'pn' ) );
                }
              })
              ;
    }
}
