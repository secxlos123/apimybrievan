<?php

namespace App\Http\Controllers\API\v1;


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
use App\Models\ApiPdmTokensBriguna;
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
	//    $data['branch'] = $request->header('branch');
	    $data['kode'] = $request->kode;
	    $data['keys'] = $request->keys;
      $data['pn'] = $request->header('pn');
	  // $apiPdmToken = $apiPdmToken[0];
      // dd(count(apiPdmToken::all()));
      if ( count(ApiPdmTokensBriguna::all()) > 0 ) {
        $apiPdmToken = ApiPdmTokensBriguna::latest('id')->first()->toArray();
      } else {
        $this->gen_token_briguna();
        $apiPdmToken = ApiPdmTokensBriguna::latest('id')->first()->toArray();
      }
      if ($apiPdmToken['expires_in'] >= date("Y-m-d H:i:s")) {
        $token = $apiPdmToken['access_token'];
        $listExisting = $this->ListBranch($data, $token);

        return response()->success( [
            'message' => 'Sukses',
            'contents' => $listExisting
        ]);
      } else {
        $briConnect = $this->gen_token_briguna();
        $apiPdmToken = ApiPdmTokensBriguna::latest('id')->first()->toArray();
        
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
		 $host = env('APP_URL');
		 
      if($host == 'http://api.dev.net/' || $host == 'http://103.63.96.167/api/' || $host=='https://apimybridev.bri.co.id/'){
		$urls = 'http://10.35.65.208:81/';
	}else{
		$urls = 'http://api.briconnect.bri.co.id/';  
	  }
      $client = new Client();
	 /*   $return =  Brispot::setEndpoint('region/v3')
				->setHeaders([
					'Authorization' => 'Bearer '.$token,
				])
                ->setBody([
                ])->get('form_params');

            return $return; */
 	  if($data['keys']=='kanwil'){
			  $requestListExisting = $client->request('GET', $urls.'bribranch/region/v3/'.$data['kode'],
				[
				  'headers' =>
				  [
					'Authorization' => 'Bearer '.$token
				  ]
				]
			  );
	  }elseif($data['keys']=='branch'){
				$requestListExisting = $client->request('GET', $urls.'bribranch/branch/'.$data['kode'],
				[
				  'headers' =>
				  [
					'Authorization' => 'Bearer '.$token
				  ]
				]
			  );
	  }elseif($data['keys']=='main'){
				$requestListExisting = $client->request('GET', $urls.'bribranch/mainbr/'.$data['kode'],
				[
				  'headers' =>
				  [
					'Authorization' => 'Bearer '.$token
				  ]
				]
			  );
	  }elseif($data['keys']=='all'){
				$requestListExisting = $client->request('GET', $urls.'bribranch/region/v3',
				[
				  'headers' =>
				  [
					'Authorization' => 'Bearer '.$token
				  ]
				]
			  );
	  } 
      $listExisting = json_decode($requestListExisting->getBody()->getContents(), true);

      return $listExisting;
    }
	

}
