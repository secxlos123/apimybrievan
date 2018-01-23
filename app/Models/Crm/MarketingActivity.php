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
              ->join('marketings', 'marketings.id', '=', 'marketing_activities.marketing_id');
    }

    public function scopeGetReportMarketings($query, Request $request)
    {
      return $query
            ->leftJoin('marketings', 'marketings.id', '=', 'marketing_activities.marketing_id')
            ->where( function($marketing) use($request){
              if($request->has('pn')){
                $marketing->where( 'marketings.pn', '=', $request->input( 'pn' ) );
              }
            });
    }
}
