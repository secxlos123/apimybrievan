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
              ->leftJoin('marketing_activity_followups', 'marketing_activity_followups.activity_id', '=', 'marketing_activities.id')
              ->where('marketing_activities.desc', '!=', 'first')
              ->select('marketing_activities.id', 'marketing_activities.pn', 'marketings.branch', 'marketing_activities.object_activity', 'marketing_activities.action_activity', 'marketing_activities.start_date', 'marketing_activities.end_date', 'marketing_activities.address', 'marketings.activity_type', 'marketings.nama', 'marketing_activity_followups.fu_result', 'marketing_activity_followups.desc', 'marketing_activity_followups.account_number', 'marketing_activity_followups.amount' , 'marketing_activity_followups.created_at')

              ->where( function($activities) use($request){
                if ($request->header('role') != 'fo') {
                  if ($request->has('start_date') && $request->has('end_date')) {
                    $from = date($request->input('start_date') . ' 00:00:00', time());
                    $to = date($request->input('end_date') . ' 23:59:59', time());
                    $activities->whereBetween('marketing_activities.created_at', array($from, $to));
                  }
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
