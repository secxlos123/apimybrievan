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
use App\Models\KPR;
use App\Models\BRIGUNA;
use DB;

class EFormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Display the specified resource.
     *
     * @param  string $type
     * @param  integer $eform_id
     * @return \Illuminate\Http\Response
     */
    public function show( $type, $eform_id )
    {
        $eform = EForm::with( 'visit_report.mutation.bankstatement' )->findOrFail( $eform_id );

        $get_user_info_service = \RestwsHc::setBody( [
            'request' => json_encode( [
                'requestMethod' => 'get_user_info',
                'requestData' => [
                    'id_cari' => $eform->ao_id,
                    'id_user' => request()->header( 'pn' )
                ]
            ] )
        ] )->setHeaders( [
            'Authorization' => request()->header( 'Authorization' )
        ] )->post( 'form_params' );

        $eform = $eform->toArray();
        if ( $get_user_info_service['responseCode'] == '00' ) {
            $eform['branch'] = $get_user_info_service['responseData']['WERKS_TX'];
        }

        return response()->success( [
            'contents' => $eform
        ] );
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

        $baseRequest = $request->all();

        if ( $request->product_type == 'kpr' ) {
            if ($baseRequest['status_property'] != ENV('DEVELOPER_KEY', 1)) {
                $baseRequest['developer'] = ENV('DEVELOPER_KEY', 1);
                $baseRequest['developer_name'] = ENV('DEVELOPER_NAME', "Non Kerja Sama");
            }
        }

        \Log::info($baseRequest);

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
        \Log::info("=======================================================");
        \Log::info($baseRequest);


        if ( $request->product_type == 'briguna' ) {
            $kpr = BRIGUNA::create( $baseRequest );

        } else {
            $kpr = KPR::create( $baseRequest );

        }

        DB::commit();
        return response()->success( [
            'message' => 'Data e-form berhasil ditambahkan.',
            'contents' => $kpr
        ], 201 );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\API\v1\EFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function submitScreening( EFormRequest $request )
    {
        DB::beginTransaction();
        $eform = EForm::findOrFail( $request->id );
        $eform->update( [ 'prescreening_status' => $request->prescreening_status ] );

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

        $dhn = \RestwsHc::setBody( [
            'request' => json_encode( [
                'requestMethod' => 'get_dhn_consumer',
                'requestData' => [
                    'id_user' => request()->header( 'pn' ),
                    'nik'=> $data->nik,
                    'nama_nasabah'=> strtolower($personal['first_name'].' '.$personal['last_name']),
                    'tgl_lahir'=> $personal['birth_date']
                ]
            ] )
        ] )->setHeaders( [
            'Authorization' => request()->header( 'Authorization' )
        ] )->post( 'form_params' );
        \Log::info($dhn);

        if ($dhn['responseCode'] != '00') {
            $dhn = ['responseData' => [['warna' => 'Hijau']], 'responseCode' => '01'];

        }

        $sicd = \RestwsHc::setBody( [
            'request' => json_encode( [
                'requestMethod' => 'get_sicd_consumer',
                'requestData' => [
                    'id_user' => request()->header( 'pn' ),
                    'nik'=> $data->nik,
                    'nama_nasabah'=> strtolower($personal['first_name'].' '.$personal['last_name']),
                    'tgl_lahir'=> $personal['birth_date'],
                    'kode_branch'=> $data->branch_id
                ]
            ] )
        ] )->setHeaders( [
            'Authorization' => request()->header( 'Authorization' )
        ] )->post( 'form_params' );
         \Log::info($sicd);

        if ($sicd['responseCode'] != '00') {
            $sicd = ['responseData' => [['bikole' => '-']], 'responseCode' => '01'];

        }

        // $score = $data->pefindo_score;
        // $pefindoC = 'Kuning';
        // if ( $score >= 250 && $score <= 573 ) {
        //     $pefindoC = 'Merah';

        // } elseif ( $score >= 677 && $score <= 900 ) {
        //     $pefindoC = 'Hijau';

        // }

        // $dhnC = $dhn['responseData'][0]['warna'];

        // if ( $sicd['responseData'][0]['bikole'] == 1 || $sicd['responseData'][0]['bikole'] == '-' || $sicd['responseData'][0]['bikole'] == null) {
        //     $sicdC = 'Hijau';

        // } elseif ( $sicd['responseData'][0]['bikole'] == 2 ) {
        //     $sicdC = 'Kuning';

        // } else {
        //     $sicdC = 'Merah';

        // }

        // $calculate = array($pefindoC, $dhnC, $sicdC);

        // if ( in_array('Merah', $calculate) ) {
        //     $result = '3';

        // } else if ( in_array('Kuning', $calculate) ) {
        //     $result = '2';

        // } else {
        //     $result = '1';

        // }

        // $data->update([
        //     'prescreening_status' => $result
        //     , 'dhn_detail' => json_encode($dhn['responseData'])
        //     , 'sicd_detail' => json_encode($sicd['responseData'])
        // ]);

        $explode = explode(',', $data->uploadscore);
        $html = '';

        foreach ($explode as $value) {
            if ($value != '') {
                $html .= asset('uploads/prescreening/'.$data->id.'/'.$value) . ',';
            }
        }

        $data['uploadscore'] = $html;

        if ($dhn['responseCode'] == '00' && $sicd['responseCode']== '00') {
            return response()->success( [
                'message' => 'Data Screening e-form',
                'contents' => [
                    'eform' => $data,
                    'dhn'=>$dhn['responseData'],
                    'sicd' => $sicd['responseData']
                ]
            ], 200 );

        }

        return response()->error( [
            'message' => 'Data Screening Tidak Ditemukan',
            'contents' => [
                'eform' => $data
                , 'dhn'=> [
                    [
                        'kategori'=>'-',
                        'keterangan'=>'-',
                        'warna'=>'Hijau',
                        'result'=>'-'
                    ]
                ]
                , 'sicd'=> [
                    [
                        'status'=>'-',
                        'acctno'=>'-',
                        'cbal'=>'-',
                        'bikole'=>'-',
                        'result'=>'-',
                        'cif'=>'-',
                        'nama_debitur'=>'-',
                        'tgl_lahir'=>'-',
                        'alamat'=>'-',
                        'no_identitas'=>'-'
                    ]
                ]
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
        DB::beginTransaction();
        $eform = EForm::findOrFail( $id );
        $ao_id = substr( '00000000' . $request->ao_id, -8 );
        $eform->update( [ 'ao_id' => $ao_id ] );

        DB::commit();
        return response()->success( [
            'message' => 'E-Form berhasil di disposisi',
            'contents' => $eform
        ], 201 );
    }

    /**
     * Set E-Form AO disposition.
     *
     * @param integer $eform_id
     * @param  \App\Http\Requests\API\v1\EFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function approve( EFormRequest $request, $eform_id )
    {
        DB::beginTransaction();
        $eform = EForm::approve( $eform_id, $request );
        if( $eform['status'] ) {

                $data =  EForm::findOrFail($eform_id);
                if ($request->is_approved) {
                    event( new Approved( $data ) );
                }
                else
                {
                    event( new RejectedEform( $data ) );
                }
                DB::commit();
                return response()->success( [
                'message' => 'E-form berhasil di' . ( $request->is_approved ? 'approve.' : 'reject.' ),
                'contents' => $eform
            ], 201 );

        } else {
            DB::rollback();
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
        dd( $result );
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
}
