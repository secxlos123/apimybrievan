<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class MarketingDeleteRequest extends Model
{
    protected $table = 'marketing_delete_requests';

    protected $fillable = ['pn','branch','marketing_id','deleted'];

    public function scopeGetRequestDelete($query, Request $request)
    {
      return $query
            ->orderBy('marketing_delete_requests.created_at', 'asc')
            ->where( function($deleteRequest) use($request){
              $deleteRequest->where( 'marketing_delete_requests.branch', '=', $request->header( 'branch' ) );
              $deleteRequest->where( 'marketing_delete_requests.status', '=', 'req' );
            })
            ;
    }
}
