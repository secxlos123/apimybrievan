<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
    'ref_id',
    'nik',
    'cif',
    'name',
    'phone',
    'address',
    'product_type',
    'officer_ref',
    'status',
    'note',
    'created_by',
    'creator_name',
    'officer_name',
    'branch_id',
    'longitude',
    'latitude',
    'contact_time',
    'intention'
    ];

    public function scopeGetReports($query, Request $request)
    {
      $referralFill = [];
      foreach ($this->fillable as $fillable) {
        $referralFill[] = "referrals.{$fillable}";
      }

      return $query
            ->orderBy('referrals.id', 'asc')
            ->where( function($referral) use($request)
                     {
                        if ($request->header('role') != 'fo') 
                        {
                            if ($request->has('start_date') && $request->has('end_date')) 
                            {
                                $from = date($request->input('start_date') . ' 00:00:00', time());
                                $to = date($request->input('end_date') . ' 23:59:59', time());
                                $referral->whereBetween('created_at', array($from, $to));
                            }
                
                            if($request->has('region'))
                            {
                                if($request->input('branch')=='all' || $request->input('branch')=='')
                                {
                                    $referral->whereIn('branch_id', $request->input('list_branch'));
                                }
                                else
                                {
                                    $referral->where('branch_id', $request->input('branch'));
                                }*/
                            }

                            if($request->has('pn'))
                            {
                                $referral->where( 'referrals.officer_name', '=', $request->input( 'pn' ) );
                            }

                            if($request->has('status'))
                            {
                                $referral->where('referrals.status', $request->input('status'));
                            }
                        }
                        else
                        {
                            $referral->where( 'referrals.officer_name', '=', $request->header( 'pn' ) );
                        }
                      }
                    );

    }


}
