<?php

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Storage;
use App\Models\UserNotification;
use App\Models\EForm;
use App\Models\ActionDate;
use App\Notifications\ApproveEFormCustomer;
use App\Notifications\ApproveEFormInternal;
use App\Notifications\RejectEFormInternal;
use App\Notifications\ApproveEFormCLAS;
use App\Notifications\ApproveEFormCLASCustomer;
use App\Notifications\RejectEFormCustomer;
use App\Notifications\RejectEFormCLAS;
use App\Notifications\RejectEFormCLASCustomer;
use App\Notifications\VerificationApproveFormNasabah;
use App\Notifications\VerificationRejectFormNasabah;
use App\Notifications\CreateScheduleNotification;
use App\Notifications\UpdateScheduleNotification;
use App\Notifications\LKNEFormCustomer;
use App\Notifications\LKNEFormRecontest;
use App\Notifications\VerificationDataNasabah;
use App\Notifications\RecontestEFormNotification;
use App\Notifications\PengajuanKprNotification;
use App\Notifications\PencairanInternal;
use App\Notifications\PencairanNasabah;
use App\Notifications\ScorePefindoPreScreening;
use App\Models\UserServices;
use App\Models\User;
use App\Models\Appointment;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\Topics;
use LaravelFCM\Facades\FCM;
use App\Events\EForm\Approved;

if (! function_exists('get_pefindo_service')) {
    /**
     * Hit pefindo service
     *
     * @return \Illuminate\Http\Response
     */
    function get_pefindo_service( $eform, $position = 'search', $couple = false, $pefindoId = null )
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
                            , "PefindoId" => 2152217
                        ]
                    ]
                ];

            } else {
                $getPefindo = \Asmx::setEndpoint( 'SmartSearchIndividual' )
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
            $return = ( $position == 'data' ) ? 0 : null;

            if ( $pefindoId ) {
                if (ENV('APP_ENV') == 'local') {
                    $getPefindo = [
                        "code" => "200"
                        , "descriptions" => "Success"
                        , "contents" => [
                            "cip" => [
                                "recordlist" => [
                                    [
                                        "date" => "2017-12-07"
                                        , "grade" => 11
                                        , "probabilityofdefault" => 14.73
                                        , "reasonslist" => [
                                            [
                                                "code" => "DIS1"
                                                , "description" => "Subject disputes the data"
                                            ]
                                            , [
                                                "code" => "NQS1"
                                                , "description" => "5 or more subscribers have recently requested reports on subject"
                                            ]
                                        ]
                                        , "score" => 606.0
                                        , "trend" => 3
                                    ]
                                ]
                            ]
                        ]
                    ];

                } else {
                    $getPefindo = \Asmx::setEndpoint( $endpoint )
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
                                        public_path('blank.pdf')
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

                                    $zip = \Zip::open( $publicPath )
                                        ->extract(
                                            $basePath
                                        );
                                    \File::delete( $publicPath );

                                    copy(
                                        $basePath . '/report.pdf'
                                        , $basePath . '/' . $filename
                                    );

                                    \File::delete( $basePath . '/report.pdf' );

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
}

if (! function_exists('get_pefindo_color')) {
    /**
     * Change pefindo score to color
     *
     * @return \Illuminate\Http\Response
     */
    function get_pefindo_color( $score, $couple = false, $prevData, $index, $risk, $selected = false )
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

        } elseif ( ( $score >= 677 && $score <= 900 ) || $score == 999 ) {
            $return['color'] = 'Hijau';
            $return['position'] = 3;

        }

        if ( $selected ) {
            return  $return;
        }

        if ( $couple ) {
            if ( $prevData['position'] < $return['position'] ) {
                $return = $prevData;
            }
        }

        return $return;
    }
}

if (! function_exists('break_pefindo')) {
    /**
     * Break pefindo data for insert to eform table
     *
     * @return \Illuminate\Http\Response
     */
    function break_pefindo( $eform, $request )
    {
        $pefindoDetail = json_decode($eform['pefindo_detail']);
        $pefindoScoreDetail = [];
        $countIndividu = count($pefindoDetail->individual);
        $countCouple = count($pefindoDetail->couple);
     if($countIndividu == 0 && $countCouple > 0 ){
        \Log::info("===MASUK KONDISI 0 - 1 ===");
        if ( isset($request['select_couple_pefindo']) ) {
            $couple = $pefindoDetail->couple[ $request['select_couple_pefindo'] ];
            $dataCouple = get_pefindo_service( $eform, 'data', true, $couple->PefindoId );
            $pdf = get_pefindo_service( $eform, 'pdf', true, $couple->PefindoId );
            $pefindo = get_pefindo_color( $dataCouple['score'], true, array(), $request['select_couple_pefindo'], $dataCouple['reasonslist'] );
            $lastDataCouple = $pefindo;
            $pefindoScoreDetail['couple'] = [
                (String) $request['select_couple_pefindo'] => [
                    'score' => ($lastDataCouple['score']) ? $lastDataCouple['score'] : 0
                    , 'color' => ($lastDataCouple['color']) ? $lastDataCouple['color'] : 0
                ]
            ];
        }

        if ( isset($request['select_individual_pefindo']) ) {
            $individu = $pefindoDetail->individual[ $request['select_individual_pefindo'] ];
            $dataIndividu = get_pefindo_service( $eform, 'data', false, $individu->PefindoId );
            $pdf .= ',' . get_pefindo_service( $eform, 'pdf', false, $individu->PefindoId );
            $pefindo = get_pefindo_color( $dataIndividu['score'], false, $pefindo, $request['select_individual_pefindo'], $dataIndividu['reasonslist'] );
            $lastDataIndividu = get_pefindo_color( $dataIndividu['score'], false, $pefindo, $request['select_individual_pefindo'], $dataIndividu['reasonslist'], true );;
            $pefindoScoreDetail['individual'] = [
                (String) $request['select_individual_pefindo'] => [
                    'score' => ($lastDataIndividu['score']) ? $lastDataIndividu['score'] : 0
                    , 'color' => ($lastDataIndividu['color']) ? $lastDataIndividu['color'] : 0
                ]
            ];
        }

     }else{

        if ( isset($request['select_individual_pefindo']) ) {
            $individu = $pefindoDetail->individual[ $request['select_individual_pefindo'] ];
            $dataIndividu = get_pefindo_service( $eform, 'data', false, $individu->PefindoId );
            $pdf = get_pefindo_service( $eform, 'pdf', false, $individu->PefindoId );
            $pefindo = get_pefindo_color( $dataIndividu['score'], false, array(), $request['select_individual_pefindo'], $dataIndividu['reasonslist'] );
            $lastDataIndividu = $pefindo;
            $pefindoScoreDetail['individual'] = [
                (String) $request['select_individual_pefindo'] => [
                    'score' => ($lastDataIndividu['score']) ? $lastDataIndividu['score'] : 0
                    , 'color' => ($lastDataIndividu['color']) ? $lastDataIndividu['color'] : 0
                ]
            ];
        }

        if ( isset($request['select_couple_pefindo']) ) {
            $couple = $pefindoDetail->couple[ $request['select_couple_pefindo'] ];
            $dataCouple = get_pefindo_service( $eform, 'data', true, $couple->PefindoId );
            $pdf .= ',' . get_pefindo_service( $eform, 'pdf', true, $couple->PefindoId );
            $pefindo = get_pefindo_color( $dataCouple['score'], true, $pefindo, $request['select_couple_pefindo'], $dataCouple['reasonslist'] );
            $lastDataCouple = get_pefindo_color( $dataCouple['score'], true, $pefindo, $request['select_couple_pefindo'], $dataCouple['reasonslist'], true );
            $pefindoScoreDetail['couple'] = [
                (String) $request['select_couple_pefindo'] => [
                    'score' => ($lastDataCouple['score']) ? $lastDataCouple['score'] : 0
                    , 'color' => ($lastDataCouple['color']) ? $lastDataCouple['color'] : 0
                ]
            ];
        }
    }

        $risk = array();
        if ( isset( $pefindo['risk'] ) ) {
            foreach ($pefindo['risk'] as $value) {
                $risk[] = $value['description'];
            }
        }

        $risk = implode(', ', $risk);
        $selected_pefindo = json_encode( array($pefindo['key'] => $pefindo['index']) );

        return [
            'risk' => $risk
            , 'pefindo' => $pefindo
            , 'selected_pefindo' => $selected_pefindo
            , 'pdf' => $pdf
            , 'pefindo_score_all' => $pefindoScoreDetail
        ];
    }
}

if (! function_exists('prescreening_result')) {
    /**
     * Get prescreening final result
     *
     * @return \Illuminate\Http\Response
     */
    function prescreening_result( $dhnC, $sicdC, $pefindoC )
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

if (! function_exists('sicd_color')) {
    /**
     * Change SICD collectible to color
     *
     * @return \Illuminate\Http\Response
     */
    function sicd_color( $collect )
    {
        if ( $collect == 1 || $collect == '-' || $collect == null || $collect == '' ) {
            return 'Hijau';

        } elseif ( $collect == 2 ) {
            return 'Kuning';

        }

        return 'Merah';
    }
}

if (! function_exists('generate_data_prescreening')) {
    /**
     * Generate eform prescreening data
     *
     * @return \Illuminate\Http\Response
     */
    function generate_data_prescreening( $eform, $request, $returnData )
    {
        $sicdDetail = json_decode($eform['sicd_detail']);
        $sicd = $sicdDetail->responseData[ $request['select_sicd'] ];

        $dhnDetail = json_decode($eform['dhn_detail']);
        $dhn = $dhnDetail->responseData[ $request['select_dhn'] ];

        return [
            'prescreening_status' => prescreening_result(
                $dhn->warna
                , sicd_color( $sicd->bikole )
                , $returnData[ 'pefindo' ][ 'color' ]
            )
            , 'pefindo_score' => $returnData[ 'pefindo' ][ 'score' ]
            , 'selected_pefindo' => $returnData[ 'selected_pefindo' ]
            , 'ket_risk' => $returnData[ 'risk' ]
            , 'uploadscore' => $returnData[ 'pdf' ]
            , 'pefindo_score_all' => json_encode($returnData[ 'pefindo_score_all' ])
            , 'is_screening' => 1
        ];
    }
}

if (! function_exists('set_action_date')) {

    /**
     * Generate pdf file.
     *
     * @param  integer $id
     * @param  string $action
     *
     * @return array
     */
    function set_action_date($id, $action)
    {
        $eform = EForm::find( $id );
        $eform->detail_actions()->updateOrCreate(
            [
                'action' => $action
                , 'execute_at' => date('Y-m-d H:i:s')
            ]
        );
    }
}

if (! function_exists('csv_to_array')) {

    /**
     * Convert csv file to array.
     *
     * @param  string $file path to file
     * @param  array $headers
     * @param  string $delimiter
     *
     * @return array
     */
	function csv_to_array($file = '', array $headers, $delimiter = ',')
	{
		if(!file_exists($file) || !is_readable($file)) return FALSE;

		$data = [];
		if (($handle = fopen($file, 'r')) !== FALSE) {
			while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
				$data[] = array_combine($headers, $row);
			}
			fclose($handle);
		}

		return $data;
	}
}

if (! function_exists('name_separator')) {

    /**
     * Return an array of first name and last name from given full name.
     *
     * @param  string  $fullname
     * @return array
     */
    function name_separator($fullname)
    {
        $fullname = explode(' ', $fullname);

        return [$fullname[0], implode(' ', array_except($fullname, 0))];
    }
}

if (! function_exists('generate_paths')) {

    /**
     * Listen for generate path of photos and save to storage.
     *
     * @param  array    $photos
     * @param  string   $driver
     * @return array
     */
    function generate_paths($photos, $driver = 'uploads', $folder = '')
    {
        $paths = [];
        foreach ($photos as $key => $photo) {
            if ( is_file($photo) ) $paths[$key]['path'] = $photo->store($folder, $driver);
        }
        return $paths;
    }
}

if (! function_exists('removed_photos')) {

    /**
     * Logic for deleted photos
     *
     * @param  mixed    $model
     * @return void
     */
    function removed_photos($model, $driver = 'uploads')
    {
        if (request()->has('removed_photos') && $model->photos) {

            /**
             * Filtering object if exists with request removed_photos
             */
            $photos = $model->photos->filter(function ($value, $key) {
                return in_array($key, request('removed_photos'));
            });

            foreach ($photos as $photo) {
                Storage::disk($driver)->delete($photo->path);
                $photo->delete();
            }
        }
    }
}

if (! function_exists('saving_photos')) {

    /**
     * Logic for saving photos
     *
     * @param  mixed    $model
     * @return void
     */
    function saving_photos($model, $driver = 'uploads')
    {
        /**
         * This logic for remove image for property type
         */
        removed_photos($model, $driver);

        /**
         * Call function generate_paths on helpers file
         * request photos is array type, properties is a driver for saving to storage, last variable is folder
         */
        if (request()->hasFile('photos')) {
            $paths = generate_paths(request('photos'), $driver, $model->id);
            $model->photos()->createMany($paths);
        }
    }
}

if (! function_exists('curl_post')) {

    function curl_post($url, array $post = NULL, array $options = array())
    {
        $defaults = [
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_POSTFIELDS => http_build_query($post)
        ];

        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));

        if( ! $result = curl_exec($ch)) {
            trigger_error(curl_error($ch));
        }

        curl_close($ch);
        return $result;
    }
}

if (! function_exists('user_info')) {
    /**
     * Get logged user info.
     *
     * @param  string $column
     * @return mixed
     */
    function user_info($column = null)
    {
        if ($user = Sentinel::check()) {
            if (is_null($column)) {
                return $user;
            }

            if ('full_name' == $column) {
                return user_info('first_name').' '.user_info('last_name');
            }

            if ('role' == $column) {
                return user_info()->roles[0];
            }

            return $user->{$column};
        }

        return null;
    }
}

if (! function_exists('generate_pdf')) {

    /**
     * Generate pdf file.
     *
     * @param  string $folder
     * @param  string $filename
     * @param  string $html
     *
     * @return array
     */
    function generate_pdf($folder, $filename, $html)
    {
        // return generate_pdf("uploads/327702020394", "myfile.pdf", '<h1>init data</h1>');
        try {
            $path = public_path().'/'.$folder;
            File::makeDirectory($path, $mode = 0777, true, true);

            PDF::loadHTML($html)
                ->setPaper('a4', 'portrait')
                ->setWarnings(false)
                ->save(public_path($folder.'/'.$filename));

        } catch (Exception $e) {
            \Log::info("=============================exception-==========================");
            \Log::info($e);
            return $e;

        }

        return $filename;
    }
}

if (! function_exists('checkRolesInternal')) {

    /**
     * Generate pdf file.
     *
     * @param  string $folder
     * @param  string $filename
     * @param  string $html
     *
     * @return array
     */
    function checkRolesInternal($branch_id)
    {
        if( in_array( intval($branch_id), [ 37, 38, 39, 41, 42, 43 ] ) ) {
            $ArrRole = ['role' =>'ao','branch_id' => $branch_id ];
        } else if( in_array( intval($branch_id), [ 21, 49, 50, 51 ] ) ) {
            $ArrRole = ['role' =>'mp','branch_id' => $branch_id ];
        } else if( in_array( intval($branch_id), [ 5, 11, 12, 14, 19 ] ) ) {
            $ArrRole = ['role' =>'pinca','branch_id' => $branch_id ];
        } else if( in_array( intval($branch_id), [ 59 ] ) ) {
            $ArrRole = ['role' =>'prescreening','branch_id' => $branch_id ];
            if( in_array( strtolower($data[ 'posisi' ]), [ 'collateral appraisal', 'collateral manager' ] ) ){
                $role = str_replace(' ', '-', strtolower($data[ 'posisi' ]));
            }
        } else if( in_array( intval($branch_id), [26] ) ) {
            $ArrRole = ['role' =>'staff','branch_id' => $branch_id ];
        } else if( in_array( intval($branch_id), [18] ) ) {
            $ArrRole = ['role' =>'collateral','branch_id' => $branch_id ];
        } else {
            $ArrRole = ['role' =>'staff','branch_id' => $branch_id ];
        }

        return $ArrRole;
    }
}

if (! function_exists('get_religion')) {

    /**
     * Convert csv file to array.
     *
     * @param  string $file path to file
     * @param  array $headers
     * @param  string $delimiter
     *
     * @return array
     */
    function get_religion($key)
    {
        $data = array(
            "BUD" => "BUDHA"
            , "HIN" => "HINDU"
            , "ISL" => "ISLAM"
            , "KRI" => "KRISTEN"
            , "ZZZ" => "LAINNYA"
        );

        if ( $key != 'all' ) {
            return isset($data[$key]) ? $data[$key] : '-';
        }

        return $data;
    }
}

if (! function_exists('get_title')) {

    /**
     * Convert csv file to array.
     *
     * @param  string $file path to file
     * @param  array $headers
     * @param  string $delimiter
     *
     * @return array
     */
    function get_title($key)
    {
        $data = array(
            "S1" => "Sarjana"
            , "S2" => "Master"
            , "S3" => "Doktor"
            // , "SE" => "Sekolah"
            , "SD" => "SD"
            , "SM" => "SMP"
            , "SU" => "SMU/SMK"
            , "ZZ" => "Diploma"
        );

        if ( $key != 'all' ) {
            return isset($data[$key]) ? $data[$key] : '-';
        }

        return $data;
    }
}

if (! function_exists('get_employment')) {

    /**
     * Convert csv file to array.
     *
     * @param  string $file path to file
     * @param  array $headers
     * @param  string $delimiter
     *
     * @return array
     */
    function get_employment($key)
    {
        $data = array(
            "1" => "Pegawai Tetap"
            , "2" => "Kontrak"
            , "3" => "Honorer"
            , "4" => "Lainnya"
        );

        if ( $key != 'all' ) {
            return isset($data[$key]) ? $data[$key] : '-';
        }

        return $data;
    }
}

if (! function_exists('notificationIsRead')) {

    /**
     * Convert csv file to array.
     *
     * @param  string $file path to file
     * @param  array $headers
     * @param  string $delimiter
     *
     * @return array
     */
    function notificationIsRead($slug, $typeModule)
    {
        $notificationIsRead =  UserNotification::where('slug', $slug)->where( 'type_module',$typeModule)
                                       ->whereNull('read_at')
                                       ->first();
        if($notificationIsRead){
            $notificationIsRead->markAsRead();
        }
    }
}

if (! function_exists('get_loan_history')) {

    /**
     * Convert csv file to array.
     *
     * @param  string $file path to file
     * @param  array $headers
     * @param  string $delimiter
     *
     * @return array
     */
    function get_loan_history($key)
    {
        $data = array(
            "1" => "Pernah menunggak"
            , "2" => "Debitur baru"
            , "3" => "Tidak ada tunggakan"
        );

        if ( $key != 'all' ) {
            return isset($data[$key]) ? $data[$key] : '-';
        }

        return $data;
    }
}

if (! function_exists('getTypeModule')) {

    /**
     * Convert csv file to array.
     *
     * @param  string $file path to file
     * @param  array $headers
     * @param  string $delimiter
     *
     * @return array
     */
    function getTypeModule($module)
    {

        switch ($module) {
            case 'App\Models\Appointment':
                $typeModule = 'schedule';
                break;
            case 'App\Models\EForm':
                $typeModule = 'eform';
                break;
            case 'App\Models\Collateral':
                $typeModule = 'collateral';
                break;
            case 'App\Models\Developer':
                $typeModule = 'developer';
                break;
            case 'App\Models\UserDeveloper':
                $typeModule = 'developer-sales';
                break;
            case 'App\Models\Property':
                $typeModule = 'property';
                break;
            case 'App\Models\Scoring':
                $typeModule = 'prescreening-scoring';
                break;
            default:
                $typeModule = 'Type undefined';
                break;
        }
        return $typeModule;
    }
}

if (! function_exists('autoApproveForVIP')) {

    /**
     * Auto approve helper for VIP.
     *
     * @param  int $eform_id
     *
     * @return array
     */
    function autoApproveForVIP( $request, $eform_id, $resend = false )
    {
        if ( !isset( $request['is_approved'] ) ) {
            $request['is_approved'] = true;
            $request['auto_approve'] = true;
        }

        $response = EForm::approve( $eform_id, (object) $request );

        if ( $response['status'] ) {
            $data = EForm::find( $eform_id );
            $typeModule = getTypeModule( EForm::class );

            $notificationIsRead = UserNotification::where( 'slug', $eform_id )
                ->where( 'type_module', $typeModule)
                ->whereNull( 'read_at' )
                ->first();

            if ( $notificationIsRead != NULL ) {
                $notificationIsRead->markAsRead();
            }

            $usersModel = User::Find( $data->user_id );
            event( new Approved( $data ) );

            // Call the helper of push notification function
            pushNotification(
                array(
                    'data' => $data
                    , 'user' => $usersModel
                )
                , 'approveEForm'
            );

            $detail = EForm::with( 'visit_report.mutation.bankstatement' )->find( $eform_id );

            generate_pdf('uploads/'. $detail->nik, 'lkn.pdf', view('pdf.approval', compact('detail')));

            if ( $resend ) {
                set_action_date($eform->id, 'eform-vip-resend');

            } else {
                set_action_date($detail->id, 'eform-approval');
                set_action_date($detail->id, 'eform-vip');

            }

            return 'E-Form VIP berhasil';
        }

        return isset($response['message']) ? $response['message'] . " dan Kirim Ulang VIP" : 'E-Form VIP gagal';
    }
}

if (! function_exists('getMessage')) {
    function getMessage($type, $credentials = null)
    {
        switch ($type) {
            case 'eform_create':
                $message = [
                    'title'=> 'EForm Notification',
                    'body' => 'Pengajuan KPR Baru',
                ];
                break;
            case 'eform_approve':
                $message = [
                    'title'=> 'EForm Notification',
                    'body' => 'Pengajuan anda telah di Setujui',
                ];
                break;
            case 'eform_approve_clas':
                $message = [
                    'title'=> 'EForm Notification',
                    'body' => 'Pengajuan : '.$credentials->ref_number.' telah di Setujui CLS.',
                ];
                break;
            case 'eform_approve_ao':
                $message = [
                    'title'=> 'EForm Notification',
                    'body' => 'Pengajuan : '.$credentials->ref_number.' telah di Setujui.',
                ];
                break;
            case 'eform_reject':
                $message = [
                    'title'=> 'EForm Notification',
                    'body' => 'Pengajuan anda telah di Tolak',
                ];
                break;
            case 'eform_reject_clas':
                $message = [
                    'title'=> 'EForm Notification',
                    'body' => 'Pengajuan : '.$credentials->ref_number.' telah di Tolak CLS.',
                ];
                break;
            case 'eform_reject_ao':
                $message = [
                    'title'=> 'EForm Notification',
                    'body' => 'Pengajuan : '.$credentials->ref_number.' telah di Tolak.',
                ];
                break;
            case 'eform_lkn_recontest':
                $message = [
                    'title'=> 'EForm Notification',
                    'body' => 'Data LKN Recontest berhasil dikirim',
                ];
                break;
            case 'eform_lkn':
                $message = [
                    'title'=> 'EForm Notification',
                    'body' => 'Data LKN berhasil dikirim',
                ];
                break;
            case 'eform_prescreening':
                $message = [
                    'title'=> 'EForm Notification',
                    'body' => 'Hasil Prescreening : '.$credentials->ref_number,
                ];
                break;
            case 'eform_pencairan_customer':
                $message = [
                    'title'=> 'EForm Notification',
                    'body' => 'Pengajuan anda telah dicairkan.',
                ];
                break;
            case 'eform_pencairan':
                $message = [
                    'title'=> 'EForm Notification',
                    'body' => 'Pengajuan : '.$credentials->ref_number.' telah dicairkan.',
                ];
                break;
            case 'eform_disposition':
                $message = [
                    'title'=> 'EForm Notification',
                    'body' => 'Disposisi Pengajuan',
                ];
                break;
            case 'eform_recontest':
                $message = [
                    'title'=> 'EForm Notification',
                    'body' => 'Pengajuan : '.$credentials->ref_number.' telah di Rekontest',
                ];
                break;
            case 'schedule_create':
                $message = [
                    'title'=> 'Schedule Notification',
                    'body' => 'Anda memiliki jadwal baru',
                ];
                break;
            case 'schedule_update':
                $message = [
                    'title'=> 'Schedule Notification',
                    'body' => 'Jadwal anda telah di update, Silahkan cek jadwal anda',
                ];
                break;
            case 'collateral_penilaian':
                $message = [
                    'title'=> 'Collateral Notification',
                    'body' => 'Form Penilaian Agunan',
                ];
                break;
            case 'collateral_approve':
                $message = [
                    'title'=> 'Collateral Notification',
                    'body' => 'Approval Collateral',
                ];
                break;
            case 'collateral_ots':
                $message = [
                    'title'=> 'Collateral Notification',
                    'body' => 'OTS Collateral',
                ];
                break;
            case 'collateral_reject':
                $message = [
                    'title'=> 'Collateral Notification',
                    'body' => 'Reject Collateral',
                ];
                break;
            case 'collateral_reject_penilaian':
                $message = [
                    'title'=> 'Collateral Notification',
                    'body' => 'Penilaian Agunan Ditolak',
                ];
                break;
            case 'collateral_disposition':
                $message = [
                    'title'=> 'Collateral Notification',
                    'body' => 'Penugasan Staff Collateral',
                ];
                break;
            case 'collateral_checklist':
                $message = [
                    'title'=> 'Collateral Notification',
                    'body' => 'Collateral Checklist',
                ];
                break;
            case 'collateral_property':
                $message = [
                    'title'=> 'Collateral Notification',
                    'body' => 'Collateral Property Baru',
                ];
                break;
            case 'verify':
                $message = [
                    'title'=> 'Verify Notification',
                    'body' => 'Silahkan Verifikasi Data Anda',
                ];
                break;
            default:
                $message = [
                    'title'=> "undefined",
                    'body' => 'Type undefined',
                ];
                break;
        }

        return $message;
    }
}

if (! function_exists('pushNotificationCRM')) {
  function pushNotificationCRM($data, $type)
  {
    if(env('PUSH_NOTIFICATION', false)){
        $type($data);
    }
  }

  function marketingNote( $sendData ) {
    $notificationBuilder = new PayloadNotificationBuilder( 'New Marketing Note' );
    $notificationBuilder->setBody( '' )//message descriptiom
                        ->setSound('default');

    $dataBuilder = new PayloadDataBuilder();
    $dataBuilder->addData([
      'slug' => $sendData['marketing_id'],
      'type' => 'marketing_note'
    ]);

    $notification = $notificationBuilder->build();
    $data = $dataBuilder->build();

    $topic = new Topics();
    $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))
          ->andTopic( 'CRM_'.$sendData['pn']);

    $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
    $topicResponse->isSuccess();
    $topicResponse->shouldRetry();
    $topicResponse->error();

  }

  function newMarketing( $sendData ) {
    $notificationBuilder = new PayloadNotificationBuilder( 'New Marketing');
    $notificationBuilder->setBody('')// message descriptions
                        ->setSound('default');

    $dataBuilder = new PayloadDataBuilder();
    $dataBuilder->addData([
      'slug' => $sendData['branch'],
      'type' => 'new_marketing'
    ]);

    $notification = $notificationBuilder->build();
    $data = $dataBuilder->build();

    $topic = new Topics();
    $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))
          ->andTopic('CRM_'.$sendData['branch']);

    $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
    $topicResponse->isSuccess();
    $topicResponse->shouldRetry();
    $topicResponse->error();
  }
}

if (! function_exists('pushNotification')) {

    /**
     * Convert csv file to array.
     *
     * @param  string $file path to file
     * @param  array $headers
     * @param  string $delimiter
     *
     * @return array
     */
    function pushNotification($credentials, $type)
    {
        if(env('PUSH_NOTIFICATION', false)){
            if($type == 'disposition'){
                disposition($credentials);
            }else if($type == 'createEForm'){
                createEForm($credentials);
            }else if($type == 'approveEForm'){
                approveEForm($credentials);
            }else if($type == 'rejectEForm'){
                rejectEForm($credentials);
            }else if($type == 'lknEForm'){
                lknEForm($credentials);
            }else if($type == 'createSchedule'){
                // createSchedule($credentials);
            }else if($type == 'updateSchedule'){
                updateSchedule($credentials);
            }else if($type == 'verifyCustomer'){
                verifyCustomer($credentials);
            }else if($type == 'recontestEForm'){
                recontestEForm($credentials);
            }else if($type == 'prescreening'){
                prescreeningEForm($credentials);
            }else if($type == 'pencairanEForm'){
                pencairanEForm($credentials);
            }else if ($type =='general'){
                collateralNotification($credentials);
            }
        }
    }

    function pencairanEForm($credentials){
        pencairanEFormToCustomer($credentials);
        pencairanEFormToAO($credentials);
        pencairanEFormToPinca($credentials);
    }


    function pencairanEFormToCustomer($credentials)
    {
        $data      = $credentials;
        $userId    = $data['data']->user_id;
        $userModel = $data['user'];

        $message   = getMessage("eform_pencairan_customer", $credentials['data']);
        $userNotif = new UserNotification;
        $userModel->notify(new PencairanNasabah($data['data']));

        $notificationBuilder = new PayloadNotificationBuilder($message['title']);
        $notificationBuilder->setBody($message['body'])
                            ->setSound('default');

        // Get data from notifications table
        $notificationData = $userNotif->where('slug', $data['data']->id)
                                        ->where('type_module', 'eform')
                                        ->orderBy('created_at', 'desc')->first();

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'   => $notificationData['id'],
            'slug' => $data['data']->id,
            'type' => 'tracking',
        ]);

        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();
        $topic        = new Topics();

        // $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('user_'.$userId);
        $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('user_'.$userId);

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function pencairanEFormToAO($credentials)
    {
        $data      = $credentials;
        $userId    = $data['data']->user_id;
        $userModel = $data['user'];
        $message   = getMessage("eform_pencairan", $credentials['data']);

        $userNotif = new UserNotification;
        $userModel->notify(new PencairanInternal($data['data']));

        $notificationBuilder = new PayloadNotificationBuilder($message['title']);
        $notificationBuilder->setBody($message['body'])
                            ->setSound('default');

        // Get data from notifications table
        $notificationData = $userNotif->where('slug', $data['data']->id)
                                        ->where('type_module', 'eform')
                                        ->orderBy('created_at', 'desc')->first();

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'   => $notificationData['id'],
            'slug' => $data['data']->id,
            'type' => 'tracking',
        ]);

        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();
        $topic        = new Topics();

        // $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('user_'.$userId);
        $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('branch_'.$credentials['data']->branch_id)->andTopic('ao_'.$credentials['data']->ao_id);

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function pencairanEFormToPinca($credentials)
    {
        $data      = $credentials;
        $userId    = $data['data']->user_id;
        $userModel = $data['user'];
        $userNotif = new UserNotification;

        $message   = getMessage("eform_pencairan", $credentials['data']);
        $notificationBuilder = new PayloadNotificationBuilder($message['title']);
        $notificationBuilder->setBody($message['body'])
                            ->setSound('default');

        // Get data from notifications table
        $notificationData = $userNotif->where('slug', $data['data']->id)
                                        ->where('type_module', 'eform')
                                        ->orderBy('created_at', 'desc')->first();

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'   => $notificationData['id'],
            'slug' => $data['data']->id,
            'type' => 'tracking',
        ]);

        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();
        $topic        = new Topics();

        // $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('user_'.$userId);
        $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('branch_'.$credentials['data']->branch_id)->andTopic('pinca');

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function prescreeningEForm($credentials)
    {
        $data      = $credentials;
        $userId    = $data['data']->user_id;
        $userModel = $data['user'];

        $message = getMessage("eform_prescreening", $credentials['data']);

        $userNotif = new UserNotification;
        $userModel->notify(new ScorePefindoPreScreening($data['data']));

        $notificationBuilder = new PayloadNotificationBuilder($message['title']);
        $notificationBuilder->setBody($message['body'])
                            ->setSound('default');

        // Get data from notifications table
        $notificationData = $userNotif->where('slug', $data['data']->id)
                                        ->where('type_module', 'eform')
                                        ->orderBy('created_at', 'desc')->first();

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'   => $notificationData['id'],
            'slug' => $data['data']->id,
            'type' => 'eform',
        ]);

        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();
        $topic        = new Topics();

        $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('branch_'.$credentials['data']->branch_id)->andTopic('ao_'.$credentials['data']->ao_id);

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function verifyCustomer($credentials){
        $dataUser  = $credentials['data'];
        $message   = getMessage("verify");

        $userModel = User::FindOrFail($dataUser['user_id']);
        $userModel->notify(new VerificationDataNasabah($dataUser));

        $notificationBuilder = new PayloadNotificationBuilder($message['title']);
        $notificationBuilder->setBody($message['body'])
                            ->setSound('default');

        $notificationData = UserNotification::where('slug', $dataUser->id)
                                        ->where('type_module', 'eform')
                                        ->orderBy('created_at', 'desc')->first();
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'       => $notificationData['id'],
            'slug'     => $dataUser->ref_number,
            'type'     => 'profile',
        ]);
        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();

        $topic = new Topics();
        $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('user_'.$dataUser->user_id);

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function recontestEForm($credentials){
        $dataUser  = $credentials['data'];
        $userModel = $credentials['user'];
        $message   = getMessage("eform_recontest", $dataUser);

        $userModel->notify(new RecontestEFormNotification($dataUser));

        $notificationBuilder = new PayloadNotificationBuilder($message['title']);
        $notificationBuilder->setBody($message['body'])
                            ->setSound('default');

        $notificationData = UserNotification::where('slug', $dataUser->id)
                                        ->where('type_module', 'eform')
                                        ->orderBy('created_at', 'desc')->first();
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'       => $notificationData->id,
            'slug'     => $dataUser->ref_number,
            'type'     => 'eform',
        ]);
        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();

        $topic = new Topics();
        $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('branch_'.$dataUser->branch_id)->andTopic('pinca');
        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function updateSchedule($credentials){
        $dataUser = $credentials['data'];
        $role     = $credentials['role'];
        $message  = getMessage("schedule_update");

        $notificationBuilder = new PayloadNotificationBuilder($message['title']);
        $notificationBuilder->setBody($message['body'])
                            ->setSound('default');

        $notificationData = UserNotification::where('slug', $dataUser['eform_id'])
                                        ->where('type_module', 'eform')
                                        ->orderBy('created_at', 'desc')->first();
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'       => $notificationData['id'],
            'slug'     => $dataUser['id'],
            'type'     => 'schedule',
        ]);
        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();

        $topic = new Topics();

        if($role == 'customer'){
            $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('ao_'.$dataUser['ao_id']);
        }else{
            $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('user_'.$dataUser['user_id']);
        }

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function createSchedule($credentials){
        $dataUser      = $credentials['data'];
        $message       = getMessage("schedule_create");
        $appointment   = Appointment::where('eform_id', $dataUser['eform_id'])->first();
        $appointmentId = $appointment->id;

        $notificationBuilder = new PayloadNotificationBuilder($message['title']);
        $notificationBuilder->setBody($message['body'])
                            ->setSound('default');

        $notificationData = UserNotification::where('slug', $dataUser['eform_id'])
                                        ->where('type_module', 'eform')
                                        ->orderBy('created_at', 'desc')->first();
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'       => $notificationData['id'],
            'slug'     => $appointmentId,
            'type'     => 'schedule',
        ]);
        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();

        $topic = new Topics();
        $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('user_'.$dataUser['user_id']);

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function lknEForm($credentials){
        $data      = $credentials;
        $userLogin = $data['credentials'];
        $branch_id = substr('0'.$userLogin['branch_id'], -3);

        if (!empty($data['recontest'])) {
            $message   = getMessage("eform_lkn_recontest");
            $userModel = $data['user'];
            $userNotif = new UserNotification;

            $userModel->notify(new LKNEFormRecontest($data['data']));

            $notificationBuilder = new PayloadNotificationBuilder($message['title']);
            $notificationBuilder->setBody($message['body'])
                                ->setSound('default');

            // Get data from notifications table
            $notificationData = $userNotif->where('slug', $data['data']->id)
                                          ->where('type_module', 'eform')
                                          ->orderBy('created_at', 'desc')->first();
            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData([
                'id'       => $notificationData['id'],
                'slug'     => $data['data']->ref_number,
                'type'     => 'eform',
            ]);

            $notification = $notificationBuilder->build();
            $data         = $dataBuilder->build();
            $topic = new Topics();
            $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('branch_'.$branch_id)->andTopic('pinca');

            $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
            $topicResponse->isSuccess();
            $topicResponse->shouldRetry();
            $topicResponse->error();
        }else {
            lknPinca($credentials);
            // lknManagerCollateral($credentials);
        }
    }

    function lknPinca($credentials)
    {
        $data      = $credentials;
        $userLogin = $data['credentials'];
        $branch_id = substr('0'.$userLogin['branch_id'], -3);

        $message   = getMessage("eform_lkn");
        $userModel = $data['user'];
        $userNotif = new UserNotification;

        $userModel->notify(new LKNEFormCustomer($data['data']));

        $notificationBuilder = new PayloadNotificationBuilder($message['title']);
        $notificationBuilder->setBody($message['body'])
                            ->setSound('default');

        // Get data from notifications table
        $notificationData = $userNotif->where('slug', $data['data']->id)
                                      ->where('type_module', 'eform')
                                      ->orderBy('created_at', 'desc')->first();
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'       => $notificationData['id'],
            'slug'     => $data['data']->ref_number,
            'type'     => 'eform',
        ]);

        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();
        $topic = new Topics();

        $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('branch_'.$branch_id)->andTopic('pinca');

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function lknManagerCollateral($credentials)
    {
        $data      = $credentials;
        $userLogin = $data['credentials'];
        $branch_id = $userLogin['branch_id'];

        $message   = getMessage("eform_lkn");
        $userModel = $data['user'];
        $userNotif = new UserNotification;

        $notificationBuilder = new PayloadNotificationBuilder($message['title']);
        $notificationBuilder->setBody($message['body'])
                            ->setSound('default');

        // Get data from notifications table
        $notificationData = $userNotif->where('slug', $data['data']->id)
                                      ->where('type_module', 'eform')
                                      ->orderBy('created_at', 'desc')->first();
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'       => $notificationData['id'],
            'slug'     => $data['data']->ref_number,
            'type'     => 'eform',
        ]);

        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();
        $topic = new Topics();
        $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('branch_'.$branch_id)->andTopic('manager_collateral_'.'00070828');

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function disposition($credentials){
        $data      = $credentials['eform'];
        $aoId      = $credentials['ao_id'];
        $message   = getMessage("eform_disposition");

        $userNotif = new UserNotification;
        $notificationBuilder = new PayloadNotificationBuilder($message['title']);
        $notificationBuilder->setBody($message['body'])
                            ->setSound('default');
        // Get data from notifications table
        $notificationData = $userNotif->where('slug', $data->id)
                                      ->where('type_module', 'eform')
                                      ->orderBy('created_at', 'desc')->first();

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'         => $notificationData['id'],
            'slug'       => $data->ref_number,
            'type'       => 'eform'
        ]);

        $notification = $notificationBuilder->build();
        $payload         = $dataBuilder->build();
        $topic = new Topics();
        $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('branch_'.$data->branch_id)->andTopic('ao_'.$aoId);

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $payload);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function createEForm($credentials){
        $dataUser = $credentials;
        $user     = $dataUser['request']->user();
        $message  = getMessage("eform_create");

        if($user != null){
            $notificationBuilder = new PayloadNotificationBuilder($message['title']);
            $notificationBuilder->setBody($message['body'])
                                ->setSound('default');
            // Get data from notifications table
            $usersModel = User::FindOrFail($dataUser['data']->user_id);
            $usersModel->notify(new PengajuanKprNotification($dataUser['data']));

            $notificationData = UserNotification::where('slug', $dataUser['data']->id)
                                            ->where('type_module', 'eform')
                                            ->orderBy('created_at', 'desc')->first();

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData([
                'id'   => $notificationData['id'],
                'slug' => $dataUser['data']->ref_number,
                'type' => 'eform',
            ]);

            $notification = $notificationBuilder->build();
            $data         = $dataBuilder->build();
            $topic        = new Topics();

            $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('branch_'.$dataUser['data']->branch_id)->andTopic('pinca');

            $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
            $topicResponse->isSuccess();
            $topicResponse->shouldRetry();
            $topicResponse->error();
        }else{
            $user_login = \RestwsHc::getUser();
            if($user_login['role'] == 'staff'){
                $notificationBuilder = new PayloadNotificationBuilder($message['title']);
                $notificationBuilder->setBody($message['body'])
                                    ->setSound('default');
                $usersModel = User::FindOrFail($dataUser['data']->user_id);
                $usersModel->notify(new PengajuanKprNotification($dataUser['data']));
                // Get data from notifications table
                $notificationData = UserNotification::where('slug', $dataUser['data']->id)
                                                ->where('type_module', 'eform')
                                                ->orderBy('created_at', 'desc')->first();

                $dataBuilder = new PayloadDataBuilder();
                $dataBuilder->addData([
                    'id'   => $notificationData['id'],
                    'slug' => $dataUser['data']->ref_number,
                    'type' => 'eform',
                ]);

                $notification = $notificationBuilder->build();
                $data         = $dataBuilder->build();
                $topic        = new Topics();

                $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('branch_'.$dataUser['data']->branch_id)->andTopic('pinca');

                $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
                $topicResponse->isSuccess();
                $topicResponse->shouldRetry();
                $topicResponse->error();
            }else if($user_login['role'] == 'ao'){
                $notificationBuilder = new PayloadNotificationBuilder($message['title']);
                $notificationBuilder->setBody($message['body'])
                                    ->setSound('default');
                // Get data from notifications table
                $usersModel = User::FindOrFail($dataUser['data']->user_id);
                $usersModel->notify(new PengajuanKprNotification($dataUser['data']));

                $notificationData = UserNotification::where('slug', $dataUser['data']->id)
                                                ->where('type_module', 'eform')
                                                ->orderBy('created_at', 'desc')->first();

                $dataBuilder = new PayloadDataBuilder();
                $dataBuilder->addData([
                    'id'   => $notificationData['id'],
                    'slug' => $dataUser['data']->ref_number,
                    'type' => 'eform',
                ]);

                $notification = $notificationBuilder->build();
                $data         = $dataBuilder->build();
                $topic        = new Topics();

                $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('branch_'.$dataUser['data']->branch_id)->andTopic('pinca');

                $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
                $topicResponse->isSuccess();
                $topicResponse->shouldRetry();
                $topicResponse->error();
            }else {
                $notificationBuilder = new PayloadNotificationBuilder($message['title']);
                $notificationBuilder->setBody($message['body'])
                                    ->setSound('default');
                // Get data from notifications table
                $usersModel = User::FindOrFail($dataUser['data']->user_id);
                $usersModel->notify(new PengajuanKprNotification($dataUser['data']));

                $notificationData = UserNotification::where('slug', $dataUser['data']->id)
                                                ->where('type_module', 'eform')
                                                ->orderBy('created_at', 'desc')->first();

                $dataBuilder = new PayloadDataBuilder();
                $dataBuilder->addData([
                    'id'   => $notificationData['id'],
                    'slug' => $dataUser['data']->ref_number,
                    'type' => 'eform',
                ]);

                $notification = $notificationBuilder->build();
                $data         = $dataBuilder->build();
                $topic        = new Topics();

                $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('branch_'.$dataUser['data']->branch_id)->andTopic('pinca');

                $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
                $topicResponse->isSuccess();
                $topicResponse->shouldRetry();
                $topicResponse->error();
            }
        }
    }

    function approveEForm($credentials){
        $data = $credentials;
        if (!empty($data['clas'])) {
            approveEFormToCustomer($credentials, true);
            approveEFormToAO($credentials, true);
            approveEFormToPinca($credentials, true);
        }else {
            approveEFormToCustomer($credentials);
            approveEFormToAO($credentials);
        }
    }

    function approveEFormToCustomer($credentials, $clas = null)
    {
        if ( !empty($clas) ) {
            $data      = $credentials;
            $userId    = $data['data']->user_id;
            $userModel = $data['user'];
            $userNotif = new UserNotification;

            $message   = getMessage("eform_approve");

            // $userModel->notify(new ApproveEFormCustomer($data['data'])); // untuk ke customer
            $userModel->notify(new ApproveEFormCLASCustomer($data['data']));

            $notificationBuilder = new PayloadNotificationBuilder($message['title']);
            $notificationBuilder->setBody($message['body'])
                                ->setSound('default');

            // Get data from notifications table
            $notificationData = $userNotif->where('slug', $data['data']->id)
                                            ->where('type_module', 'eform')
                                            ->orderBy('created_at', 'desc')->first();

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData([
                'id'   => $notificationData['id'],
                'slug' => $data['data']->id,
                'type' => 'tracking',
            ]);

            $notification = $notificationBuilder->build();
            $data         = $dataBuilder->build();
            $topic        = new Topics();

            // $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('user_'.$userId);
            $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('user_'.$userId);

            $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
            $topicResponse->isSuccess();
            $topicResponse->shouldRetry();
            $topicResponse->error();
        }
    }

    function approveEFormToAO($credentials, $clas = null)
    {
        $data      = $credentials;
        $userId    = $data['data']->user_id;
        $userModel = $data['user'];
        $userNotif = new UserNotification;

        if ( empty($clas) ) {
            $userModel->notify(new ApproveEFormInternal($data['data']));
            $message   = getMessage("eform_approve_ao", $credentials['data']);

            $notificationBuilder = new PayloadNotificationBuilder($message['title']);
            $notificationBuilder->setBody($message['body'])
                                ->setSound('default');

            // Get data from notifications table
            $notificationData = $userNotif->where('slug', $data['data']->id)
                                            ->where('type_module', 'eform')
                                            ->orderBy('created_at', 'desc')->first();

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData([
                'id'   => $notificationData['id'],
                'slug' => $data['data']->id,
                'type' => 'tracking',
            ]);

            $notification = $notificationBuilder->build();
            $data         = $dataBuilder->build();
            $topic        = new Topics();

            $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('branch_'.$credentials['data']->branch_id)->andTopic('ao_'.$credentials['data']->ao_id);

            $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
            $topicResponse->isSuccess();
            $topicResponse->shouldRetry();
            $topicResponse->error();
        }
    }

    function approveEFormToPinca($credentials)
    {
        $data      = $credentials;
        $userId    = $data['data']->user_id;
        $userModel = $data['user'];

        $message   = getMessage("eform_approve_clas", $data['data']);

        $userNotif = new UserNotification;
        $userModel->notify(new ApproveEFormCLAS($data['data']));

        $notificationBuilder = new PayloadNotificationBuilder($message['title']);
        $notificationBuilder->setBody($message['body'])
                            ->setSound('default');

        // Get data from notifications table
        $notificationData = $userNotif->where('slug', $data['data']->id)
                                        ->where('type_module', 'eform')
                                        ->orderBy('created_at', 'desc')->first();

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'   => $notificationData['id'],
            'slug' => $data['data']->id,
            'type' => 'tracking',
        ]);

        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();
        $topic        = new Topics();

        // $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('user_'.$userId);
        $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('branch_'.$credentials['data']->branch_id)->andTopic('pinca')->orTopic('ao_'.$credentials['data']->ao_id);

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function rejectEForm($credentials){
        $data = $credentials;

        if (!empty($data['clas'])) {
            RejectEFormToCustomer($credentials, true);
            RejectEFormToAO($credentials, true);
            RejectEFormToPinca($credentials, true);
        }else {
            RejectEFormToCustomer($credentials);
            RejectEFormToAO($credentials);
        }
    }

    function RejectEFormToCustomer($credentials, $clas = null)
    {
        $data      = $credentials;
        $userId    = $data['data']->user_id;
        $userModel = $data['user'];
        $userNotif = new UserNotification;

        $message   = getMessage("eform_reject");
        if (empty($clas))
        {
            $userModel->notify(new RejectEFormCustomer($data['data']));
        }else {
            $userModel->notify(new RejectEFormCLASCustomer($data['data']));
        }

        $notificationBuilder = new PayloadNotificationBuilder($message['title']);
        $notificationBuilder->setBody($message['body'])
                            ->setSound('default');

        // Get data from notifications table
        $notificationData = $userNotif->where('slug', $data['data']->id)
                                        ->where('type_module', 'eform')
                                        ->orderBy('created_at', 'desc')->first();

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'   => $notificationData['id'],
            'slug' => $data['data']->id,
            'type' => 'tracking',
        ]);

        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();
        $topic        = new Topics();

        $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('user_'.$userId);

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function RejectEFormToAO($credentials, $clas = null)
    {
        $data      = $credentials;
        $userId    = $data['data']->user_id;
        $userModel = $data['user'];
        $userNotif = new UserNotification;

        if ( empty($clas) ) {
            $userModel->notify(new RejectEFormInternal($data['data']));
            $message = getMessage("eform_reject_ao", $credentials['data']);

            $notificationBuilder = new PayloadNotificationBuilder($message['title']);
            $notificationBuilder->setBody($message['body'])
                                ->setSound('default');

            // Get data from notifications table
            $notificationData = $userNotif->where('slug', $data['data']->id)
                                            ->where('type_module', 'eform')
                                            ->orderBy('created_at', 'desc')->first();

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData([
                'id'   => $notificationData['id'],
                'slug' => $data['data']->id,
                'type' => 'tracking',
            ]);

            $notification = $notificationBuilder->build();
            $data         = $dataBuilder->build();
            $topic        = new Topics();

        $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('branch_'.$credentials['data']->branch_id)->andTopic('ao_'.$credentials['data']->ao_id);

            $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
            $topicResponse->isSuccess();
            $topicResponse->shouldRetry();
            $topicResponse->error();
        }
    }

    function RejectEFormToPinca($credentials)
    {
        $data      = $credentials;
        $userId    = $data['data']->user_id;
        $userModel = $data['user'];

        $message   = getMessage("eform_reject_clas", $credentials['data']);

        $userNotif = new UserNotification;
        $userModel->notify(new RejectEFormCLAS($data['data']));

        $notificationBuilder = new PayloadNotificationBuilder($message['title']);
        $notificationBuilder->setBody($message['body'])
                            ->setSound('default');

        // Get data from notifications table
        $notificationData = $userNotif->where('slug', $data['data']->id)
                                        ->where('type_module', 'eform')
                                        ->orderBy('created_at', 'desc')->first();

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'   => $notificationData['id'],
            'slug' => $data['data']->id,
            'type' => 'tracking',
        ]);

        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();
        $topic        = new Topics();

        $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('branch_'.$credentials['data']->branch_id)->andTopic('pinca')->orTopic('ao_'.$credentials['data']->ao_id);

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function collateralNotification($credentials){
       $user_id= $credentials['user_id'];
       $bodyNotif= $credentials['bodyNotif'];
       $headerNotif= $credentials['headerNotif'];
       $id = $credentials['id'];
       $type= $credentials['type'];
       $slug = $credentials['slug'];
       $receiver = $credentials['receiver'];

        $notificationBuilder = new PayloadNotificationBuilder($headerNotif);
        $notificationBuilder->setBody($bodyNotif)
                              ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'       => $id,
            'slug'     => $slug,
            'type'     => 'collateral',
        ]);

        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();
        $topic = new Topics();
        if($receiver=='staf_collateral'){
             $dataUser  = UserServices::where('pn',$user_id)->first();
             $branch_id = $dataUser['branch_id'];
             $user_id = substr('0000'.$user_id, -8);
             $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('branch_'.$branch_id)->andTopic('staff_collateral_'.$user_id)->orTopic('ao_'.$user_id);
        }else if ($receiver=='external'){  //send to external mobile apps
             $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('user_'.$user_id);
        }else if ($receiver=='manager_collateral'){
             $dataUser  = UserServices::where('pn',$user_id)->first();
             $branch_id = $dataUser['branch_id'];
             $user_id = substr('0000'.$user_id, -8);
             $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('branch_'.$branch_id)->andTopic('manager_collateral_'.$user_id);
        }else if($receiver =='ao'){
            $dataUser  = UserServices::where('pn',$user_id)->first();
            $branch_id = $dataUser['branch_id'];
            $user_id = substr('0000'.$user_id, -8);
            $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'))->andTopic('branch_'.$branch_id)->andTopic('ao_'.$user_id);
        }else{
            $topic->topic(env('PUSH_NOTIFICATION_TOPICS', 'testing'));
        }
        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }
}
