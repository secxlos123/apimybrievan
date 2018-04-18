<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


class PhoneDuration extends Model
{
    protected $table = 'marketing_activity_phone_durations';
    protected $fillable = [
      'pn',
      'nik',
      'cif',
      'phone_number',
      'duration'
    ];

    public function scopeGetListDurationByCustomer($query, Request $request)
    {
      return $query
            ->where(function($phoneActivity) use($request){
              if ($request->has('nik')) {
                $phoneActivity->where('nik', $request['nik']);
              }
              if ($request->has('cif')) {
                $phoneActivity->where('cif', $request['cif']);
              }
            });
    }
}
