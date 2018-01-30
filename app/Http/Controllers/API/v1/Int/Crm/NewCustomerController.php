<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\Crm\Marketing\CreateRequest;
// use App\Http\Request\API\v1\Crm\Marketing\UpdateRequest;
use App\Models\Crm\Marketing;
use App\Models\Crm\MarketingNote;
use App\Models\Crm\MarketingActivity;
use App\Models\Crm\rescheduleActivity;
use App\Models\Crm\MarketingActivityFollowup;
use App\Models\Crm\ActivityType;
use App\Models\Crm\ProductType;
use App\Models\Crm\Status;
use App\Models\Crm\CrmNewCustomer;
use App\Models\User;

use RestwsHc;

class NewCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $pn = $request->header('pn');
      $branch = $request->header('branch');
      $auth = $request->header('Authorization');

      $newCustomer = CrmNewCustomer::where('pn', $pn)->get();

      return response()->success( [
          'message' => 'Sukses get New Customer',
          'contents' => $newCustomer
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $customers = User::getCustomers( $request )->get();
        $data['product_type'] = ProductType::all();
        $data['activity_type'] = ActivityType::all();
        $data['status'] = Status::all();
        $data['accounts'] = $customers;

        return response()->success( [
    			'message' => 'Sukses',
    			'contents' => $data
    		], 200 );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $data['pn'] = $request->header('pn');
      $data['branch'] = $request->header('branch');
      $data['name'] = $request['nama'];
      $data['nik'] = $request['nik'];
      $data['email'] = $request['email'];
      $data['phone'] = $request['telp'];
      $data['address'] = $request['alamat'];

      if (CrmNewCustomer::where('email', '=', $request['email'])->exists()) {
        return response()->error([
            'message' => 'Email Telah Digunakan',
        ], 500);
      }

      if (CrmNewCustomer::where('nik', '=', $request['nik'])->exists()) {
        return response()->error([
            'message' => 'NIK Telah Digunakan',
        ], 500);
      }

      $save = CrmNewCustomer::create($data);
      if ($save) {
          return response()->success([
              'message' => 'Customer Baru berhasil ditambah.',
              'contents' => collect($save),
          ], 201);
      }

      return response()->error([
          'message' => 'Data Customer Baru Tidak Dapat Ditambah.',
      ], 500);
    }

    public function show($id)
    {
        $marketing = Marketing::find($id);
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $marketing
          ]);
    }

    public function update(Request $request, $id)
    {
      $data = Marketing::find($id);

      $update['pn'] = $request->header('pn');
      $update['product_type'] = $request['product_type'];
      $update['activity_type'] = $request['activity_type'];
      $update['target'] = $request['target'];
      $update['account_id'] = $request['account_id'];
      $update['nama'] = $request['nama'];
      $update['nik'] = $request['nik'];
      $update['cif'] = $request['cif'];
      $update['status'] = $request['status'];
      $update['target_closing_date'] = $request['target_closing_date'];

      // return $request->only($postTaken);die();
      if($data) {
        $data->update($update);

        return response()->success([
          'message' => 'Data Marketing berhasil diupdate.',
          'contents' => Marketing::find($id),
        ], 201);
      }

      return response()->error([
          'message' => 'Data Marketing Tidak Dapat Diupdate.',
      ], 500);
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

}
