<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\EFormRequest;
use App\Events\EForm\Approved;
use App\Events\EForm\RejectedEform;
use App\Events\EForm\VerifyEForm;
use App\Models\EForm;
use App\Models\EFormMonitoring;
use App\Models\Customer;
use App\Models\CustomerDetail;
use App\Models\KPR;
use App\Models\BRIGUNA;
use App\Models\KartuKredit;
use App\Models\EformBriguna;
use App\Models\Mitra;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Collateral;
use App\Models\User;
use App\Models\UserServices;
use App\Models\Appointment;
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
use PDF;
use App\Models\Crm\apiPdmToken;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Http\Controllers\API\v1\Int\PrescreeningController;

class EFormController extends Controller
{
    public function __construct(User $user, UserServices $userservices, UserNotification $userNotification)
    {
        $this->userServices = new UserServices;
        $this->user = $user;
        $this->userservices = $userservices;
        $this->userNotification = $userNotification;
    }

    public function ListBranch($data)
    {
      $client = new Client();
      $host = env('APP_URL');
      if($host == 'http://api.dev.net/' || $host == 'http://103.63.96.167/api/' || $host='https://apimybridev.bri.co.id/'){
        $url = 'http://10.35.65.208:81/bribranch/branch/';
       }else{
        $url = 'http://api.briconnect.bri.co.id/bribranch/branch/';
      }
      $requestListExisting = $client->request('GET', $url.$data['branch'],
                [
                  'headers' =>
                  [
                    'Authorization' => 'Bearer '.$this->get_token()
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
        $message = 'Hapus Gagal';
        $eform = EForm::findOrFail( $request->id );
        if ( $eform->briguna ) {
            $briguna = $eform->briguna;
            if( $briguna->is_send == null || $briguna->is_send == '' || empty($briguna->is_send) ){
                $briguna->delete();
                $eform->delete();
                $message = 'Hapus berhasil';
            }
        }

        return response()->success( [
            'contents' => $message
        ], 200 );
    }

    public function index( Request $request )
    {
        $limit = $request->input( 'limit' ) ?: 10;
        if ($request->has('slug')) {
            $newForm = EForm::findOrFail($request->input('slug'));
        }else{
            $newForm = EForm::filter( $request )->paginate( $limit );
        }
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $newForm
        ], 200 );
    }

	    public function monitoring( Request $request )
    {

	$limit = $request->input( 'limit' ) ?: 10;
        if ($request->has('slug')) {
            $newForm = EFormMonitoring::findOrFail($request->input('slug'));
        }else{
            $newForm = EFormMonitoring::filter( $request )->paginate( $limit );
        }
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $newForm
        ], 200 );
    }

    public function eformGenerate(Request $request)
    {
        $user = \RestwsHc::getUser();
        if($request->has('startdate') && $request->has('enddate')){
            $startdate = $request->input('startdate');
            $enddate = $request->input('enddate');
            $generateEform = EForm::whereBetween('created_at',[$startdate, $enddate])->get();
            if($user['role'] == 'ao'){
             $generateEform->where('ao_id', $user['pn']);
            }else{
                 $generateEform->where('branch_id', $user['branch_id']);
            }
            return response()->success(['message' => 'Sukses', 'contents' => $generateEform ]);
        }else{
            $generateEform = Eform::all();
            if($user['role'] == 'ao'){
             $generateEform->where('ao_id', $user['pn']);
            }else{
             $generateEform->where('branch_id', $user['branch_id']);
            }
            return response()->success(['message' => 'Sukses', 'contents' => $generateEform ]);
        }

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

    public function birth_place($id)
    {
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
          $mitra_relation[0]['UNIT_KERJA'] = $eform[0]['branch'];
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
            $status= 'Proses CLS';
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

          // keperluan tot
          $host = env('APP_URL');
          if($host == 'http://103.63.96.167/api/'){     
              $eform[0]['Url'] = 'http://103.63.96.167/api/uploads/';
          }else{
              $eform[0]['Url'] = env('APP_URL').'/uploads/';
          }
          

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
        if($eform['product_type'] == 'kartu_kredit'){
          $eform = EForm::with('kartukredit')->get();
          return response()->success([
            'contents'=>$eform
          ]);
        }else if ( $eform['product_type'] == 'briguna' ) {
            $another_array = [];
            $another_array['id'] = $eform_id;
            $another_array['user_id'] = $eform['user_id'];

            $request = new Request($another_array);
            $eform = $this->show_bri($request);
            return $eform;

        } elseif( $eform['product_type'] == 'kpr' ) {
            $eform = EForm::with( 'visit_report.mutation.bankstatement' )->findOrFail( $eform_id );
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

    public function uploadKKImage($image,$nik,$type,$time){
      $path = public_path('uploads/'.$nik.'/');
      $filename = null;
        if ($image) {
            if (!$image->getClientOriginalExtension()) {
                if ($image->getMimeType() == '.pdf') {
                    $extension = 'pdf';
                }elseif($image->getMimeType() == '.jpg'||$image->getMimeType() == '.jpeg'){
                    $extension = 'jpg';
                }else{
                    $extension = 'png';
                }
            }else{
                $extension = $image->getClientOriginalExtension();
            }
            $filename = $time. '-'.$type.'.' . $extension;
            $image->move( $path, $filename );
        }
        return $filename;

    }

    public function uploadimage($image,$id,$atribute)
    {
        $path = public_path( 'uploads/' . $id . '/' );

        if ( ! empty( $this->attributes[ $atribute ] ) ) {
            File::delete( $path . $this->attributes[ $atribute ] );
        }
        $filename = null;
        if ($image) {
            if (!$image->getClientOriginalExtension()) {
                if ($image->getMimeType() == '.pdf') {
                    $extension = 'pdf';
                }elseif($image->getMimeType() == '.jpg'||$image->getMimeType() == '.jpeg'){
                    $extension = 'jpg';
                }else{
                    $extension = 'png';
                }
            }else{
                $extension = $image->getClientOriginalExtension();
            }
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
    public function get_token()
    {
      if ( count(apiPdmToken::all()) > 0 ) {
        $apiPdmToken = apiPdmToken::latest('id')->first()->toArray();
      } else {
        $this->gen_token();
        $apiPdmToken = apiPdmToken::latest('id')->first()->toArray();
      }

      if ($apiPdmToken['expires_in'] >= date("Y-m-d H:i:s")) {
        $token = $apiPdmToken['access_token'];
        return $token;
      } else {
        $this->gen_token();
        $apiPdmToken = apiPdmToken::latest('id')->first()->toArray();

        $token = $apiPdmToken['access_token'];
        return $token;
      }
    }

    public function store( EFormRequest $request )
    {
        if ($request->product_type != 'kartu_kredit'){
            $nik = $request->nik;
            $check = CustomerDetail::where('nik', $nik)->get();
            $data = Eform::where('nik', $nik)->get();
            if ( count($check) == 0 ) {
                return response()->error( [
                    'message' => 'Data dengan nik tersebut tidak ditemukan!',
                    'contents' => $data,
                ], 422 );
            }
        }

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

            if ($request->product_type == 'kartu_kredit'){
                \Log::info("========================KARTU_KREDIT========================");
                //cek nik di customer detail, kalau gak ada di create
                $nik = $request->nik;
                $checkNik = CustomerDetail::where('nik',$nik)->get();
                if(count($checkNik) == 0){
                    return response()->json([
                        'responseCode' => '01',
                        'responseMessage' => "NIK tidak ditemukan"
                    ]);
                } else {
                    //nama gambar
                    $time = date('YmdHis');
                    $nik = $request->nik;
                    //cek debitur atau nasabah. ambil gambar
                    if ($request->jenis_nasabah == 'debitur'){
                        
                        $npwp = $request->NPWP;
                        $ktp = $request->KTP;
                        $slipGaji = $request->SLIP_GAJI;

                        // uploadKKImage($image,$nik,$type,$time)
                        $npwp = $this->uploadKKImage($npwp,$nik,'NPWP',$time);
                        $ktp = $this->uploadKKImage($ktp,$nik,'KTP',$time);
                        $slipGaji = $this->uploadKKImage($slipGaji,$nik,'SLIP_GAJI',$time);

                        $baseRequest['NPWP'] = $npwp;
                        $baseRequest['KTP'] = $ktp;
                        $baseRequest['SLIP_GAJI'] = $slipGaji;
                    }else{
                        $npwp = $request->NPWP;
                        $ktp = $request->KTP;
                        $slipGaji = $request->SLIP_GAJI;
                        $nameTag = $request->NAME_TAG;
                        $limitKartu = $request->KARTU_BANK_LAIN;

                        $npwp = $this->uploadKKImage($npwp,$nik,'NPWP',$time);
                        $ktp = $this->uploadKKImage($ktp,$nik,'KTP',$time);
                        $slipGaji = $this->uploadKKImage($slipGaji,$nik,'SLIP_GAJI',$time);
                        $nameTag = $this->uploadimage($nameTag,$nik,'NAME_TAG',$time);
                        $kartuBankLain = $this->uploadimage($limitKartu,$nik,"KARTU_BANK_LAIN",$time);

                        $baseRequest['NPWP'] = $npwp;
                        $baseRequest['KTP'] = $ktp;
                        $baseRequest['SLIP_GAJI'] = $slipGaji;
                        $baseRequest['NAME_TAG'] = $nameTag;
                        $baseRequest['KARTU_BANK_LAIN'] = $kartuBankLain;
                    }

                    $baseRequest['id_foto'] = $id;

                    //create eform
                    $kk = new KartuKredit();
                    //insert ke table eform
                    $eformCreate = $kk->createEform($baseRequest);
                    $eformId = $eformCreate['id'];
                    \Log::info("===========create eform==========");
                    //insert ke table kartu_kredit_details
                    $baseRequest['eform_id'] = $eformId;
                    $kkDetailsCreate = $kk->createKartuKreditDetails($baseRequest);
                    \Log::info("========crate kk details=============");
                    \Log::info($eformCreate);


                    //lengkapi data kredit di eform
                    $rangeLimit = $kkDetailsCreate['range_limit'];
                    $eform = EForm::where('id',$eformId)->update([
                        'kk_details'=>'{"range_limit":"'.$rangeLimit.'","is_analyzed":"false"}'
                    ]);


                    //cek dedup
                    $nik = $baseRequest['nik'];
                    $tokenLos = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwidXNlcm5hbWUiOiJsb3NhcHAiLCJhY2Nlc3MiOlsidGVzIl0sImp0aSI6IjhjNDNlMDNkLTk5YzctNDJhMC1hZDExLTgxODUzNDExMWNjNCIsImlhdCI6MTUxODY2NDUzOCwiZXhwIjoxNjA0OTc4MTM4fQ.ocz_X3duzyRkjriNg0nXtpXDj9vfCX8qUiUwLl1c_Yo';

                    $host = '10.107.11.111:9975/api/nik';
                    $header = ['access_token'=> $tokenLos];
                    $client = new Client();

                    try{
                        $res = $client->request('POST',$host, ['headers' =>  $header,
                            'form_params' => ['nik' => $nik]
                        ]);
                    } catch (RequestException $e){
                        return response()->error([
                            'responseCode'=>'01',
                            'responseMessage'=> $e->getMessage()
                        ],400);
                    }

                    $body = $res->getBody();
                    $obj = json_decode($body);
                    $responseCode = $obj->responseCode;

                    if ($responseCode == 0 || $responseCode == 00){
                        //langsung merah. update eform.
                        $updateEform = EForm::where('id',$eformId)->update([
                            'prescreening_status'=>3
                        ]);

                        return response()->json([
                            'responseCode' => '02',
                            'responseMessage' => 'Nasabah pernah mengajukan kartu kredit 6 bulan terakhir'
                        ]);
                    }

                    //berhasil lewat dedup

                    //cek jumlah kk
                    //pefindo dalam development. sabar ya :)
                    //update eform

                    DB::commit();

                    return response()->json([
                        'responseMessage' => 'Nasabah sukses melewati proses dedup.',
                        //balikin eform buat eform list di android
                        'eform_id' => $eformId

                    ]);
                }

            } else if ( $request->product_type == 'briguna' ) {
              \Log::info("=======================================================");
                $user_idsss = DB::table('customer_details')
                             ->select(DB::raw('customer_details.user_id'))
                             ->groupBy('customer_details.user_id')
                             ->where('customer_details.nik', $request->nik)
                             ->get();
                $user_idsss = $user_idsss->toArray();
                $user_idsss = json_decode(json_encode($user_idsss), True);
                $validasi_eform = 'false';
                if(!empty($user_idsss)){
                    $hasil = DB::table('eforms')
                         ->select(DB::raw('eforms."product_type",eforms."IsFinish"'))
                         ->groupBy(DB::raw('eforms."product_type",eforms."IsFinish"'))
                         ->where('eforms.user_id', $user_idsss[0])
                         ->get();
                        $hasil = $hasil->toArray();
                        $hasil = json_decode(json_encode($hasil), True);
                        $c = count($hasil)-1;
                        if(empty($hasil)){
                            $validasi_eform = 'true';
                        }elseif(!empty($hasil)&&$hasil[$c]['IsFinish']=='true'){
//                            if($hasil['product_type']=='briguna'){
                                if($hasil[$c]['IsFinish']=='true'){
                                $validasi_eform = 'true';
                                }
//                            }
                        }
                }
            /* BRIGUNA */
                if($validasi_eform=='true'){
                    $baseRequest['IsFinish'] = 'false';
                    $data_new['branch']=$request->input('branch_id');
                        $listExisting = $this->ListBranch($data_new);
/*                    if ( count(apiPdmToken::all()) > 0 ) {
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
                      } */
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
        //            $SK_AWAL = $request->SK_AWAL;
        //            $SK_AKHIR = $request->SK_AKHIR;
                    $REKOMENDASI = $request->REKOMENDASI;

                    $id = date('YmdHis');
                    $NPWP_nasabah = $this->uploadimage($NPWP_nasabah,$id,'NPWP_nasabah');
                    $KK = $this->uploadimage($KK,$id,'KK');
                    $SLIP_GAJI = $this->uploadimage($SLIP_GAJI,$id,'SLIP_GAJI');
        //            $SK_AWAL = $this->uploadimage($SK_AWAL,$id,'SK_AWAL');
        //            $SK_AKHIR = $this->uploadimage($SK_AKHIR,$id,'SK_AKHIR');
                    $REKOMENDASI = $this->uploadimage($REKOMENDASI,$id,'REKOMENDASI');

                    $baseRequest['NPWP_nasabah'] = $NPWP_nasabah;
                    $baseRequest['KK'] = $KK;
                    $baseRequest['SLIP_GAJI'] = $SLIP_GAJI;
        //            $baseRequest['SK_AWAL'] = $SK_AWAL;
        //
        //            $baseRequest['SK_AKHIR'] = $SK_AKHIR;
                    $baseRequest['REKOMENDASI'] = $REKOMENDASI;
                    $baseRequest['id_foto'] = $id;

                    $SK_AWAL = '';
                    $SK_AKHIR = '';

                    if($baseRequest['baru_atau_perpanjang']=='0' && $baseRequest['kredit_take_over']=='0'){
                        if(!empty($request->SK_AWAL) && !empty($request->SK_AKHIR)){
                            $SK_AWAL = $request->SK_AWAL;
                            $SK_AWAL = $this->uploadimage($SK_AWAL,$id,'SK_AWAL');
                            $baseRequest['SK_AWAL'] = $SK_AWAL;

                            $SK_AKHIR = $request->SK_AKHIR;
                            $SK_AKHIR = $this->uploadimage($SK_AKHIR,$id,'SK_AKHIR');
                            $baseRequest['SK_AKHIR'] = $SK_AKHIR;
                            /*----------------------------------*/
                        }else{
                            $dataEform =  EForm::where('nik', $request->nik)->get();
                            return response()->error( [
                                'message' => 'Baru Atau Perpanjangan Harus ada & Kredit Take Over harus Iya',
                                'contents' => $dataEform
                            ], 422 );
                        }
                    }else{
                        if(!empty($request->SK_AWAL) && !empty($request->SK_AKHIR)){
                            $SK_AWAL = $request->SK_AWAL;
                            $SK_AWAL = $this->uploadimage($SK_AWAL,$id,'SK_AWAL');
                            $baseRequest['SK_AWAL'] = $SK_AWAL;

                            $SK_AKHIR = $request->SK_AKHIR;
                            $SK_AKHIR = $this->uploadimage($SK_AKHIR,$id,'SK_AKHIR');
                            $baseRequest['SK_AKHIR'] = $SK_AKHIR;
                            /*----------------------------------*/
                        }else if(!empty($request->SK_AWAL) || !empty($request->SK_AKHIR)){
                            if(!empty($request->SK_AWAL)){
                                $baseRequest['SK_AKHIR'] = $SK_AKHIR;
                                $SK_AWAL = $request->SK_AWAL;
                                $SK_AWAL = $this->uploadimage($SK_AWAL,$id,'SK_AWAL');
                                $baseRequest['SK_AWAL'] = $SK_AWAL;

                            }else{
                                $baseRequest['SK_AWAL'] = $SK_AWAL;

                                $SK_AKHIR = $request->SK_AKHIR;
                                $SK_AKHIR = $this->uploadimage($SK_AKHIR,$id,'SK_AKHIR');
                                $baseRequest['SK_AKHIR'] = $SK_AKHIR;
                            }
                            /*----------------------------------*/
                        }else{
                            $baseRequest['SK_AWAL'] = $SK_AWAL;
                            $baseRequest['SK_AKHIR'] = $SK_AKHIR;
                        }
                    }

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
                        $customer = DB::table('customer_details')
                                 ->select('users.*','customer_details.*')
                                 ->join('users', 'users.id', '=', 'customer_details.user_id')
                                 ->where('customer_details.nik', $request->nik)
                                 ->get();

                        $customer = $customer->toArray();
                        $customer = json_decode(json_encode($customer), True);
                        $message = ['no_hp'=>$customer[0]['mobile_phone'],'no_reff'=>$kpr->ref_number,'nama_cust'=>$customer[0]['first_name'].' '.$customer[0]['last_name'],'kode_message'=>'1'];
                        \Log::info("-------------------sms notifikasi-----------------");
                        \Log::info($message);
                        $testing = app('App\Http\Controllers\API\v1\SentSMSNotifController')->sentsms($message);
                                        \Log::info($testing);
                        $return = [
                            'message' => 'Data e-form briguna berhasil ditambahkan.',
                            'contents' => $kpr
                        ];

                } else {
                        $dataEform =  EForm::where('nik', $request->nik)->get();
                        return response()->error( [
                            'message' => 'User sedang dalam pengajuan',
                            'contents' => $dataEform
                        ], 422 );
                }

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

                $dataEform =  EForm::where('nik', $request->nik)->where('product_type','kpr')->get();

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
                            'category' => $baseRequest['kpr_type_property'],
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

                        $baseProperty['region_id'] = 'Q';
                        if ( $getKanwil['responseCode'] == '00' ) {
                            foreach ($getKanwil['responseData'] as $kanwil) {
                                $branchid = substr( '00000' . $kanwil['branch'], -5 );
                                if ( $branchid == $request->input('branch_id') ) {
                                $baseProperty['region_id'] = $kanwil['region'];
                                $baseProperty['region_name'] = $kanwil['rgdesc'];
                                }
                            }
                        }

                        $property =  Property::create( $baseProperty );
                        $baseRequest['property'] = $property->id;
                        $baseRequest['property_name'] = $developer_name;
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
                            $baseRequest['property_type']= $propertyType->id;
                            $baseRequest['property_type_name']= $developer_name;
                            if ($propertyType) {
                                $data = [
                                'developer_id' => $developer_id,
                                'property_id' => $property->id,
                                'status' => Collateral::STATUS[0]
                            ];
                            $collateral = Collateral::updateOrCreate(['property_id' => $property->id],$data);
                            }
                        }
                    }

                    $kpr = KPR::create( $baseRequest );
                    $return = [
                        'message' => 'Data e-form berhasil ditambahkan.',
                        'contents' => $kpr['kpr']
                    ];

                    $userId = CustomerDetail::where('nik', $baseRequest['nik'])->first();
                    $usersModel = User::FindOrFail($userId['user_id']);     /*send notification*/

                    $credentials = [
                        'data'    => $kpr['eform'],
                        'request' => $request,
                    ];
                    pushNotification($credentials, 'createEForm');

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

        return response()->success($return, 201);
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
        try {
            DB::beginTransaction();
            $eform = EForm::findOrFail( $id );
            if (!empty($eform->ao_id)) {
                $message = 'Redisposisi';
            } else {
                $message = 'Disposisi';
            }
            $ao_id = substr( '00000000' . $request->ao_id, -8 );

            $baseRequest = [ 'ao_id' => $ao_id ];
            // Get User Login
            $user_login = \RestwsHc::getUser($ao_id);
            $baseRequest['pinca_note'] = $request->has('pinca_note') ? $request->pinca_note : 'Tidak Ada Note';
            $baseRequest['ao_name'] = $user_login['name'];
            $baseRequest['ao_position'] = $user_login['position'];
            if (isset($request->tgl_disposisi)) {
                $baseRequest['tgl_disposisi'] = date('Y-m-d H:i:s');
            }

            $eform->update( $baseRequest );

            $typeModule = getTypeModule(EForm::class);
            notificationIsRead($id, $typeModule);

            $usersModel = User::FindOrFail($eform->user_id);     /*send notification*/
            $usersModel->notify(new EFormPenugasanDisposisi($eform));

            //add scheduleData in Disposisition
            $scheduleData = array( 'ao_id' => $eform->ao_id );

            Appointment::where('eform_id', $eform->id)->update($scheduleData);

            // Credentials for push notification helper
            $credentials = [
                'eform' => $eform,
                'ao_id' => $ao_id,
            ];

            // Call the helper of push notification function
            pushNotification($credentials, 'disposition');
        } catch (Exception $e) {
            DB::rollback();
            return response()->error( [
                'message' => 'Terjadi Kesalahan Silahkan Tunggu Beberapa Saat Dan Ulangi',
            ], 422 );
        }
        DB::commit();

        set_action_date($eform->id, 'eform-disposition');

        return response()->success( [
            'message' => 'E-Form berhasil di '.$message,
            'contents' => $eform
        ], 201);
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
        $status = ($baseRequest->is_approved == "true" ? 'approveEForm' : 'rejectEForm');
        $eform = EForm::approve( $eform_id, $baseRequest );

        if( $eform['status'] ) {
            $data =  EForm::findOrFail($eform_id);

            $typeModule = getTypeModule(EForm::class);
            notificationIsRead($eform_id, $typeModule);

            if ($request->is_approved == "true") {
                $usersModel = User::FindOrFail($data->user_id);
                // Recontest
                if ( $currentStatus != 'Approval2' ) {
                    event( new Approved( $data ) );
                }

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

                set_action_date($detail->id, 'eform-recontest-approval');

            } else {
                $usersModel = User::FindOrFail($data->user_id);
                $detail = EForm::with( 'visit_report.mutation.bankstatement' )->findOrFail( $eform_id );
                generate_pdf('uploads/'. $detail->nik, 'lkn.pdf', view('pdf.approval', compact('detail')));

                set_action_date($detail->id, 'eform-approval');

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
                notificationIsRead($verify['contents']->id, $typeModule);

                $usersModel  = User::FindOrFail($verify['contents']->user_id);
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

            set_action_date($detail->id, 'customer-verification');

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
        if ($eform->product_type == 'kartu_kredit'){
          // $delete = EForm::where('id',$eform)->delete();
          try{
                $customer = DB::table('customer_details')
                         ->select('users.*','customer_details.*')
                         ->join('users', 'users.id', '=', 'customer_details.user_id')
                         ->where('customer_details.user_id', $eform->user_id)
                         ->get();

                \Log::info($customer);

                $customer = $customer->toArray();
                $customer = json_decode(json_encode($customer), True);


                $kk = KartuKredit::
                         where('eform_id', $request->eform_id)
                         ->get();

                $kk = $kk->toArray();
                $kk = json_decode(json_encode($kk), True);
                
                User::destroy($eform->user_id);
                DB::commit();
                return response()->success( [
                    'message' => 'Hapus User Berhasil',
                ], 200 );
            } catch (\Exception $e) {
                    DB::rollback();
                    return response()->error( [
                        'message' => 'User Tidak Dapat Dihapus',
                    ], 422 );
            }
        }else if($eform->product_type=='briguna'){
            try{

                $customer = DB::table('customer_details')
                         ->select('users.*','customer_details.*')
                         ->join('users', 'users.id', '=', 'customer_details.user_id')
                         ->where('customer_details.user_id', $eform->user_id)
                         ->get();
			
                $customer = $customer->toArray();
                $customer = json_decode(json_encode($customer), True);

            } catch (\Exception $e) {
                    DB::rollback();
                    return response()->error( [
                        'message' => 'User tidak ditemukan',
                    ], 422 );
            }
			try{

                $briguna = DB::table('briguna')
                         ->select('year','request_amount')
                         ->where('briguna.eform_id', $request->eform_id)
                         ->get();

                $briguna = $briguna->toArray();
                $briguna = json_decode(json_encode($briguna), True);

            } catch (\Exception $e) {
                    DB::rollback();
                    return response()->error( [
                        'message' => 'Data pengajuan tidak ditemukan',
                    ], 422 );
            }
			try{			
				$message = ['no_hp'=>$customer[0]['mobile_phone'],
                            'plafond'=>$briguna[0]['request_amount'],
                            'year'=>$briguna[0]['year'],
                            'nama_cust'=>$customer[0]['first_name'].' '.$customer[0]['last_name'],
                            'kode_message'=>'5'];
                \Log::info("-------------------sms notifikasi-----------------");
                \Log::info($message);
                $testing = app('App\Http\Controllers\API\v1\SentSMSNotifController')->sentsms($message);
                                \Log::info($testing);

            } catch (\Exception $e) {
                    DB::rollback();
                    return response()->error( [
                        'message' => 'Gagal Mengirimkan SMS',
                    ], 422 );
            }
			try{
                    User::destroy($eform->user_id);
                  DB::commit();
                return response()->success( [
                    'message' => 'Hapus User Berhasil',
                ], 200 );
            } catch (\Exception $e) {
                    DB::rollback();
                    return response()->error( [
                        'message' => 'User Tidak Dapat Dihapus',
                    ], 422 );
            }
        }else{
            if ($eform->kpr->is_sent == false || $eform->status_eform == 'Rejected' ) {
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
        $status  = $request->input('status');
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
                    notificationIsRead($data->id, $typeModule);

                    $usersModel  = User::FindOrFail($data['user_id']);
                    $credentials = [
                        'data'  => $data,
                        'user'  => $usersModel,
                        'clas'  => true,
                    ];

                    // Call the helper of push notification function
                    $slug = 'CLASEForm';
                    if ( $status == "Approval1" ) {
                        $slug = 'approveEForm';

                    } elseif ( $status == "Approval2" ) {
                        $slug = 'recontestEForm';

                    } elseif ( $status == "Rejected" ) {
                        $slug = 'rejectEForm';

                    } elseif ( $status == "Pencairan" ) {
                        $slug = 'pencairanEForm';

                    }

                    pushNotification($credentials, $slug);

                    set_action_date(
                        $data->id
                        , 'customer-clas-' . strtolower( $request->input('status') )
                    );

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

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function submitScreening( Request $request )
    {
        DB::beginTransaction();

        $message = 'Screening e-form gagal di update.';

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

            $message = 'Screening e-form berhasil di update.';
        }

        $eform = array();

        DB::commit();
        return response()->success( [
            'message' => $message,
            'contents' => $eform
        ], 201 );
    }

}
