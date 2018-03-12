<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Screening;
use App\Models\EForm;
use DB;

use Zip;
use File;
use Asmx;
use RestwsHc;

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

            // change request by Mas Singh
            // if ( $user_login['role'] == 'ao' ) {
            // } else {
            //     $dhn = array($dhn->responseData[ intval($data->selected_dhn) ]);
            // }
        }

        $sicd = json_decode((string) $data->sicd_detail);
        if ( !isset($sicd->responseData) ) {
            $sicd = json_decode((string) '[{"status":null,"acctno":null,"cbal":null,"bikole":null,"result":null,"cif":null,"nama_debitur":null,"tgl_lahir":null,"alamat":null,"no_identitas":null}]');

        } else {
            $sicd = $sicd->responseData;

            // change request by Mas Singh
            // if ( $user_login['role'] == 'ao' ) {
            // } else {
            //     $sicd = array($sicd->responseData[ intval($data->selected_sicd) ]);
            // }
        }

        $data['uploadscore'] = $this->generatePDFUrl( $data );

        return response()->success( [
            'message' => 'Data Screening e-form',
            'contents' => [
                'eform' => $data
                , 'dhn' => $dhn
                , 'sicd' => $sicd
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
                $this->dependencies( $key, $eform );
            }
        }
        if( env( 'AUTO_PRESCREENING', false ) ){
            if ( !$eform->pefindo_detail ) {
                $this->pefindo( $eform );
            }
        }

        $eform['uploadscore'] = $this->generatePDFUrl( $eform );

        $detail = $eform;
        generate_pdf('uploads/'. $detail->nik, 'prescreening.pdf', view('pdf.prescreening', compact('detail')));

        return response()->success( [
            'message' => 'Data Screening e-form',
            'contents' => $eform
        ], 200 );
    }

    /**
     * Finalize prescreening data.
     *
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, $prescreening )
    {
        $eform = EForm::findOrFail( $prescreening );

        $pefindoDetail = json_decode($eform['pefindo_detail']);
        $dhnDetail = json_decode($eform['dhn_detail']);
        $sicdDetail = json_decode($eform['sicd_detail']);

        $sicd = $sicdDetail->responseData[ $request->input('select_sicd') ];
        $dhn = $dhnDetail->responseData[ $request->input('select_dhn') ];

        if ( $request->has('select_individual_pefindo') || $request->has('select_couple_pefindo') ) {
            if ( $request->has('select_individual_pefindo') ) {
                $individu = $pefindoDetail->individual[ $request->input('select_individual_pefindo') ];
                $dataIndividu = $this->getPefindo( $eform, 'data', false, $individu->PefindoId );
                $pdf = $this->getPefindo( $eform, 'pdf', false, $individu->PefindoId );
                $pefindo = $this->getColorPefindo( $dataIndividu['score'], false, array(), $request->input('select_individual_pefindo'), $dataIndividu['reasonslist'] );

            }

            if ( $request->has('select_couple_pefindo') ) {
                $couple = $pefindoDetail->couple[ $request->input('select_couple_pefindo') ];
                $dataCouple = $this->getPefindo( $eform, 'data', true, $couple->PefindoId );
                $pdf .= ',' . $this->getPefindo( $eform, 'pdf', true, $couple->PefindoId );
                $pefindo = $this->getColorPefindo( $dataCouple['score'], true, $pefindo, $request->input('select_couple_pefindo'), $dataCouple['reasonslist'] );
            }

            $risk = array();
            if ( isset( $pefindo['risk'] ) ) {
                foreach ($pefindo['risk'] as $value) {
                    $risk[] = $value['description'];
                }
            }

            $risk = implode(', ', $risk);
            $selected_pefindo = json_encode( array($pefindo['key'] => $pefindo['index']) );

        } else {
            $risk = $eform->ket_risk;
            $pdf = $eform->uploadscore;
            $selected_pefindo = 0;

            $score = $eform->pefindo_score;
            $pefindoC = 'Kuning';
            if ( $score >= 250 && $score <= 529 ) {
                $pefindoC = 'Merah';

            } elseif ( $score >= 677 && $score <= 900 ) {
                $pefindoC = 'Hijau';

            }

            $pefindo = array(
                'color' => $pefindoC
                , 'score' => $score
            );

        }

        $eform->update([
            'prescreening_status' => $this->getResult( $dhn->warna, $this->getColorSicd( $sicd->bikole ), $pefindo['color'] )
            , 'selected_sicd' => $request->input('select_sicd')
            , 'selected_dhn' => $request->input('select_dhn')
            , 'pefindo_score' => $pefindo['score']
            , 'selected_pefindo' => $selected_pefindo
            , 'is_screening' => 1
            , 'ket_risk' => $risk
            , 'uploadscore' => $pdf
        ]);

        $message = 'Berhasil proses prescreening E-Form';
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
    }

    /**
     * Hit service
     *
     * @return \Illuminate\Http\Response
     */
    public function getService( $endpoint, $requestData, $couple = false, $base = array(), $defaultValue )
    {
        $return = RestwsHc::setBody( [
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

        $pefindo = $this->getPefindo( $eform, 'search', false, null );
        $pefindoCouple = array();

        try {
            if ( $personal['status_id'] == 2 ) {
                $pefindoCouple = $this->getPefindo( $eform, 'search', true );

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
     * Hit pefindo service
     *
     * @return \Illuminate\Http\Response
     */
    public function getPefindo( $eform, $position = 'search', $couple = false, $pefindoId = null )
    {
        $customer = $eform->customer;
        $sendNik = ($couple ? $customer->personal['couple_nik'] : $eform->nik);
        $reason = 'Prescreening oleh ' . $eform->ao_name . '-' . $eform->ao_name;

        if ( $position == 'search' ) {
            $sendName = ($couple ? $customer->personal['couple_name'] : $customer->personal['name']);
            $sendBirthDate = ($couple ? $customer->personal['couple_birth_date'] : $customer->personal['birth_date']);

            if (ENV('APP_ENV') == 'local') {
                $getPefindo = [
                    "code" => "200"
                    , "descriptions" => "Success"
                    , "contents" => [
                        [
                            "Address" => "KAMPUNG GUNUNG KATUN, WAY KAMBAS"
                            , "DateOfBirth" => "1975-05-30"
                            , "FullName" => "RADEN FITRA"
                            , "KTP" => "1808043005750001"
                            , "PefindoId" => 2152216
                        ]
                        , [
                            "Address" => "Jl Tumenggung Suryo No. 18 Malang"
                            , "DateOfBirth" => "1975-05-30"
                            , "FullName" => "Ahmad Fitra"
                            , "KTP" => "9987613005750014"
                            , "PefindoId" => 2152216
                        ]
                    ]
                ];

            } else {
                $getPefindo = Asmx::setEndpoint( 'SmartSearchIndividual' )
                    ->setBody([
                        'Request' => json_encode( array(
                            'nomer_id_pefindo' => $sendNik
                            , 'nama_pefindo' => $sendName
                            , 'tanggal_lahir_pefindo' => $sendBirthDate
                            , 'alasan_pefindo' => $reason
                        ) )
                    ])
                    ->post( 'form_params' );
            }

            return ( $getPefindo["code"] == "200" ) ? $getPefindo["contents"] : null;

        } else {
            $endpoint = ( $position == 'data' ) ? 'PefindoReportData' : 'GetPdfReport';
            $return = ( $position == 'data' ) ? 0 : 'PDF kosong';

            if ( $pefindoId ) {
                if (ENV('APP_ENV') == 'local') {
                    $getPefindo = [
                        "code" => "200"
                        , "descriptions" => "Success"
                        , "contents" => [
                            "cip" => [
                                "recordlist" => [0]
                            ]
                        ]
                    ];

                } else {
                    $getPefindo = Asmx::setEndpoint( $endpoint )
                        ->setBody([
                            'Request' => json_encode( array(
                                'id_pefindo' => $pefindoId //2152216
                                , 'tipesubject_pefindo' => 'individual'
                                , 'alasan_pefindo' => $reason
                                , 'nomer_id_pefindo' => $sendNik
                            ) )
                        ])
                        ->post( 'form_params' );

                }

                if ( $getPefindo["code"] == "200" ) {
                    if ( $position == 'data' ) {
                        if ( isset( $getPefindo['contents']['cip'] ) ) {
                            if ( isset( $getPefindo['contents']['cip']['recordlist'] ) ) {
                                if ( isset( $getPefindo['contents']['cip']['recordlist'][0] ) ) {
                                    return $getPefindo['contents']['cip']['recordlist'][0];
                                }
                            }
                        }
                    } else {
                        if ( !empty($getPefindo["contents"]) ) {
                            $filename = ($couple ? 'pefindo-couple.pdf' : 'pefindo-individual.pdf');
                            $basePath = public_path( 'uploads/' . $eform->nik );
                            $publicPath = $basePath . '/pefindo.zip';

                            if (ENV('APP_ENV') == 'local') {
                                try {
                                    copy(
                                        $basePath . '/../blank.pdf'
                                        , $basePath . '/' . $filename
                                    );

                                    return $filename;

                                } catch (Exception $e) {
                                    return "Gagal generate PDF";

                                }

                            } else {
                                try {
                                    file_put_contents(
                                        $publicPath
                                        , base64_decode($getPefindo["contents"])
                                    );

                                    $zip = Zip::open( $publicPath )
                                        ->extract(
                                            $basePath
                                        );
                                    File::delete( $publicPath );

                                    copy(
                                        $basePath . '/report.pdf'
                                        , $basePath . '/' . $filename
                                    );

                                    File::delete( $basePath . '/report.pdf' );

                                    return $filename;

                                } catch (Exception $e) {
                                    return "Gagal generate PDF";

                                }
                            }
                        }
                    }
                }
            }

            return $return;
        }
    }

    /**
     * Change pefindo score to color
     *
     * @return \Illuminate\Http\Response
     */
    public function getColorPefindo( $score, $couple = false, $prevData, $index, $risk )
    {
        $return = array(
            'color' => 'Kuning'
            , 'position' => 2
            , 'key' => $couple ? 'couple' : 'individual'
            , 'index' => $index
            , 'risk' => $risk
            , 'score' => $score
        );
        if ( $score >= 250 && $score <= 529 ) {
            $return['color'] = 'Merah';
            $return['position'] = 1;

        } elseif ( $score >= 677 && $score <= 900 ) {
            $return['color'] = 'Hijau';
            $return['position'] = 3;

        }

        if ( $couple ) {
            if ( $prevData['position'] < $return['position'] ) {
                $return = $prevData;
            }
        }

        return $return;
    }

    /**
     * Change SICD collectible to color
     *
     * @return \Illuminate\Http\Response
     */
    public function getColorSicd( $collect )
    {
        if ( $collect == 1 || $collect == '-' || $collect == null || $collect == '' ) {
            return 'Hijau';

        } elseif ( $collect == 2 ) {
            return 'Kuning';

        }

        return 'Merah';
    }

    /**
     * Get prescreening final result
     *
     * @return \Illuminate\Http\Response
     */
    public function getResult( $dhnC, $sicdC, $pefindoC )
    {
        $calculate = array($pefindoC, $dhnC, $sicdC);

        if ( in_array('Merah', $calculate) ) {
            return 3;

        } else if ( in_array('Kuning', $calculate) ) {
            return 2;

        }
        return 1;
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
                $html .= asset('uploads/'.$eform->nik.'/'.$value) . ',';
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
            )
        ], 200 );
    }
}
