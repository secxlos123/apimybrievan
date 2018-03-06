<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Artisaninweb\SoapWrapper\SoapWrapper;
use App\Models\BRIGUNA;

class SchedulerRekening extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SchedulerRekening:updaterekening';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update rekening local from brinets';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     parent::__construct();
    // }

    protected $soapWrapper;

    public function __construct(SoapWrapper $soapWrapper) {
        parent::__construct();
        $this->soapWrapper = $soapWrapper;      
    }

    function client() {
        $url = config('restapi.asmx_las');
        return new \SoapClient($url);
    }

    function return_conten($respons){
        try {
            $data = (array) $respons;
            if (isset($data['items'])) {
                $conten = [
                    'code'         => $data['statusCode'],
                    'descriptions' => $data['statusDesc'],
                    'contents' => [
                        'data' => $data['items']
                    ]
                ];
            } else {
                $conten = [
                    'code'         => $data['statusCode'],
                    'descriptions' => $data['statusDesc'],
                    'contents' => [
                        'data' => []
                    ]
                ];
            }          
            return $conten;
        } catch (Exception $e) {
            return [
                'code' => 05, 
                'descriptions' => 'Gagal Koneksi Jaringan',
                'contents' => [
                    'data' => 'Gagal Koneksi Jaringan'
                ]
            ];
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            \Log::info('handle running scheduler aman nih coyy');
            /*$data_briguna = \DB::table("briguna")
                        ->select(['id','eform_id','id_aplikasi','cif','no_rekening'])
                        ->whereNotNull('id_aplikasi')
                        ->whereNull('no_rekening')
                        ->orWhere('no_rekening','=','')
                        ->orderBy('id','asc')
                        ->limit('10')
                        ->get()->toArray();
            // print_r($data_briguna);exit();
            if (!empty($data_briguna)) {
                $message = [
                    'message'  => 'data briguna kosong',
                    'contents' => ''
                ];
                // print_r($data_briguna);exit();
                $client = $this->client();
                foreach ($data_briguna as $key => $value) {
                    try {
                        // if ((!isset($value->no_rekening) || $value->no_rekening == '') && $value->id_aplikasi != '') {
                            $parameter['id_aplikasi'] = $value->id_aplikasi;
                            $rekening = $client->getStatusInterface($parameter);
                            if($rekening->getStatusInterfaceResult){
                                $datadetail = json_decode($rekening->getStatusInterfaceResult);
                                $result = $this->return_conten($datadetail);
                                // print_r($result);
                                \Log::info($result);
                                if ($result['code'] == '01') {
                                    $update_data = [
                                        'eform_id'    => $value->eform_id,
                                        'is_send'     => 6,
                                        'no_rekening' => $result['contents']['data'][0]->NO_REKENING,
                                        'cif'         => $result['contents']['data'][0]->CIF,
                                        'cif_las'     => $result['contents']['data'][0]->CIF_LAS,
                                    ];

                                    $briguna = BRIGUNA::where("eform_id", "=", $value->eform_id);
                                    $briguna->update($update_data);

                                    // save json_ws_log
                                    $data_log = [
                                        'json_data' => 'scheduler update nomer rekening sukses',
                                        'function_name' => 'updateRekening',
                                        'created_at'=> date('Y-m-d H:i:s')
                                    ];
                                    $save = \DB::table('json_ws_log')->insert($data_log);
                                    \Log::info('Sukses Update Rekening ALL dan simpan json_ws_log');
                                    $message = [
                                        'message'  => 'Sukses update briguna',
                                        'contents' => $briguna
                                    ];
                                }
                            }
                        // }
                    } catch (Exception $e) {
                        // save json_ws_log
                        $data_log = [
                            'json_data' => 'scheduler update nomer rekening gagal',
                            'function_name' => 'updateRekening',
                            'created_at'=> date('Y-m-d H:i:s')
                        ];
                        $save = \DB::table('json_ws_log')->insert($data_log);
                        \Log::info('Gagal Update Rekening ALL dan simpan json_ws_log');
                        return response()->error([
                            'message' => 'Koneksi Jaringan Gagal'.$e,
                            'contents' => ''
                        ], 400 );
                    }
                }
                
                return $message;
            } else {
                // save json_ws_log
                $data_log = [
                    'json_data' => 'Gagal update rekening, karena no rekening sudah tersimpan dilocal mybri',
                    'function_name' => 'updateRekening',
                    'created_at'=> date('Y-m-d H:i:s')
                ];
                $save = \DB::table('json_ws_log')->insert($data_log);
                \Log::info('Gagal Update Rekening ALL, karena no rekening sudah tersimpan dilocal mybri');
                return response()->error([
                    'message' => 'Hasil inquiry data rekening briguna tidak ditemukan',
                    'contents' => ''
                ], 400 );
            }*/
        } catch (Exception $e) {
            // save json_ws_log
            $data_log = [
                'json_data' => 'scheduler update nomer rekening gagal',
                'function_name' => 'updateRekening',
                'created_at'=> date('Y-m-d H:i:s')
            ];
            $save = \DB::table('json_ws_log')->insert($data_log);
            \Log::info('Gagal Update Rekening ALL dan simpan json_ws_log');
            return response()->error([
                'message' => 'Koneksi Jaringan Gagal'.$e,
                'contents' => ''
            ], 400 );
        }
    }
}
