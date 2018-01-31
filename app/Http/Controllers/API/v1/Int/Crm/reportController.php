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
      $marketings = Marketing::getReports($request)->paginate($limit);
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
