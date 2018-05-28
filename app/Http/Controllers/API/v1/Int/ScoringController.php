<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\ScoringRequest;
use App\Models\Scoring;
use App\Models\EForm;
use App\Models\User;
use Sentinel;
use File;
use DB;
use App\Notifications\ScorePefindoPreScreening;
use App\Models\UserNotification;

class ScoringController extends Controller
{
    public function __construct(UserNotification $userNotification)
    {
        $this->userNotification = $userNotification;
    }

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
     * @param  \App\Http\Requests\API\v1\ScoringRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storebefore( ScoringRequest $request )
    {

        $data = $request->all();
        if($data['product_leads']=='kpr'){
                DB::beginTransaction();
            $customer = Customer::create( $request->all() );

            DB::commit();
            return response()->success( [
                'message' => 'Data nasabah berhasil ditambahkan.',
                'contents' => $customer
            ], 201 );
        }elseif($data['product_leads']=='briguna'){
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

     public function uploadimage($image,$id){
        $eform = EForm::where('id', $id)->first();
        $path = public_path( 'uploads/' . $eform->nik . '/' );

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
            $filename = $id . '-pefindo-report.' . $extension;
            $image->move( $path, $filename );
        }
        return $filename;

     }

     public function uploadimagemulti($image,$id,$i){
        $eform = EForm::where('id', $id)->first();
        $path = public_path( 'uploads/' . $eform->nik . '/' );

        if (!$image->getClientOriginalExtension()) {
            if ($image->getMimeType() == '.pdf') {
                $extension = '.pdf';
            }else{
                $extension = 'png';
            }
        }else{
            $extension = $image->getClientOriginalExtension();
        }

        $filename = $id.'-'.$i.'-pefindo-report.' . $extension;
        $image->move( $path, $filename );
        return $filename;

     }

    /**
     * Remove all stored image
     *
     * @return void
     **/
    public function removeAllImage( $eform )
    {
        $path = public_path( 'uploads/' . $eform->nik . '/' );

        foreach (explode(',', $eform->uploadscore) as $image) {
            if ( ! empty( $image ) ) {
                File::delete( $path . $image );
            }
        }
    }

    public function store( ScoringRequest $request )
    {
        $id = $request->id;
        $filename2 = '';
        $countu = $request->countupload;
        $image = $request->uploadscore;

        $detail = EForm::findOrFail( $id );
        $personal = $detail->customer->personal;
        $this->removeAllImage( $detail );

        $filename = $this->uploadimage($image,$id);
        $dats = $request->except('id');
        if($countu>2){
        for($i=2;$i<$countu;$i++){
            $image = $request['uploadscore'.$i];
            $filename2 .= $this->uploadimagemulti($image,$id,$i).',';
            unset($dats['uploadscore'.$i]);
        }
        }
        unset($dats['countupload']);

        $dats['uploadscore'] = $filename;
        if ($filename2 != '') {
            $dats['uploadscore'] .= ','.$filename2;

        }

        // Get pefindo
        $score = $request->input('pefindo_score');
        $pefindoC = 'Kuning';
        if ( $score >= 250 && $score <= 529 ) {
            $pefindoC = 'Merah';

        } elseif ( ( $score >= 677 && $score <= 900 ) || $score == 999 ) {
            $pefindoC = 'Hijau';

        }

        // Get DHN
        $dhn = \RestwsHc::setBody( [
            'request' => json_encode( [
                'requestMethod' => 'get_dhn_consumer',
                'requestData' => [
                    'id_user' => request()->header( 'pn' ),
                    'nik'=> $detail->nik,
                    'nama_nasabah'=> strtolower($personal['first_name'].' '.$personal['last_name']),
                    'tgl_lahir'=> $personal['birth_date']
                ]
            ] )
        ] )->post( 'form_params' );
        \Log::info("==============dhn====================");
        \Log::info($dhn);

        if ($dhn['responseCode'] != '00') {
            $dhn = ['responseData' => [['warna' => 'Hijau']], 'responseCode' => '01'];

        }
        \Log::info($dhn);

        $pasangan = false;
        try {
            if ( $personal['status_id'] == 2 ) {
                $dhnPasangan = \RestwsHc::setBody( [
                    'request' => json_encode( [
                        'requestMethod' => 'get_dhn_consumer',
                        'requestData' => [
                            'id_user' => request()->header( 'pn' ),
                            'nik'=> $personal['couple_nik'],
                            'nama_nasabah'=> strtolower($personal['couple_name']),
                            'tgl_lahir'=> $personal['couple_birth_date']
                        ]
                    ] )
                ] )->post( 'form_params' );
                \Log::info("==============dhnPasangan====================");
                \Log::info($dhnPasangan);

                if ($dhnPasangan['responseCode'] != '00') {
                    $dhn['responseData'][] = ['warna' => 'Hijau'];

                } else {
                    $dhn['responseData'] = array_merge(
                        $dhn['responseData']
                        , $dhnPasangan['responseData']
                    );

                }
                \Log::info($dhn);
                $pasangan = true;
            }
        } catch (Exception $e) {
            \Log::info("=====================data DHN pasangan salaaah====================");
            \Log::info($e);
        }

        $dhnC = $dhn['responseData'][0]['warna'];
        $selectedDHN = 0;

        if ( $pasangan ) {
            foreach ($dhn['responseData'] as $key => $responseData) {
                $doAction = false;

                if ( ( $responseData['warna'] == 'Kuning' && $dhnC != 'Merah' ) || ( $responseData['warna'] == 'Merah' ) ) {
                    $doAction = true;

                }

                if ( $doAction ) {
                    $dhnC = $responseData['warna'];
                    $selectedDHN = $key;
                }
            }
        }

        // Get SICD
        $sicd = \RestwsHc::setBody( [
            'request' => json_encode( [
                'requestMethod' => 'get_sicd_consumer',
                'requestData' => [
                    'id_user' => request()->header( 'pn' ),
                    'nik'=> $detail->nik,
                    'nama_nasabah'=> strtolower($personal['first_name'].' '.$personal['last_name']),
                    'tgl_lahir'=> $personal['birth_date'],
                    'kode_branch'=> $detail->branch_id
                ]
            ] )
        ] )->post( 'form_params' );
         \Log::info($sicd);

        if ($sicd['responseCode'] != '00') {
            $sicd = ['responseData' => [["status" => null, "acctno" => null, "cbal" => null, "bikole" => null, "result" => null, "cif" => null, "nama_debitur" => null, "tgl_lahir" => null, "alamat" => null, "no_identitas" => null]], 'responseCode' => '01'];

        }

        $pasangan = false;
        try {
            if ( $personal['status_id'] == 2 ) {
                $sicdPasangan = \RestwsHc::setBody( [
                    'request' => json_encode( [
                        'requestMethod' => 'get_sicd_consumer',
                        'requestData' => [
                            'id_user' => request()->header( 'pn' ),
                            'nik'=> $personal['couple_nik'],
                            'nama_nasabah'=> strtolower($personal['couple_name']),
                            'tgl_lahir'=> $personal['couple_birth_date'],
                            'kode_branch'=> $detail->branch_id
                        ]
                    ] )
                ] )->post( 'form_params' );
                \Log::info("==============sicdPasangan====================");
                \Log::info($sicdPasangan);

                if ($sicdPasangan['responseCode'] != '00') {
                    $sicd['responseData'][] = ["status" => null, "acctno" => null, "cbal" => null, "bikole" => null, "result" => null, "cif" => null, "nama_debitur" => null, "tgl_lahir" => null, "alamat" => null, "no_identitas" => null];

                } else {
                    $sicd['responseData'] = array_merge(
                        $sicd['responseData']
                        , $sicdPasangan['responseData']
                    );

                }
                \Log::info($sicd);
                $pasangan = true;
            }
        } catch (Exception $e) {
            \Log::info("=====================data DHN pasangan salaaah====================");
            \Log::info($e);
        }

        $target = 1;
        $selected = 0;

        foreach ($sicd['responseData'] as $index => $responseData) {
            if ($sicd['responseCode'] == '00') {
                $date = explode(" ", $responseData['tgl_lahir']);

                $doAction = false;

                if ( $pasangan ) {
                    if ( strtoupper($responseData['nama_debitur']) == strtoupper($personal['couple_name']) && $personal['couple_birth_date'] == $date[0] && $personal['couple_nik'] == $responseData['no_identitas'] ) {
                        $doAction = true;
                    }
                }

                if ( ( strtoupper($responseData['nama_debitur']) == strtoupper($personal['first_name'].' '.$personal['last_name']) && $personal['birth_date'] == $date[0] && $personal['nik'] == $responseData['no_identitas'] ) || $doAction ) {
                    if ( $responseData['bikole'] > $target ) {
                        $target = $responseData['bikole'];
                        $selected = $index;
                    }

                }
            }
        }

        if ( $target == 1 ) {
            $sicdC = 'Hijau';

        } elseif ( $target == 2 ) {
            $sicdC = 'Kuning';

        } else {
            $sicdC = 'Merah';

        }

        $calculate = array($pefindoC, $dhnC, $sicdC);

        if ( in_array('Merah', $calculate) ) {
            $result = '3';

        } else if ( in_array('Kuning', $calculate) ) {
            $result = '2';

        } else {
            $result = '1';

        }

        $dats['prescreening_status'] = $result;
        $dats['dhn_detail'] = json_encode($dhn);
        $dats['sicd_detail'] = json_encode($sicd);
        $dats['selected_sicd'] = $selected;
        $dats['selected_dhn'] = $selectedDHN;
        $dats['is_screening'] = 1;
        $dats['pefindo_score_all'] = json_encode(['individual' => ["0" => ['color' => $pefindoC, 'score' => $score]]]);

        // Get User Login
        $user_login = \RestwsHc::getUser();
        $dats['prescreening_name'] = $user_login['name'];
        $dats['prescreening_position'] = $user_login['position'];

        DB::beginTransaction();
        $detail->update($dats);

        $eform = EForm::findOrFail( $id );
        $message = 'Data nasabah berhasil dirubah';
        // auto approve for VIP
        if ( $eform->is_clas_ready ) {
            $message .= ' dan ' . autoApproveForVIP( array(), $eform->id );
        }

        $usersModel = User::FindOrFail($eform->user_id);     /*send notification*/
        $credentials = [
            'data' => $eform,
            'user' => $usersModel
        ];
        // Call the helper of push notification function

        $typeModule = getTypeModule(Scoring::class);
        notificationIsRead($id, $typeModule);

        pushNotification($credentials, 'prescreening');

        generate_pdf('uploads/'. $detail->nik, $detail->ref_number.'-prescreening.pdf', view('pdf.prescreening', compact('detail')));
        DB::commit();

        set_action_date($eform->id, 'eform-prescreening');

        return response()->success( [
            'message' => $message,
            'contents' => $detail
        ] );
    }

    public function show( $id )
    {
        $customer = Customer::findOrFail( $id );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $customer
        ], 200 );
    }

}
