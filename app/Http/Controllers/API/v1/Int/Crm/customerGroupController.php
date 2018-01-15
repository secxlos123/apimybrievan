<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Crm\CustomerGroup;
use RestwsHc;

class customerGroupController extends Controller
{
    public function index(Request $request)
    {
      $pn = $request->header('pn');
      $branch = $request->header('branch');
      $auth = $request->header('Authorization');
      $pemasar = $this->pemasar($pn,$branch,$auth);

      if ($pemasar != null) {
        $pemasar_name = array_column($pemasar, 'SNAME','PERNR' );
        $list_pn = array_column($pemasar, 'PERNR');
      } else {
        $pemasar_name = [];
        $list_pn =[];
      }

      $customer = CustomerGroup::all();

      if ($customer) {
        return response()->success( [
            'message' => 'Sukses get customer district',
            'contents' => $customer
          ]);
      } else {
        return response()->success( [
            'message' => 'Gagal get customer district',
            'contents' => $customer
          ]);
      }

    }

    public function store(Request $request)
    {
      $pn = $request->header('pn');
      $data['name'] = $request['name'];
      $data['nik'] = $request['nik'];
      $data['cif'] = $request['cif'];
      $data['category'] = $request['category'];
      $data['map_id'] = $request['map_id'];
      $data['created_by'] = $pn;


      $save = CustomerGroup::create($data);
      if ($save) {
          return response()->success([
              'message' => 'Data Customer Group berhasil ditambah.',
              'contents' => collect($save)->merge($request->all()),
          ], 201);
      }

      return response()->error([
          'message' => 'Data Customer Group Tidak Dapat Ditambah.',
      ], 500);
    }

    public function pemasar($pn, $branch, $auth){
      $list_ao = RestwsHc::setBody([
        'request' => json_encode([
          'requestMethod' => 'get_list_tenaga_pemasar',
          'requestData' => [
            'id_user' => $pn,
            'kode_branch' => $branch
          ],
        ])
      ])->setHeaders([
        'Authorization' => $auth
      ])->post('form_params');

      $list_fo = RestwsHc::setBody([
        'request' => json_encode([
          'requestMethod' => 'get_list_fo',
          'requestData' => [
            'id_user' => $pn,
            'kode_branch' => $branch
          ],
        ])
      ])->setHeaders([
        'Authorization' => $auth
      ])->post('form_params');

      $ao = $list_ao['responseData'];
      $fo = $list_fo['responseData'];

      if ($ao != null && $fo != null) {
        $result = array_merge_recursive($fo,$ao);
      } else {
        $result = [];
      }

      return $result;
    }
}
