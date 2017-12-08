<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\CustomerRequest;
use App\Events\Customer\CustomerVerify;
use App\Models\Customer;
use App\Models\CustomerDetail;
use App\Models\EForm;
use App\Models\User;
use App\Models\KPR;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Collateral;
use Sentinel;
use DB;

class CustomerController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index( Request $request )
	{
		$limit = $request->input( 'limit' ) ?: 10;
		$customers = User::getCustomers( $request )->paginate( $limit );
		return response()->success( [
			'message' => 'Sukses',
			'contents' => $customers
		], 200 );
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \App\Http\Requests\API\v1\CustomerRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store( CustomerRequest $request )
	{
		$data = $request->all();
		$product_leads = '';
		if ( isset($request->product_leads) ){
			$product_leads = $request->product_leads;
		}

		if ( $product_leads == '' || $product_leads == 'kpr' ){
			DB::beginTransaction();
			$customer = Customer::create( $request->all() );

			DB::commit();
			return response()->success( [
				'message' => 'Data nasabah berhasil ditambahkan.',
				'contents' => $customer
			], 201 );

		} elseif ( $product_leads == 'briguna' ) {
			$data['address'] = $data['alamat'].' rt '.$data['rt'].'/rw '.$data['rw'].', kelurahan='.
								$data['kelurahan'].'kecamatan='.$data['kecamatan'].','.$data['kota'].' '.$data['kode_pos'];

			$data['address_domisili'] = $data['alamat_domisili'].' rt '.$data['rt_domisili'].'/rw '.
								$data['rw_domisili'].', kelurahan='.$data['kelurahan_domisili'].'kecamatan='.$data['kecamatan_domisili'].','.$data['kota_domisili'].' '.$data['kode_pos_domisili'];
			DB::beginTransaction();
			$customer = Customer::create( $data );

			DB::commit();
			return response()->success( [
				'message' => 'Data nasabah berhasil ditambahkan.',
				'contents' => $data
			], 201 );

		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \App\Http\Requests\API\v1\CustomerRequest  $request
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function update( CustomerRequest $request, $id )
	{
		DB::beginTransaction();
		$customer = Customer::findOrFail( $id );
		$customer->update( $request->all() );

		DB::commit();
		return response()->success( [
			'message' => 'Data nasabah berhasil dirubah.',
			'contents' => $customer
		] );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  string  $type
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show( $type, $id )
	{
		$customerDetail = CustomerDetail::where( 'nik', '=', $id )->first();

		if (count($customerDetail) > 0) {
			$customer = Customer::findOrFail( $customerDetail->user_id );
		} else {
			$customer = Customer::findOrFail( $id );
		}
		return response()->success( [
			'message' => 'Sukses',
			'contents' => $customer
		], 200 );
	}

	/**
	 * Verify the specified resource in storage.
	 *
	 * @param  \App\Http\Requests\API\v1\CustomerRequest  $request
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function verify( CustomerRequest $request, $id )
	{
		DB::beginTransaction();
		$customer = Customer::findOrFail( $id );

		$baseRequest = $request->only('developer','property','status_property','price', 'building_area', 'home_location', 'year', 'active_kpr', 'dp', 'request_amount', 'developer_name', 'property_name', 'kpr_type_property','property_type','property_type_name','property_item','property_item_name');

        // Get User Login
        $user_login = \RestwsHc::getUser();
        $baseRequest['ao_name'] = $user_login['name'];
        $baseRequest['ao_position'] = $user_login['position'];

		if ($request->has('eform_id')) {

			$developer_id = env('DEVELOPER_KEY',1);
            $developer_name = env('DEVELOPER_NAME','Non Kerja Sama');
        
            if ($baseRequest['developer'] == $developer_id && $baseRequest['developer_name'] == $developer_name)  {
                $property =  Property::create([
                    'developer_id'=>$baseRequest['developer'],
                    'prop_id_bri'=>'1',
                    'name'=>$developer_name,
                    'pic_name'=>'BRI',
                    'pic_phone'=>'-',
                    'address'=>$baseRequest['home_location'],
                    'category'=>'3',
                    'latitude'=>'0',
                    'longitude'=>'0',
                    'description'=>'-',
                    'facilities'=>'-'
                ]);
                $baseRequest['property'] = $property->id;
                $baseRequest['property_name'] = $developer_name;
                \Log::info('=================== Insert Property===========');
                \Log::info($property);
                if ($property) {
                    $propertyType = PropertyType::create([
                        'property_id'=>$property->id,
                        'name'=>$developer_name,
                        'building_area'=>$baseRequest['building_area'],
                        'price'=>$baseRequest['price'],
                        'surface_area'=>$baseRequest['building_area'],
                        'electrical_power'=>'-',
                        'bathroom'=>0,
                        'bedroom'=>0,
                        'floors'=>0,
                        'carport'=>0
                    ]);
                    \Log::info('=================== Insert Property type===========');
                    \Log::info($propertyType);
                    $baseRequest['property_type']= $propertyType->id;
                    $baseRequest['property_type_name']= $developer_name;
                    if ($propertyType) {
                        $data = [
                        'developer_id' => $developer_id,
                        'property_id' => $property->id,
                        'status' => Collateral::STATUS[0]
                    ];
                    $collateral = Collateral::updateOrCreate(['property_id' => $property->id],$data);
                    \Log::info('=================== Insert Collateral===========');
                    \Log::info($collateral);
                    }
                }
            }
            \Log::info($baseRequest);
				KPR::updateOrCreate(['eform_id' => $request->eform_id], $baseRequest);

		}

		$customer->verify( $request->except('join_income','developer','property','status_property', 'eform_id', 'price', 'building_area', 'home_location', 'year', 'active_kpr', 'dp', 'request_amount', 'developer_name', 'property_name', 'kpr_type_property','property_type','property_type_name','property_item','property_item_name','kpr_type_property_name','active_kpr_name','down_payment') );
		$eform = EForm::generateToken( $customer->personal['user_id'] );

		DB::commit();
		if( $request->verify_status == 'verify' ) {
			event( new CustomerVerify( $customer, $eform ) );
			return response()->success( [
				'message' => 'Email telah dikirim kepada nasabah untuk verifikasi data nasabah.',
				'contents' => $customer
			] );
		} else if( $request->verify_status == 'verified' ) {
			return response()->success( [
				'message' => 'Data nasabah telah di verifikasi.',
				'contents' => []
			] );
		}
	}
}
