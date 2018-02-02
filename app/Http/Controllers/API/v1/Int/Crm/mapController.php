<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Crm\Marketing;
use App\Models\Crm\MarketingActivity;
use App\Models\Crm\MarketingActivityFollowup;
use DB;

class mapController extends Controller
{
    public function mapByActivity(Request $request)
    {
      $from = $request->input('from');
      $to = $request->input('to');

      $activityMaps = MarketingActivity::where('created_at', '>=', $from)->where('created_at', '<=', $to)->get();
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
