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
            ->where( function($marketing) use($request){
              if ($request->header('role') != 'fo') {
                if($request->has('region')){
                  if($request->input('branch')=='all' || $request->input('branch')==''){
                    $marketing->whereIn('branch', $request->input('list_branch'));
                  }else{
                    $marketing->where('branch', $request->input('branch'));
                  }
                }
                if($request->has('pn')){
                  $marketing->where( 'marketings.pn', '=', $request->input( 'pn' ) );
                }
              }else{
                $marketing->where( 'marketings.pn', '=', $request->header( 'pn' ) );
              }
            })
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
              if($request->has('activity_type')){
                $marketing->where('marketings.activity_type', '=', $request->input('activity_type'));
              }
              if($request->has('pn')){
                $marketing->where('marketings.pn', '=', $request->input('pn'));
              }
            })
            ;
    }
}
