<?php

namespace App\Http\Controllers\API\v1\Int;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Mitra;
use App\Models\Mitra3;
use Sentinel;
use DB;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Brispot;
use Cache;
use App\Models\Crm\apiPdmToken;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class SelectUkerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
		function aasort (&$array, $key) {
			$sorter=array();
			$ret=array();
			reset($array);
			foreach ($array as $ii => $va) {
				$sorter[$ii]=$va[$key];
			}
			asort($sorter);
			foreach ($sorter as $ii => $va) {
				$ret[$ii]=$array[$ii];
			}
			$array=$ret;
		return $array;
		}
	public function getBranch( Request $request )
	{
	    $data['branch'] = $request->header('branch');
	    $data['kode'] = $request->kode_kanwil;
	    $data['keys'] = $request->keys;
      $data['pn'] = $request->header('pn');
      // $apiPdmToken = $apiPdmToken[0];
      // dd(count(apiPdmToken::all()));
      if ( count(apiPdmToken::all()) > 0 ) {
        $apiPdmToken = apiPdmToken::latest('id')->first()->toArray();
      } else {
        $this->gen_token();
        $apiPdmToken = apiPdmToken::latest('id')->first()->toArray();
      }

      if ($apiPdmToken['expires_in'] >= date("Y-m-d H:i:s")) {
        $token = $apiPdmToken['access_token'];
        $listExisting = $this->ListBranch($data, $token);

        return response()->success( [
            'message' => 'Sukses',
            'contents' => $listExisting
        ]);
      } else {
        $briConnect = $this->gen_token();
        $apiPdmToken = apiPdmToken::latest('id')->first()->toArray();
        
        $token = $apiPdmToken['access_token'];
        $listExisting = $this->ListBranch($data, $token);

        return response()->success( [
            'message' => 'Sukses',
            'contents' => $listExisting
        ]);
      }
	}

	public function ListBranch($data, $token)
    {
      $client = new Client();
	 /*   $return =  Brispot::setEndpoint('region/v3')
				->setHeaders([
					'Authorization' => 'Bearer '.$token,
				])
                ->setBody([
                ])->get('form_params');

            return $return; */
 	  if($data['keys']=='main'){
			  $requestListExisting = $client->request('GET', 'http://172.18.44.182/bribranch/region/v3/'.$data['kode'],
				[
				  'headers' =>
				  [
					'Authorization' => 'Bearer '.$token
				  ]
				]
			  );
	  }elseif($data['keys']=='branch'){
				$requestListExisting = $client->request('GET', 'http://172.18.44.182/bribranch/mainbr/'.$data['kode'],
				[
				  'headers' =>
				  [
					'Authorization' => 'Bearer '.$token
				  ]
				]
			  );
	  }elseif($data['keys']=='kanwil'){
				$requestListExisting = $client->request('GET', 'http://172.18.44.182/bribranch/region/v3',
				[
				  'headers' =>
				  [
					'Authorization' => 'Bearer '.$token
				  ]
				]
			  );
	  } 
      $listExisting = json_decode($requestListExisting->getBody()->getContents(), true);

      return $listExisting;*/
    }
	

}
