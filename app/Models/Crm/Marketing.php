<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Marketing extends Model
{
  /**
   * Fields that can be mass assigned.
   *
   * @var array
   */
    protected $fillable = [
      'pn',
      'branch',
      'product_type',
      'activity_type',
      'target',
      'account_id',
      'number',
      'nik',
      'cif',
      'nama',
      'status',
      'target_closing_date'
    ];

  /**
   * Fields that can be mass assigned.
   *
   * @var array
   */
    protected $hidden = ['created_at', 'updated_at'];


    public function activity()
    {
      return $this->hasMany('App\Models\Crm\MarketingActivity');
    }

    public function followUp()
    {
      return $this->hasOne('App\Models\Crm\MarketingActivityFollowup');
    }

    public function scopeGetReports($query, Request $request)
    {
      $marketingFill = [];
      foreach ($this->fillable as $fillable) {
        $marketingFill[] = "marketings.{$fillable}";
      }

      return $query
            ->orderBy('marketings.id', 'asc')
            // ->leftJoin('marketing_activities', 'marketing_activities.marketing_id', '=', 'marketings.id')
            // ->where('desc', '!=', 'first')
            // ->leftJoin('marketing_activity_followups')
            ->where( function($marketing) use($request){
              if($request->has('pn')){
                $marketing->where( 'marketings.pn', '=', $request->input( 'pn' ) );
              }
              if($request->has('period_start') && $request->has('period_end')){
                $marketing->whereBetwen('created_at',[$request->input( 'period_start' ), $request->input( 'period_end' )]);
              }
            })
            // ->select(array_merge([
            //   'marketings.*','marketing_activities.pn','marketing_activities.pn_join','marketing_activities.desc','marketing_activities.start_date'
            // ]))
            ;
    }

    public function scopeGetMarketingSummary($query, Request $request)
    {
      $summary_marketing = [];
      return $query
            ->where(function($marketing) use($request){
              if($request->has('month')){
                $marketing->whereMonth('marketings.created_at', '=', $request->input('month'));
              }
              if($request->has('product_type')){
                $marketing->where('marketings.product_type', '=', $request->input('product_type'));
              }
              if($request->has('pn')){
                $marketing->where('marketings.pn', '=', $request->input('pn'));
              }
            })
            ;
    }
}
