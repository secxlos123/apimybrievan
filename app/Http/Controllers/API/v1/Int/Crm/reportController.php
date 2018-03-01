<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Crm\Marketing;
use App\Models\Crm\MarketingActivity;
use App\Models\Crm\MarketingActivityFollowup;
use DB;

class reportController extends Controller
{
    public function report_marketings(Request $request)
    {
      $limit = $request->input( 'limit' ) ?: 10;
      // $marketings = Marketing::getReports($request)->paginate($limit);
      $data = Marketing::getReports($request)->get();
      $marketings = [];
      foreach ($data as $key => $value) {
        $last_activity = MarketingActivity::where('desc', '!=', 'first')->where('marketing_id',$value->id)->orderBy('created_at', 'desc')->first();
        $result = MarketingActivityFollowup::where('activity_id',$last_activity['id'])->orderBy('created_at', 'desc')->first();
        $marketings[] =[
          "pn"=>$value->pn,
          "bulan"=>date('M',strtotime($value->created_at)),
          "tahun"=>date('Y',strtotime($value->created_at)),
          "wilayah"=> $value->branch,
          "cabang"=> $value->branch,
          "uker"=> $value->branch,
          "fo_name"=> $value->pn,
          "product_type"=> $value->product_type,
          "activity_type"=> $value->activity_type,
          "nama"=> $value->nama,
          "target"=> $value->target,
          "rekening" => $result['account_number'],
          "volume_rekening" => $result['amount'],
          "status"=> $value->status,
          "target_closing_date"=> $value->target_closing_date,
          "result" => $result['fu_result'],
          "activity" => $last_activity
          // "nik"=> $value->nik,
          // "number"=> $value->number,
          // "cif"=> $value->cif,
          // "ref_id"=> $value->ref_id,
          // "branch"=> $value->branch
        ];
      }
      return response()->success( [
        'message' => 'Sukses get list report marketings',
        'contents' => $marketings
      ], 200 );

    }

    public function report_activities(Request $request)
    {
      // $limit = $request->input( 'limit' ) ?: 10;
      // $activities = MarketingActivity::getReportActivities($request)->paginate($limit);
      // return response()->success( [
      //   'message' => 'Sukses get list report Activities',
      //   'contents' => $activities
      // ], 200 );
    }
}
