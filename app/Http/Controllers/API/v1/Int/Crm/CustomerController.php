<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\Crm\Marketing\CreateRequest;
// use App\Http\Request\API\v1\Crm\Marketing\UpdateRequest;
use App\Models\Crm\Marketing;
use App\Models\Crm\ActivityType;
use App\Models\Crm\ProductType;
use App\Models\Crm\Status;
use App\Models\User;

// use GuzzleHttp\Exception\GuzzleException;
// use GuzzleHttp\Client;

use App\Classes\Client\Client;


class CustomerController extends Controller
{
    public function index(Request $request)
    {
      $customersInt = User::getCustomers( $request )->get();
      // $customerData = Client::setEndpoint('customer')
      //               ->setQuery(['limit' => 100])
      //               ->setHeaders([
      //                 "Authorization" => request()->header('Authorization'),
      //                 "pn" => request()->header('branch')
      //               ])->get();
      // $customersEks = $customerData['contents']['data'];
      // dd($customersEks);
      return response()->success( [
  			'message' => 'Sukses',
  			'contents' => $customersInt
  		], 200 );

    }

    // public function test(Request $request)
    // {
    //   $customerData = Client::setEndpoint('customer')
    //                 ->setHeaders([
    //                   "Authorization" => request()->header('Authorization'),
    //                   "pn" => request()->header('branch')
    //                 ])->get();
    //   $dataCustomer = $customerData['contents']['data'];
    //
    //   return response()->success( [
  	// 		'message' => 'Sukses',
  	// 		'contents' => $dataCustomer
  	// 	], 200 );
    // }
}
