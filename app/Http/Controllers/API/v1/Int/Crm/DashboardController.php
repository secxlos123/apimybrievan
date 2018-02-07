<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RestwsHc;
use RestwsSm;
use DB;


use App\Models\Crm\RestBrispot;
use App\Models\Crm\ProductType;
use App\Models\Crm\ActivityType;
use App\Models\Crm\Status;
use App\Models\Crm\ObjectActivity;
use App\Models\Crm\ActionActivity;



class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $data['product_type'] = ProductType::all();
      $data['activity_type'] = ActivityType::all();
      // $data['status'] = Status::all();
      foreach (Status::all() as $status) {
          $data['status'][]=[
            'id'=> $status->id,
            'status_name'=>$status->status_name
          ];
      }
      $additional =[
        'id'=>0,
        'status_name'=>'All'
      ];
      array_push($data['status'],$additional);
      // return $data['status'];
      $data['object_activity'] = ObjectActivity::all();
      $data['action_activity'] = ActionActivity::all();

        return response()->success( [
            'message' => 'Sukses',
            'contents' => $data
          ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pemasar(Request $request)
    {
      $list_ao = RestwsHc::setBody([
        'request' => json_encode([
          'requestMethod' => 'get_list_tenaga_pemasar',
          'requestData' => [
            'id_user' => $request->header('pn'),
            'kode_branch' => $request->header('branch')
          ],
        ])
      ])->post('form_params');

      $list_fo = RestwsHc::setBody([
        'request' => json_encode([
          'requestMethod' => 'get_list_fo',
          'requestData' => [
            'id_user' => $request->header('pn'),
            'kode_branch' => $request->header('branch')
          ],
        ])
      ])->post('form_params');

      $ao = $list_ao['responseData'];
      $fo = $list_fo['responseData'];

      if ($ao != null && $fo != null) {
        $result = array_merge_recursive($fo,$ao);
      } else {
        $result = [];
      }

      return response()->success([
        'contents' => $result
      ]);
    }

    public function sales_kit(Request $request)
    {
      // $pn = $request->header('pn');
      // $auth = $request->header('Authorization');
      // $sales_kit = RestwsSm::setBody([
      //   'request' => json_encode([
      //     'requestMethod' => 'salesKit',
      //     'requestData' => [
      //       'user' => $pn,
      //     ],
      //   ])
      // ])->post('form_params');
      //
      // return $sales_kit;

      $db_ext = new RestBrispot;
      $db_ext->setConnection('mysql2');
      $sales_kit = $db_ext->all();
      return $db_ext;die();
      $sales_kit = $db_ext->table('saleskit_sme')->get();
      return response()->success([
        'message' => 'Sukses',
        'contents' => $sales_kit
      ]);
    }

    public function salesKit(Request $request)
    {
      $host = 'http://10.35.65.111';
      $user = 'root';
      $password = 'P@ssw0rd';
      $db = 'brispot';
      $table = 'saleskit_sme';

      $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $password);
      $sql = "select * from sales_kit ";
      $sales_kit = mysql_query($sql);


    }

}
