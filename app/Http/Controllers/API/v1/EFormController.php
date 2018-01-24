<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\EFormRequest;
use App\Events\EForm\Approved;
use App\Events\EForm\RejectedEform;
use App\Events\EForm\VerifyEForm;
use App\Models\EForm;
use App\Models\Customer;
use App\Models\CustomerDetail;
use App\Models\KPR;
use App\Models\BRIGUNA;
use App\Models\EformBriguna;
use App\Models\Mitra;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Collateral;
use App\Models\User;
use App\Models\UserServices;
use App\Notifications\EFormPenugasanDisposisi;
use App\Notifications\PengajuanKprNotification;
use App\Models\UserNotification;
use App\Notifications\ApproveEFormCustomer;
use App\Notifications\RejectEFormCustomer;
use App\Notifications\VerificationApproveFormNasabah;
use App\Notifications\VerificationRejectFormNasabah;
use DB;
use Brispot;
use Cache;
use App\Models\Crm\apiPdmToken;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class EFormController extends Controller
{
    public function __construct(User $user, UserServices $userservices, UserNotification $userNotification)
    {
        $this->userServices = new UserServices;
        $this->user = $user;
        $this->userservices = $userservices;
        $this->userNotification = $userNotification;
    }

	public function ListBranch($data, $token)
    {
      $client = new Client();
	  $host = env('APP_URL');
	  if($host == 'http://api.dev.net/'){
		$url = 'http://172.18.44.182/bribranch/branch/';
	}else{
		$url = 'http://api.briconnect.bri.co.id/bribranch/branch/';  
	  }
	  $requestListExisting = $client->request('GET', $url.$data['branch'],
				[
				  'headers' =>
				  [
					'Authorization' => 'Bearer '.$token
				  ]
				]
			  );
			  
      $listExisting = json_decode($requestListExisting->getBody()->getContents(), true);
	 return $listExisting;
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 public function hapuseform( Request $request )
    {
        \Log::info($request->all());
          $briguna = BRIGUNA::where('eform_id', $request->id )->findOrFail();
		  $briguna = $eform->delete();
          $eform = EForm::where('eform_id', $request->id )->findOrFail();
		  $eform = $eform->delete();
        return response()->success( [
            'contents' => 'Hapus berhasil'
        ],200 );
    }
    public function index( Request $request )
    {
        \Log::info($request->all());
        $limit = $request->input( 'limit' ) ?: 10;
        $newForm = EForm::filter( $request )->paginate( $limit );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $newForm
        ], 200 );
    }
	public function php_ini(){
		phpinfo();
	}
    public function show_briguna( Request $request )
    {
        \Log::info($request->all());
          $eform = EformBriguna::filter( $request )->get();
		  $eform = $eform->toArray();
          $eform[0]['Url'] = env('APP_URL').'/uploads/';
        return response()->success( [
            'contents' => $eform
        ],200 );
    }
	public function birth_place($id){

		  $birth_place = DB::table('cities')
						 ->select('name')
						 ->where('cities.id', $id)
						 ->get();
				$birth_place = $birth_place->toArray();
				$birth_place = json_decode(json_encode($birth_place), True);
		return $birth_place;
	}

	public function show_bri( Request $request )
    {
		$customer = DB::table('customer_details')
						 ->select('users.*','customer_details.*')
						 ->join('users', 'users.id', '=', 'customer_details.user_id')
						 ->where('customer_details.user_id', $request->user_id)
						 ->get();
				$customer = $customer->toArray();
				$customer = json_decode(json_encode($customer), True);

        \Log::info($request->all());
          $eform = EformBriguna::filter( $request )->get();
		  
		$mitra_relation = DB::table('mitra')
						 ->select('mitra.*')
						 ->where('mitra.idMitrakerja', $eform[0]['mitra_id'])
						 ->get();
				$mitra_relation = $mitra_relation->toArray();
				$mitra_relation = json_decode(json_encode($mitra_relation), True);
		  $eform = $eform->toArray();
		  //----------personal------------------------
		  $eform[0]['customer']['personal'] = $customer[0];
		  $eform[0]['mitra'] = $mitra_relation[0];
		  //-----------work---------------------------
		  $work = [
					"type_id"=> $customer[0]['job_type_id'],
                "type"=> $customer[0]['job_type_name'],
                "work_id"=> $customer[0]['job_id'],
                "work"=> $customer[0]['job_name'],
                "company_name"=> $customer[0]['company_name'],
                "work_field_id"=> $customer[0]['job_field_id'],
                "work_field"=> $customer[0]['job_field_name'],
                "position_id"=> $customer[0]['position'],
                "position"=> $customer[0]['position_name'],
                "work_duration"=> $customer[0]['work_duration'],
                "work_duration_month"=> $customer[0]['work_duration_month'],
                "office_address"=> $customer[0]['office_address'],
					];
     	  $eform[0]['customer']['work'] = $work;

		  $status_income = '';
		  $status_finance = '';
		  if($customer[0]['couple_salary']==NULL){
			  $status_income = 'Pisah Harta';
		  }else{
			   $status_income = 'Gabung Harta';
		  }
		  if($customer[0]['couple_salary']==NULL){
			  $status_finance = 'Single Income';
		  }else{
			  $status_finance = 'Joint Income';
		  }
		  $financial = [
				 "salary"=> $customer[0]['salary'],
                "other_salary"=> $customer[0]['other_salary'],
                "loan_installment"=> $customer[0]['loan_installment'],
                "dependent_amount"=> $customer[0]['dependent_amount'],
				"status_income"=> $status_income,
                "status_finance"=> $status_finance,
                "salary_couple"=> $customer[0]['couple_salary'],
                "other_salary_couple"=> $customer[0]['couple_other_salary'],
                "loan_installment_couple"=> $customer[0]['couple_loan_installment']
					];
		  $eform[0]['customer']['financial'] = $financial;

		  $contact = [
				 "emergency_contact"=> $customer[0]['emergency_contact'],
                "emergency_relation"=> $customer[0]['emergency_relation'],
                "emergency_name"=> $customer[0]['emergency_name']
					];
		  $eform[0]['customer']['contact'] = $contact;

		  $other = [
				"image"=> $customer[0]['image'],
                "identity"=> $customer[0]['identity'],
                "npwp"=> $customer[0]['npwp'],
                "family_card"=> $customer[0]['family_card'],
                "marrital_certificate"=> $customer[0]['marrital_certificate'],
                "diforce_certificate"=> $customer[0]['diforce_certificate']

					];
		  $eform[0]['customer']['other'] = $other;

		  $status = '';
		 if ($eform[0]['status_eform'] == 'Rejected' ) {
            $status= 'Kredit Ditolak';
        }
        if( $eform[0]['is_approved'] && $customer[0]['is_verified'] ) {
            $status= 'Proses CLF';
        }
        if( $eform[0]['ao_id'] ) {
            $status= 'Disposisi Pengajuan';
        }
		$eform[0]['status'] = $status;

			//-----------customer------------------
     	  $eform[0]['customer']['is_simple'] = true;
		  $eform[0]['customer']['is_completed'] = false;
		  $eform[0]['customer']['is_verified'] = $customer[0]['is_verified'];


		  $eform[0]['customer']['schedule'] = [];
		  $eform[0]['customer']['is_approved'] = $eform[0]['is_approved'];

          $eform[0]['Url'] = env('APP_URL').'/uploads/';

		  $eform[0]['nominal'] = $eform[0]['request_amount'];
		  $eform[0]['costumer_name'] = $customer[0]['first_name'].' '.$customer[0]['last_name'];
		  $eform[0]['kpr']['year'] = $eform[0]['year'];
            if(!empty($customer[0]['birth_place_id'])){
                  $birth_place = $this->birth_place($customer[0]['birth_place_id']);
                  $eform[0]['customer']['personal']['birth_place'] = $birth_place[0]['name'];
            }else{
                $eform[0]['customer']['personal']['birth_place']  = null;
            }

    		if(!empty($customer[0]['couple_birth_place_id'])){
    			  $birth_place_couple = $this->birth_place($customer[0]['couple_birth_place_id']);
    			  $eform[0]['customer']['personal']['couple_birth_place'] = $birth_place_couple[0]['name'];
    		}else{
    			$eform[0]['customer']['personal']['couple_birth_place']  = null;
    		}
		  $eform[0]['customer']['personal']['name'] = $customer[0]['first_name'].' '.$customer[0]['last_name'];
        return response()->success( [
            'contents' => $eform[0]
        ],200 );
    }


    public function mitra_relation( Request $request )
    {
        \Log::info($request->all());
        $mitra = Mitra::filter( $request )->get();
        // $eform = EForm::with( 'visit_report.mutation.bankstatement' )->findOrFail( $eform_id );
        return response()->success( [
            'contents' => [
                'data' => $mitra
            ]
        ], 200 );
    }
    /**
     * Display the specified resource.
     *
     * @param  string $type
     * @param  integer $eform_id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $type, $eform_id )
    {
        $recontest = (empty(request()->header('recontest')) ? false : true);
		$eform = EForm::findOrFail($eform_id);
		$data = $eform;
		if($eform['product_type']=='briguna'){
			$another_array = [];
			$another_array['id'] = $eform_id;
			$another_array['user_id'] = $eform['user_id'];

			$request = new Request($another_array);
			$eform = $this->show_bri($request);
	        return $eform;

		}elseif($eform['product_type']=='kpr'){
			$eform = EForm::with( 'visit_report.mutation.bankstatement' )->findOrFail( $eform_id );
			// Check recontest or not
            if($recontest){
                $usersModel  = User::FindOrFail($eform->user_id);
                $credentials = [
                    'data' => $eform,
                    'user' => $usersModel,
                ];
                pushNotification($credentials, "recontestEForm");
            }
            return response()->success([
				'contents' => $eform
			]);
		}
    }

    public function showIdsAndRefNumber( $ids, $ref_number )
    {
        $eform = EForm::Where('id',(int)$ids)->OrWhere('ref_number', 'ilike','%"'. $ref_number.'"%')->first();

        return response()->success( [
            'contents' => $eform
        ] );
    }

    public function uploadimage($image,$id,$atribute) {
        //$eform = EForm::findOrFail($id);
        $path = public_path( 'uploads/' . $id . '/' );

        if ( ! empty( $this->attributes[ $atribute ] ) ) {
            File::delete( $path . $this->attributes[ $atribute ] );
        }
        $filename = null;
        if ($image) {
            if (!$image->getClientOriginalExtension()) {
                if ($image->getMimeType() == '.pdf') {
                    $extension = '.pdf';
                }else{
                    $extension = 'png';
                }
            }else{
                $extension = $image->getClientOriginalExtension();
            }
            // log::info('image = '.$image->getMimeType());
            $filename = $id . '-'.$atribute.'.' . $extension;
            $image->move( $path, $filename );
        }
        return $filename;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\API\v1\EFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store( EFormRequest $request )
    {
        DB::beginTransaction();
        try {
            $baseRequest = $request->all();

            // Get User Login
            $user_login = \RestwsHc::getUser();

            if ($user_login['role'] === 'ao' ) {
                $baseRequest['ao_id'] = $user_login['pn'];
                $baseRequest['ao_name'] = $user_login['name'];
                $baseRequest['ao_position'] = $user_login['position'];
            } else {
                $baseRequest['staff_name'] = $user_login['name'];
                $baseRequest['staff_position'] = $user_login['position'];
            }

            if ( $request->product_type == 'kpr' ) {
                if ($baseRequest['status_property'] != ENV('DEVELOPER_KEY', 1)) {
                    $baseRequest['developer'] = ENV('DEVELOPER_KEY', 1);
                    $baseRequest['developer_name'] = ENV('DEVELOPER_NAME', "Non Kerja Sama");
                }
            }

            $baseArray = array (
                'job_type_id' => 'work_type', 'job_type_name' => 'work_type_name'
                , 'job_id' => 'work', 'job_name' => 'work_name'
                , 'job_field_id' => 'work_field', 'job_field_name' => 'work_field_name'
                , 'citizenship_name' => 'citizenship'
            );

            foreach ($baseArray as $target => $base) {
                if ( isset($baseRequest[$base]) ) {
                    $baseRequest[$target] = $baseRequest[$base];
                    unset($baseRequest[$base]);
                }
            }

            if ( $request->product_type == 'briguna' ) {
            \Log::info("=======================================================");
            /* BRIGUNA */
					$data_new['branch']=$request->input('branch_id');
					  if ( count(apiPdmToken::all()) > 0 ) {
						$apiPdmToken = apiPdmToken::latest('id')->first()->toArray();
					  } else {
						$this->gen_token();
						$apiPdmToken = apiPdmToken::latest('id')->first()->toArray();
					  }
					  if ($apiPdmToken['expires_in'] >= date("Y-m-d H:i:s")) {
						$token = $apiPdmToken['access_token'];
						$listExisting = $this->ListBranch($data_new, $token);
					  } else {
						$briConnect = $this->gen_token();
						$apiPdmToken = apiPdmToken::latest('id')->first()->toArray();
						$token = $apiPdmToken['access_token'];
						$listExisting = $this->ListBranch($data_new, $token);

					  }
					if ( $listExisting['success'] == '00' ) {
						foreach ($listExisting['data'] as $branch) {
							if ( $branch['branch'] == $request->input('branch_id') ) {
								$baseRequest['branch'] = $branch['mbdesc'];

							}
						}
					}
            $NPWP_nasabah = $request->NPWP_nasabah;
            $KK = $request->KK;
            $SLIP_GAJI = $request->SLIP_GAJI;
            $SK_AWAL = $request->SK_AWAL;
            $SK_AKHIR = $request->SK_AKHIR;
            $REKOMENDASI = $request->REKOMENDASI;

            $id = date('YmdHis');
            $NPWP_nasabah = $this->uploadimage($NPWP_nasabah,$id,'NPWP_nasabah');
            $KK = $this->uploadimage($KK,$id,'KK');
            $SLIP_GAJI = $this->uploadimage($SLIP_GAJI,$id,'SLIP_GAJI');
            $SK_AWAL = $this->uploadimage($SK_AWAL,$id,'SK_AWAL');
            $SK_AKHIR = $this->uploadimage($SK_AKHIR,$id,'SK_AKHIR');
            $REKOMENDASI = $this->uploadimage($REKOMENDASI,$id,'REKOMENDASI');

            $baseRequest['NPWP_nasabah'] = $NPWP_nasabah;
            $baseRequest['KK'] = $KK;
            $baseRequest['SLIP_GAJI'] = $SLIP_GAJI;
            $baseRequest['SK_AWAL'] = $SK_AWAL;
            $baseRequest['SK_AKHIR'] = $SK_AKHIR;
            $baseRequest['REKOMENDASI'] = $REKOMENDASI;
			$baseRequest['id_foto'] = $id;

			if($baseRequest['Payroll']=='1'){
				$SKPG = '';
				if(!empty($request->SKPG)){
					$SKPG = $request->SKPG;
					$SKPG = $this->uploadimage($SKPG,$id,'SKPG');
					$baseRequest['SKPG'] = $SKPG;
					/*----------------------------------*/
				}
				$baseRequest['SKPG'] = $SKPG;
			}else{
				if(!empty($request->SKPG)){
                $SKPG = $request->SKPG;
                $SKPG = $this->uploadimage($SKPG,$id,'SKPG');
                $baseRequest['SKPG'] = $SKPG;
			}else{
				$dataEform =  EForm::where('nik', $request->nik)->get();
                return response()->error( [
                    'message' => 'Payroll Non BRI SKPG harus ada',
                    'contents' => $dataEform
                ], 422 );
				}
			}
                $kpr = BRIGUNA::create( $baseRequest );
                $return = [
                    'message' => 'Data e-form briguna berhasil ditambahkan.',
                    'contents' => $kpr
                ];
                    \Log::info($kpr);
        } else {
			        $branchs = \RestwsHc::setBody([
					'request' => json_encode([
						'requestMethod' => 'get_near_branch_v2',
						'requestData'   => [
							'app_id' => 'mybriapi',
							'kode_branch' => $request->input('branch_id'),
							'distance'    => 0,

							// if request latitude and longitude not present default latitude and longitude cimahi
							'latitude'  => 0,
							'longitude' => 0
						]
					])
				])
				->post('form_params');
				if ( $branchs['responseCode'] == '00' ) {
					foreach ($branchs['responseData'] as $branch) {
						if ( $branch['kode_uker'] == $request->input('branch_id') ) {
							$baseRequest['branch'] = $branch['unit_kerja'];

						}
					}
				}
            $dataEform =  EForm::where('nik', $request->nik)->get();
            // $dataEform = [];
            if (count($dataEform) == 0) {
                $developer_id = env('DEVELOPER_KEY',1);
                $developer_name = env('DEVELOPER_NAME','Non Kerja Sama');

                if ($baseRequest['developer'] == $developer_id && $baseRequest['developer_name'] == $developer_name)  {

                    $baseProperty = array(
                        'developer_id' => $baseRequest['developer'],
                        'prop_id_bri' => '1',
                        'name' => $developer_name,
                        'pic_name' => 'BRI',
                        'pic_phone' => '-',
                        'address' => $baseRequest['home_location'],
                        'category' => '3',
                        'latitude' => '0',
                        'longitude' => '0',
                        'description' => '-',
                        'facilities' => '-'
                    );

                    $getKanwil = \RestwsHc::setBody([
                        'request' => json_encode([
                            'requestMethod' => 'get_list_uker_from_cabang',
                            'requestData' => [
                                'app_id' => 'mybriapi'
                                , 'branch_code' => $request->input('branch_id')
                            ]
                        ])
                    ])->post('form_params');

                    if ( $getKanwil['responseCode'] == '00' ) {
                        foreach ($getKanwil['responseData'] as $kanwil) {
                            if ( $kanwil['branch'] == $request->input('branch_id') ) {
                                $baseProperty['region_id'] = $kanwil['region'];
                                $baseProperty['region_name'] = $kanwil['rgdesc'];
                            }
                        }

                        $property =  Property::create( $baseProperty );
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
                }
                    $kpr = KPR::create( $baseRequest );
                    $return = [
                        'message' => 'Data e-form berhasil ditambahkan.',
                        'contents' => $kpr['kpr']
                    ];
                } else {
                    return response()->error( [
                        'message' => 'User sedang dalam pengajuan',
                        'contents' => $dataEform
                    ], 422 );
                }
            }
            DB::commit();
    } catch (Exception $e) {
            DB::rollback();
            return response()->error( [
                'message' => 'Terjadi Kesalahan Silahkan Tunggu Beberapa Saat Dan Ulangi',
            ], 422 );
        }
        $userId = CustomerDetail::where('nik', $baseRequest['nik'])->first();
        $usersModel = User::FindOrFail($userId['user_id']);     /*send notification*/

        $credentials = [
            'data'    => $kpr['eform'],
            'request' => $request,
        ];
        pushNotification($credentials, 'createEForm');
        return response()->success($return, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function submitScreening( Request $request )
    {
        DB::beginTransaction();

        if ( $request->has('selected_sicd') && $request->has('selected_dhn') ) {
            $eform = EForm::find( $request->input('eform_id') );

            $calculate = array(
                $request->input('pefindo', 'Hijau')
                , $request->input('dhn', 'Hijau')
                , $request->input('sicd', 'Hijau')
            );

            if ( in_array('Merah', $calculate) ) {
                $result = '3';

            } else if ( in_array('Kuning', $calculate) ) {
                $result = '2';

            } else {
                $result = '1';

            }

            $eform->update( [
                'prescreening_status' => $result
                , 'selected_dhn' => $request->input('selected_dhn')
                , 'selected_sicd' => $request->input('selected_sicd')
            ] );

            $eform = array();

        } else {
            $eform = EForm::findOrFail( $request->id );
            $eform->update( [ 'prescreening_status' => $request->prescreening_status ] );

        }

        DB::commit();
        return response()->success( [
            'message' => 'Screening e-form berhasil disimpan.',
            'contents' => $eform
        ], 201 );
    }

    /**
     * Get data for prescreening.
     *
     * @return \Illuminate\Http\Response
     */
    public function postPrescreening( Request $request )
    {
        $data = EForm::findOrFail($request->eform);
        $personal = $data->customer->personal;

        $dhn = json_decode((string) $data->dhn_detail);
        if ( !isset($dhn->responseData) ) {
            $dhn = json_decode((string) '{"responseCode":"01","responseDesc":"","responseData":[{"kategori":null,"keterangan":"","warna":"Hijau","result":""}]}');
        }

        $sicd = json_decode((string) $data->sicd_detail);
        if ( !isset($sicd->responseData) ) {
            $sicd = json_decode((string) '{"responseCode":"01","responseDesc":"","responseData":[{"status":null,"acctno":null,"cbal":null,"bikole":null,"result":null,"cif":null,"nama_debitur":null,"tgl_lahir":null,"alamat":null,"no_identitas":null}]}');
        }

        $html = '';

        foreach (explode(',', $data->uploadscore) as $value) {
            if ($value != '') {
                $html .= asset('uploads/'.$data->nik.'/'.$value) . ',';
            }
        }

        $data['uploadscore'] = $html;

        return response()->success( [
            'message' => 'Data Screening e-form',
            'contents' => [
                'eform' => $data
                , 'dhn' => $dhn->responseData
                , 'sicd' => $sicd->responseData
            ]
        ], 200 );
    }

    /**
     * Set E-Form AO disposition.
     *
     * @param  \App\Http\Requests\API\v1\EFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function disposition( EFormRequest $request, $id )
    {
        $role = request()->header( 'role' );
        $pn = request()->header( 'pn' );
        $branch_id = request()->header( 'branch_id' );

        DB::beginTransaction();
        $eform = EForm::findOrFail( $id );
        $ao_id = substr( '00000000' . $request->ao_id, -8 );

        $baseRequest = [ 'ao_id' => $ao_id ];
        // Get User Login
        $user_login = \RestwsHc::getUser($ao_id);
        $baseRequest['ao_name'] = $user_login['name'];
        $baseRequest['ao_position'] = $user_login['position'];

        $eform->update( $baseRequest );

        $typeModule = getTypeModule(EForm::class);
        $notificationIsRead =  $this->userNotification->where('slug', $id)->where( 'type_module',$typeModule)
                                       ->whereNull('read_at')
                                       ->first();
        if($notificationIsRead != NULL){
            $notificationIsRead->markAsRead();
        }
        $usersModel = User::FindOrFail($eform->user_id);     /*send notification*/
        $usersModel->notify(new EFormPenugasanDisposisi($eform));

        DB::commit();

        // Credentials for push notification helper
        $credentials = [
            'eform' => $eform,
            'ao_id' => $ao_id,
        ];

        // Call the helper of push notification function
        pushNotification($credentials, 'disposition');

        return response()->success( [
            'message' => 'E-Form berhasil di disposisi',
            'contents' => $eform
        ], 201 );
    }

    /**
     * Set E-Form Approve.
     *
     * @param integer $eform_id
     * @param  \App\Http\Requests\API\v1\EFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function approve( EFormRequest $request, $eform_id )
    {
        $baseRequest = $request;

        // Get User Login
        $user_login = \RestwsHc::getUser();
        if(isset($user_login)){
            $baseRequest['pinca_name'] = $user_login['name'];
            $baseRequest['pinca_position'] = $user_login['position'];
        }

        $data = EForm::findOrFail($eform_id);
        $currentStatus = $data->status_eform;
        $status = ( $request->is_approved ? 'approveEForm' : 'rejectEForm' );
        $eform = EForm::approve( $eform_id, $baseRequest );

        if( $eform['status'] ) {
            $data =  EForm::findOrFail($eform_id);
            $typeModule = getTypeModule(EForm::class);

            $notificationIsRead = $this->userNotification
                ->where( 'slug', $eform_id)
                ->where( 'type_module',$typeModule)
                ->whereNull('read_at')
                ->first();

            if($notificationIsRead != NULL ){
                $notificationIsRead->markAsRead();
            }

            if ($request->is_approved) {
                $usersModel = User::FindOrFail($data->user_id);
                // Recontest
                if ( $currentStatus != 'Approval2' ) {
                    event( new Approved( $data ) );
                }

                // $responseName = ($data->additional_parameters['nama_reviewer']) ? $data->additional_parameters['nama_reviewer'] : '';
                // $responseMessage = 'E-form berhasil di approve oleh ' . $responseName . '.';
                $responseMessage = 'E-form berhasil di approve.';
                $credentials = [
                    'data' => $data,
                    'user'  => $usersModel
                ];
                // Call the helper of push notification function
                pushNotification($credentials, $status);

            } else {
                $usersModel = User::FindOrFail($data->user_id);
                event( new RejectedEform( $data ) );
                $credentials = [
                    'data' => $data,
                    'user'  => $usersModel
                ];
                // Call the helper of push notification function
                pushNotification($credentials, $status);

                $responseMessage = 'E-form berhasil di reject.';

            }

            // Recontest
            if ( $currentStatus == 'Approval2' ) {
                $detail = EForm::with( 'visit_report.mutation.bankstatement', 'recontest' )->findOrFail( $eform_id );
                generate_pdf('uploads/'. $detail->nik, 'recontest.pdf', view('pdf.recontest', compact('detail')));
            } else {
                $usersModel = User::FindOrFail($data->user_id);
                $detail = EForm::with( 'visit_report.mutation.bankstatement' )->findOrFail( $eform_id );
                generate_pdf('uploads/'. $detail->nik, 'lkn.pdf', view('pdf.approval', compact('detail')));
            }

            return response()->success( [
                'message' => $responseMessage,
                'contents' => $eform
            ], 201 );

        } else {
            return response()->success( [
                'message' => isset($eform['message']) ? $eform['message'] : 'Approval E-Form Gagal',
                'contents' => $eform
            ], 400 );
        }
    }

    /**
     * Insert data to core BRI.
     *
     * @param integer $step_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function insertCoreBRI( Request $request, $eform_id, $step_id )
    {
        DB::beginTransaction();
        $eform = EForm::findOrFail( $eform_id );
        $result = $eform->insertCoreBRI( $step_id );

        DB::commit();
    }

    /**
     * Approve / Reject verification specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $token
     * @param  string $status
     * @return \Illuminate\Http\Response
     */
    public function verify( Request $request, $token, $status )
    {
        DB::beginTransaction();
        $verify = EForm::verify( $token, $status );
        if( $verify['message'] ) {
            if ($verify['contents']) {
                $typeModule = getTypeModule(EForm::class);

                $notificationIsRead =  $this->userNotification
                    ->where( 'slug', $verify['contents']->id)
                    ->where( 'type_module',$typeModule)
                    ->whereNull('read_at')
                    ->first();

                if ( $notificationIsRead != NULL ) {
                    $notificationIsRead->markAsRead();
                }

                $usersModel  = User::FindOrFail($verify['contents']->user_id);

                $credentials = [
                    'data' => $verify['contents'],
                    'user' => $usersModel,
                ];
                pushNotification($credentials, $status."EForm");

                if ($status == 'approve') {
                    $detail = EForm::with( 'customer', 'kpr' )->where('id', $verify['contents']->id)->first();

					if ( $verify['contents']['product_type'] == 'briguna' ){
                        $detail = EForm::with( 'customer', 'briguna' )->where('id', $verify['contents']->id)->first();

                    } else {
					   $detail = EForm::with( 'customer', 'kpr' )->where('id', $verify['contents']->id)->first();

                    }

                    generate_pdf('uploads/'. $detail->nik, 'permohonan.pdf', view('pdf.permohonan', compact('detail')));
                }
                event( new VerifyEForm( $verify['contents'] ) );
            }
            DB::commit();
            $code = 201;

        } else {
            DB::rollback();
            $code = 404;
        }

        return response()->success( $verify, $code );
    }

   /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @author erwan.akse@wgs.co.id
     */
    public function delete(Request $request)
    {
        DB::beginTransaction();
        $eform = EForm::findOrFail($request->eform_id);
        if ($eform->kpr->is_sent == false ) {
          User::destroy($eform->user_id);
          DB::commit();
        return response()->success( [
            'message' => 'Hapus User Berhasil',
        ], 200 );
      }else
      {
        DB::rollback();
        return response()->error( [
            'message' => 'User Tidak Dapat Dihapus',
        ], 422 );
      }
    }

    /**
     * Update eform status from CLAS.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateCLAS( Request $request )
    {
        $message = "EForm tidak ditemukan.";
        DB::beginTransaction();

        try {
            if ( $request->has('fid_aplikasi') && $request->has('status') ) {
                $updateCLAS = EForm::updateCLAS(
                    $request->input('fid_aplikasi')
                    , $request->input('status')
                );

                if ( $updateCLAS['message'] ) {
                    \DB::commit();

                    // Push Notification
                    $data = EForm::where(
                        DB::Raw("additional_parameters::json->>'fid_aplikasi'")
                        , $request->input('fid_aplikasi')
                    )->first();

                    $typeModule = getTypeModule(EForm::class);
                    $notificationIsRead = $this->userNotification->where( 'slug', $data->id)->where( 'type_module',$typeModule)
                       ->whereNull('read_at')
                       ->first();

                    $usersModel  = User::FindOrFail($data['user_id']);
                    $status      = $updateCLAS['status'];
                    $credentials = [
                        'data'  => $data,
                        'user'  => $usersModel
                    ];
                    // Call the helper of push notification function
                    pushNotification($credentials, ($status) ? 'approveEForm' : 'rejectEForm');

                    return response()->json([
                        "responseCode" => "01",
                        "responseDesc" => $updateCLAS['message']
                    ], 200);
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $message = $e->getMessage();
            dd($e);
        }

        return response()->json([
            "responseCode" => "02",
            "responseDesc" => $message
        ], 200 );
    }
}
