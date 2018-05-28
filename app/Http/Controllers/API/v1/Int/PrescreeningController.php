<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Jobs\GeneratePefindoJob;
use App\Models\Screening;
use App\Models\EForm;
use DB;

class PrescreeningController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $limit = $request->input( 'limit' ) ?: 10;
        $screening = Screening::filter( $request )->paginate( $limit );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $screening
        ], 200 );
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show( Request $request )
    {
        $user_login = \RestwsHc::getUser();

        $data = EForm::findOrFail($request->eform);
        if ( $data->customer ) {
            $personal = $data->customer->personal;
        }

        $dhn = json_decode((string) $data->dhn_detail);
        if ( !isset($dhn->responseData) ) {
            $dhn = json_decode((string) '[{"kategori":null,"keterangan":"","warna":"Hijau","result":""}]');

        } else {
            $dhn = $dhn->responseData;
        }

        $sicd = json_decode((string) $data->sicd_detail);
        if ( !isset($sicd->responseData) ) {
            $sicd = json_decode((string) '[{"status":null,"acctno":null,"cbal":null,"bikole":null,"result":null,"cif":null,"nama_debitur":null,"tgl_lahir":null,"alamat":null,"no_identitas":null}]');

        } else {
            $sicd = $sicd->responseData;
        }

        $pefindo = json_decode((string) $data->pefindo_detail);
        if ( !$data->pefindo_detail ) {
            $pefindo = (object) [];
        }

        $data['uploadscore'] = $this->generatePDFUrl( $data );

        return response()->success( [
            'message' => 'Data Screening e-form',
            'contents' => [
                'eform' => $data
                , 'dhn' => $dhn
                , 'sicd' => $sicd
                , 'pefindo' => $pefindo
            ]
        ], 200 );
    }

    /**
     * Generate all depedencies.
     *
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request )
    {
        $eform = EForm::findOrFail( $request->input('eform') );

        foreach ( array( 'sicd', 'dhn' ) as $key) {
            if ( !$eform->{$key.'_detail'} ) {
                ${$key} = $this->dependencies( $key, $eform );
            } else {
                ${$key} = json_decode((string) $eform->{$key.'_detail'});
                ${$key} = ${$key}->responseData;
            }
        }

        if( env( 'AUTO_PRESCREENING', false ) ){
            if ( !$eform->pefindo_detail ) {
                $this->pefindo( $eform );
            }
        }

        $eform['uploadscore'] = $this->generatePDFUrl( $eform );

        $pefindo = json_decode((string) $eform->pefindo_detail);
        if ( !$eform->pefindo_detail ) {
            $pefindo = (object) [];
        }

        set_action_date($eform->id, 'eform-prescreening');

        return response()->success( [
            'message' => 'Data Store Screening e-form',
            'contents' => [
                'eform' => $eform
                , 'dhn' => $dhn
                , 'sicd' => $sicd
                , 'pefindo' => $pefindo
            ]
        ], 200 );
    }

    /**
     * Finalize prescreening data.
     *
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, $prescreening )
    {
        // Get User Login
        $user_login = \RestwsHc::getUser();
        $eform = EForm::findOrFail( $prescreening );
        $waiting = false;

        $updateData = [
            'selected_sicd' => $request->input('select_sicd')
            , 'selected_dhn' => $request->input('select_dhn')
            , 'prescreening_name' => $user_login['name']
            , 'prescreening_position' => $user_login['position']
        ];

        if ( $request->has('select_individual_pefindo') || $request->has('select_couple_pefindo') ) {
            if ( ENV('DELAY_PRESCREENING', false) ) {
                $waiting = true;
                $message = 'Prescreening sudah pernah di lakukan';

                if ( $eform->delay_prescreening == 0 ) {
                    dispatch( new GeneratePefindoJob( $eform, $request->all() ) );
                    $updateData[ 'delay_prescreening' ] = 1;
                    $message = 'Hasil prescreening sedang dalam proses';
                }

            } else {
                $returnData = break_pefindo( $eform, $request );

            }

        } else {
            $score = $eform->pefindo_score;
            $pefindoC = 'Kuning';
            if ( $score >= 250 && $score <= 529 ) {
                $pefindoC = 'Merah';

            } elseif ( ( $score >= 677 && $score <= 900 ) || $score == 999 ) {
                $pefindoC = 'Hijau';

            }

            $returnData = [
                'risk' => $eform->ket_risk
                , 'pefindo' => [
                        'color' => $pefindoC
                        , 'score' => $score
                    ]
                , 'selected_pefindo' => 0
                , 'pdf' => $eform->uploadscore
                , 'pefindo_score_all' => [
                        'individual' => [
                            "0" => [
                                'color' => $pefindoC
                                , 'score' => $score
                            ]
                        ]
                    ]
            ];
        }

        if ( !$waiting ) {
            $updateData = array_merge(
                $updateData
                , generate_data_prescreening( $eform, $request, $returnData )
            );

            $message = 'Berhasil proses prescreening E-Form';
            set_action_date($eform->id, 'eform-prescreening-update');

        }

        $eform->update( $updateData );

        if ( !$waiting ) {
            $detail = $eform;
            generate_pdf('uploads/'. $detail->nik, $detail->ref_number.'-prescreening.pdf', view('pdf.prescreening', compact('detail')));
        }

        // auto approve for VIP
        if ( $eform->is_clas_ready ) {
            $message .= ' dan ' . autoApproveForVIP( array(), $eform->id );
        }

        return response()->success( [
            'message' => $message,
            'contents' => $eform
        ], 200 );
    }

    /**
     * Collection all service data
     *
     * @return \Illuminate\Http\Response
     */
    public function dependencies( $type, $eform )
    {
        $personal = $eform->customer->personal;

        $endpoint = 'get_' . $type . '_consumer';

        $requestData = array(
            'id_user' => request()->header( 'pn' ),
            'nik' => $eform->nik,
            'nama_nasabah' => strtolower($personal['first_name'].' '.$personal['last_name']),
            'tgl_lahir' => $personal['birth_date']
        );

        if ( $type == "sicd" ) {
            $requestData['kode_branch'] = $eform->branch_id;
        }

        $defaultValue = array(
            'warna' => 'Hijau'
        );

        if ( $type == "sicd" ) {
            $defaultValue = array(
                "status" => null
                , "acctno" => null
                , "cbal" => null
                , "bikole" => null
                , "result" => null
                , "cif" => null
                , "nama_debitur" => null
                , "tgl_lahir" => null
                , "alamat" => null
                , "no_identitas" => null
            );
        }

        $base = $this->getService( $endpoint, $requestData, false, array(), $defaultValue );

        try {
            if ( $personal['status_id'] == 2 ) {
                $requestData['nik'] = $personal['couple_nik'];
                $requestData['nama_nasabah'] = strtolower($personal['couple_name']);
                $requestData['tgl_lahir'] = $personal['couple_birth_date'];

                $base = $this->getService( $endpoint, $requestData, true, $base, $defaultValue );
            }
        } catch (Exception $e) {
            \Log::info("=====================data ".$type." pasangan salaaah====================");
            \Log::info($e);
        }

        $eform->update([
           $type . '_detail' => json_encode($base)
           , 'selected_' . $type => 0
        ]);

        return $base['responseData'];
    }

    /**
     * Hit service
     *
     * @return \Illuminate\Http\Response
     */
    public function getService( $endpoint, $requestData, $couple = false, $base = array(), $defaultValue )
    {
        $return = \RestwsHc::setBody( [
            'request' => json_encode( [
                'requestMethod' => $endpoint,
                'requestData' => $requestData
            ] )
        ] )->post( 'form_params' );

        if ($return['responseCode'] != '00') {
            if ( !$couple ) {
                $base = ['responseData' => [$defaultValue], 'responseCode' => '01'];

            } else {
                $base['responseData'][] = $defaultValue;

            }

        } else {
            if ( !$couple ) {
                $base = $return;
            } else {
                foreach ($return['responseData'] as $responseData) {
                    $base['responseData'][] = $responseData;
                }

            }

        }

        return $base;
    }

    /**
     * Collection all pefindo service data
     *
     * @return \Illuminate\Http\Response
     */
    public function pefindo( $eform )
    {
        $personal = $eform->customer->personal;

        $pefindo = get_pefindo_service( $eform, 'search', false, null );
        $pefindoCouple = array();

        try {
            if ( $personal['status_id'] == 2 ) {
                $pefindoCouple = get_pefindo_service( $eform, 'search', true );

            }
        } catch (Exception $e) {
            \Log::info("=====================data ".$type." pasangan salaaah====================");
            \Log::info($e);
        }

        $eform->update([
            'pefindo_detail' => json_encode(
                array(
                    'individual' => $pefindo
                    , 'couple' => $pefindoCouple
                )
            )
            , 'selected_pefindo' => 0
        ]);
    }

    /**
     * change image format
     *
     * @return string
     **/
    public function generatePDFUrl( $eform )
    {
        $html = '';

        foreach (explode(',', $eform->uploadscore) as $value) {
            if ($value != '') {
                $host = env('APP_URL');
                if($host == 'http://103.63.96.167/api/'){     
                    $html .= 'http://103.63.96.167/api/uploads/'.$eform->nik.'/'.$value. ',';
                }else{
                    $html .= asset('uploads/'.$eform->nik.'/'.$value) . ',';
                }
                
            }
        }

        return $html;
    }

    /**
     * Get auto prescreening flag
     *
     * @return string
     **/
    public function getIsAutoPrescreening()
    {
        return response()->success( [
            'message' => 'Sukses',
            'contents' => array(
                'auto_prescreening' => env( 'AUTO_PRESCREENING', false )
                , 'delay_prescreening' => env( 'DELAY_PRESCREENING', false )
            )
        ], 200 );
    }
}
