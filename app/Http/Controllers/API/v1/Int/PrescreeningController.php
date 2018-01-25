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
     * @param  string $type
     * @param  integer $eform_id
     * @return \Illuminate\Http\Response
     */
    public function show( Request $request )
    {
        $data = EForm::findOrFail($request->eform);
        $personal = $data->customer->personal;

        $dhn = json_decode((string) $data->dhn_detail);
        if ( !isset($dhn->responseData) ) {
            $dhn = json_decode((string) '[{"kategori":null,"keterangan":"","warna":"Hijau","result":""}]');
        } else {
            $dhn = array($dhn->responseData[ intval($data->selected_dhn) ]);
        }

        $sicd = json_decode((string) $data->sicd_detail);
        if ( !isset($sicd->responseData) ) {
            $sicd = json_decode((string) '[{"status":null,"acctno":null,"cbal":null,"bikole":null,"result":null,"cif":null,"nama_debitur":null,"tgl_lahir":null,"alamat":null,"no_identitas":null}]');
        } else {
            $sicd = array($sicd->responseData[ intval($data->selected_sicd) ]);
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
                , 'dhn' => $dhn
                , 'sicd' => $sicd
            ]
        ], 200 );
    }

    public function store( Request $request )
    {
        $eform = EForm::findOrFail( $request->input('eform') );

        $this->dependencies( 'sicd', $eform );
        $this->dependencies( 'dhn', $eform );
        // $this->pefindo( $eform );

        return response()->success( [
            'message' => 'Data Screening e-form',
            'contents' => $eform
        ], 200 );
    }

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
            foreach ($pefindo['risk'] as $value) {
                $risk[] = $value['description'];
            }

            $risk = implode(', ', $risk);
            $selected_pefindo = json_encode( array($pefindo['key'] => $pefindo['index']) );

        } else {
            $risk = $eform->ket_risk;
            $pdf = $eform->uploadscore;
            $selected_pefindo = 0;

            $score = $eform->pefindo_score;
            $pefindoC = 'Kuning';
            if ( $score >= 250 && $score <= 573 ) {
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

        return response()->success( [
            'message' => 'Berhasil proses prescreening E-Form',
            'contents' => $eform
        ], 200 );
    }

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

        $defaultValue = ( $type == "sicd" ? ['bikole' => '-'] : ['warna' => 'Hijau'] );

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

    public function getPefindo( $eform, $position = 'search', $couple = false, $pefindoId = null )
    {
        $customer = $eform->customer;
        $sendNik = ($couple ? $customer->personal['couple_nik'] : $eform->nik);
        $reason = 'Prescreening oleh ' . $eform->ao_name . '-' . $eform->ao_name;

        if ( $position == 'search' ) {
            $sendName = ($couple ? $customer->personal['couple_name'] : $customer->personal['name']);
            $sendBirthDate = ($couple ? $customer->personal['couple_birth_date'] : $customer->personal['birth_date']);

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

            return ( $getPefindo["code"] == "200" ) ? $getPefindo["contents"] : null;

        } else {
            $endpoint = ( $position == 'data' ) ? 'PefindoReportData' : 'GetPdfReport';
            $return = ( $position == 'data' ) ? 0 : 'PDF kosong';

            if ( $pefindoId ) {
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
                            try {
                                $filename = ($couple ? 'pefindo-couple.pdf' : 'pefindo-individual.pdf');
                                $basePath = public_path( 'uploads/' . $eform->nik );
                                $publicPath = $basePath . '/pefindo.zip';

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

            return $return;
        }
    }

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
        if ( $score >= 250 && $score <= 573 ) {
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

    public function getColorSicd( $collect )
    {
        if ( $collect == 1 || $collect == '-' || $collect == null || $collect == '' ) {
            return 'Hijau';

        } elseif ( $collect == 2 ) {
            return 'Kuning';

        }

        return 'Merah';
    }

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
}
