<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
// use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Controller;
use App\Models\KodePos;
use App\Models\ApiLas;
use App\Models\EForm;
use App\Models\Customer;
use App\Models\CustomerDetail;
use App\Models\BRIGUNA;
use App\Models\EformBriguna;
use Asmx;
use File;
use Zip;
use Artisaninweb\SoapWrapper\SoapWrapper;

class ApiLasController extends Controller
{
    protected $soapWrapper;

    public function __construct(SoapWrapper $soapWrapper) {
        $this->soapWrapper = $soapWrapper;      
    }

    function client() {
        $url = config('restapi.asmx_las');
        return new \SoapClient($url);
    }

    public function index(Request $request) {        
        // print_r($request);exit();
        $ApiLas  = new ApiLas();
        $respons = $request->all();
        $method  = $respons['requestMethod'];
        $data    = '';
        if (!empty($respons['requestData'])) {
            $data = $respons['requestData'];
        }
        
        switch ($method) {
            case 'insertDataDebtPerorangan':
                // if (!empty($data)) {
                    $data = $respons;
                    /*$data['kodepos']            = '';
                    $data['kelurahan']          = '';
                    $data['kecamatan']          = '';
                    $data['kabupaten']          = '';
                    $data['kodepos_domisili']   = '';
                    $data['kelurahan_domisili'] = '';
                    $data['kecamatan_domisili'] = '';
                    $data['kabupaten_domisili'] = '';
                    $data['kota_domisili']      = '';
                    $data['propinsi_domisili']  = '';
                    $data['kodepos_usaha']      = '';
                    $data['kelurahan_usaha']    = '';
                    $data['kecamatan_usaha']    = '';
                    $data['kabupaten_usaha']    = '';
                    $data['kota_usaha']         = '';
                    $data['propinsi_usaha']     = '';                

                    if (!empty($data['kode_pos'])) {
                        $kode_pos = ['key' => $data['kode_pos']];
                        $kodepos  = KodePos::filter($kode_pos)->get();
                        $pos      = $kodepos->toArray();
                        if (!empty($pos)) {
                            foreach ($pos as $index => $value) {
                                $kota = explode(" ", $value['Kota']);
                                // print_r($kota);exit();
                                $data['kodepos']   = $value['postal_code'];
                                $data['kelurahan'] = $value['Kelurahan'];
                                $data['kecamatan'] = $value['Kecamatan'];
                                $data['kabupaten'] = $kota[1];
                            }
                        }
                    }
                    
                    if (!empty($data['kode_pos_domisili'])) {
                        $kode_pos_dom = ['key' => $data['kode_pos_domisili']];
                        $kodepos_dom  = KodePos::filter($kode_pos_dom)->get();
                        $pos_dom      = $kodepos_dom->toArray();
                        if (!empty($pos_dom)) {
                            foreach ($pos_dom as $index => $value) {
                                $kota = explode(" ", $value['Kota']);
                                // print_r($value);exit();
                                $data['kodepos_domisili']   = $value['postal_code'];
                                $data['kelurahan_domisili'] = $value['Kelurahan'];
                                $data['kecamatan_domisili'] = $value['Kecamatan'];
                                $data['kabupaten_domisili'] = $kota[1];
                                $data['kota_domisili']      = $kota[1];
                                $data['propinsi_domisili']  = $value['Propinsi'];
                                $data['kodepos_usaha']      = $value['postal_code'];
                                $data['kelurahan_usaha']    = $value['Kelurahan'];
                                $data['kecamatan_usaha']    = $value['Kecamatan'];
                                $data['kabupaten_usaha']    = $kota[1];
                                $data['kota_usaha']         = $kota[1];
                                $data['propinsi_usaha']     = $value['Propinsi'];
                            }
                        }
                    }*/

                    if ($request['transaksi_normal_harian'] == '1') {
                        $gaji = "G1";
                    } else if ($request['transaksi_normal_harian'] == '2') {
                        $gaji = "G3";
                    } else if ($request['transaksi_normal_harian'] == '3') {
                        $gaji = "G4";
                    } else if ($request['transaksi_normal_harian'] == '4' || $request['transaksi_normal_harian'] == '5') {
                        $gaji = "G5";
                    } else {
                        $gaji = "G2";
                    }

                    $data['gaji'] = $gaji;
                    $insert = $this->insertAllAnalisa($data);
                    return $insert;
                // } 
                // $error[0] = 'Uknown request data';
                // return [
                //     'code' => 05, 
                //     'descriptions' => 'Uknown request data',
                //     'contents' => [
                //         'data' => $error
                //     ]
                // ];
                // print_r($data);exit();
                // $insert = $ApiLas->insertDataDebtPerorangan($data);
                break;

            case 'insertPrescreeningBriguna':
                // $insert = $ApiLas->insertPrescreeningBriguna($data);
                $insert = $this->insertPrescreeningBriguna($data);
                return $insert;
                break;
        
            case 'insertPrescoringBriguna':
                // $insert = $ApiLas->insertPrescoringBriguna($data);
                $insert = $this->insertPrescoringBriguna($data);
                return $insert;
                break;

            case 'insertDataKreditBriguna':
                // $insert = $ApiLas->insertDataKreditBriguna($data);
                $insert = $this->insertDataKreditBriguna($data);
                return $insert;
                break;

            /*case 'insertAgunanLainnya':
                $insert = $ApiLas->insertAgunanLainnya($data);
                return $insert;
                break;*/

            case 'hitungCRSBrigunaKarya':
                // $hitung = $ApiLas->hitungCRSBrigunaKarya($data);
                $hitung = $this->hitungCRSBrigunaKarya($data);
                return $hitung;
                break;

            case 'kirimPemutus':
                // $kirim = $ApiLas->kirimPemutus($data);
                $kirim = $this->kirimPemutus($data);
                return $kirim;
                break;

            case 'getStatusInterface':
                // $getData = $ApiLas->getStatusInterface($data);
                // return $getData;
                try {
                    $parameter['id_aplikasi'] = $data;
                    $client = $this->client();
                    $resultclient = $client->getStatusInterface($parameter);
                    // print_r($resultclient);exit();
                    if($resultclient->getStatusInterfaceResult){
                        $datadetail = json_decode($resultclient->getStatusInterfaceResult);
                        $dataResult = (array) $datadetail;
                        if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                            // getdata
                            if(isset($datadetail->items)){
                                $result = $dataResult;
                                return $result;
                            }
                        }
                        $result = $dataResult;
                        return $result;
                    }
                    return "Error Exception";
                }
                catch(SoapFault $f){
                    return "Error Exception";
                }
                break;

            case 'putusSepakat':
                $putus = $this->putusan($data);
                return $putus;
                break;

            /*case 'inquiryHistoryDebiturPerorangan':
                if (!empty($data)) {
                    $inquiry = $ApiLas->inquiryHistoryDebiturPerorangan($data);
                    if ($inquiry['statusCode'] == '01') {
                        $result  = $inquiry['data'][0]['items'];
                        $result[0]['ID_KREDIT']  = $inquiry['data'][1]['items'][0]['id_kredit'];
                        $result[0]['NO_REKENING']= $inquiry['data'][1]['items'][0]['no_rekening'];
                        $result[0]['BAKI_DEBET'] = $inquiry['data'][1]['items'][0]['baki_debet'];
                        $result[0]['BISA_SP']    = $inquiry['data'][1]['items'][0]['bisa_SP'];
                        $conten = [
                            'code'         => $inquiry['statusCode'],
                            'descriptions' => $inquiry['statusDesc'],
                            'contents' => [
                                'data' => $result
                            ]
                        ];
                        // print_r($conten);exit();
                        return $conten;
                    }
                    return $inquiry;
                }
                $error[0] = 'Uknown request data';
                return [
                    'code' => 05, 
                    'descriptions' => 'Uknown request data',
                    'contents' => [
                        'data' => $error
                    ]
                ];
                break;*/

            case 'inquiryListPutusan':
                if (!empty($data)) {
                    $pn      = substr('00000000'. $data, -8 );
                    $inquiryUserLAS = $this->loginLAS($pn);

                    if ($inquiryUserLAS['code'] == '01') {
                        $uid = $inquiryUserLAS['contents']['data'][0]->uid;
                        // $inquiry = $ApiLas->inquiryListPutusan($uid);
                        // $conten  = $this->return_conten($inquiry);
                        // return $conten;

                        $parameter['uid'] = $uid;
                        try {
                            $client = $this->client();
                            $resultclient = $client->inquiryListPutusan($parameter);
                            // print_r($resultclient);exit();
                            if($resultclient->inquiryListPutusanResult){
                                $datadetail = json_decode($resultclient->inquiryListPutusanResult);
                                if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                                    // getdata sukses
                                    if(isset($datadetail->items)){
                                        $result = $this->return_conten($datadetail);
                                        return $result;
                                    }
                                }
                                $result = $this->return_conten($datadetail);
                                return $result;
                            }
                            $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                            return [
                                'code' => 04, 
                                'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                                'contents' => [
                                    'data' => $error
                                ]
                            ];
                        }
                        catch(SoapFault $f){
                            $error[0] = 'Gagal Koneksi Jaringan';
                            return [
                                'code' => 04, 
                                'descriptions' => 'Gagal Koneksi Jaringan',
                                'contents' => [
                                    'data' => $error
                                ]
                            ];
                        }
                    } else {
                        $error[0] = 'Hasil Inquiry Kosong / Anda belum memiliki user LAS';
                        return [
                            'code' => 04, 
                            'descriptions' => 'Hasil Inquiry Kosong / Anda belum memiliki user LAS',
                            'contents' => [
                                'data' => $error
                            ]
                        ];
                    }
                }
                $error[0] = 'Uknown request data';
                return [
                    'code' => 05, 
                    'descriptions' => 'Uknown request data',
                    'contents' => [
                        'data' => $error
                    ]
                ];
                break;

            case 'inquiryListVerputADK':
                if (!empty($data)) {
                    // $kode_cabang = substr('00000',$data, -5);
                    // $inquiry = $ApiLas->inquiryListVerputADK($data);
                    // $conten  = $this->return_conten($inquiry);
                    // return $conten;

                    $parameter['branch'] = $data;
                    try {
                        $client = $this->client();
                        $resultclient = $client->inquiryListVerputADK($parameter);
                        // print_r($resultclient);exit();
                        if($resultclient->inquiryListVerputADKResult){
                            $datadetail = json_decode($resultclient->inquiryListVerputADKResult);

                            if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                                // getdata sukses
                                if(isset($datadetail->items)){
                                    $result = $this->return_conten($datadetail);
                                    return $result;
                                }
                            }
                            $result = $this->return_conten($datadetail);
                            return $result;
                        }
                        $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                        return [
                            'code' => 04, 
                            'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                            'contents' => [
                                'data' => $error
                            ]
                        ];
                    }
                    catch(SoapFault $f){
                        $error[0] = 'Gagal Koneksi Jaringan';
                        return [
                            'code' => 04, 
                            'descriptions' => 'Gagal Koneksi Jaringan',
                            'contents' => [
                                'data' => $error
                            ]
                        ];
                    }
                }

                $error[0] = 'Uknown request data';
                return [
                    'code' => 05, 
                    'descriptions' => 'Uknown request data',
                    'contents' => [
                        'data' => $error
                    ]
                ];
                break;

            case 'inquiryPremiAJKO':
                if (!empty($data)) {
                    $params = [
                        "loantype" => $data['loantype'],
                        "jup"      => $data['jup'], 
                        "tgl_lahir"=> $data['tgl_lahir'],
                        "term"     => $data['term'], 
                        "rate"     => $data['rate']
                    ];

                    // $inquiry = $ApiLas->inquiryPremiAJKO($params);
                    // print_r($inquiry);exit();
                    // if ($inquiry['statusCode'] == '01') {
                    //     $conten  = $this->return_conten($inquiry);
                    //     return $conten;
                    // }
                    // return $inquiry;

                    $parameter['JSONData'] = json_encode($params);
                    try {
                        $client = $this->client();
                        $resultclient = $client->inquiryPremiAJKO($parameter);
                        // print_r($resultclient);exit();
                        if($resultclient->inquiryPremiAJKOResult){
                            $datadetail = json_decode($resultclient->inquiryPremiAJKOResult);

                            if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                                // get data sukses
                                if(isset($datadetail->items)){
                                    $result = $this->return_conten($datadetail);
                                    return $result;
                                }
                            }
                            $result = $this->return_conten($datadetail);
                            return $result;
                        }
                        $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                        return [
                            'code' => 04, 
                            'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                            'contents' => [
                                'data' => $error
                            ]
                        ];
                    }
                    catch(SoapFault $f){
                        $error[0] = 'Gagal Koneksi Jaringan';
                        return [
                            'code' => 04, 
                            'descriptions' => 'Gagal Koneksi Jaringan',
                            'contents' => [
                                'data' => $error
                            ]
                        ];
                    }
                }
                $error[0] = 'Uknown request data';
                return [
                    'code' => 05, 
                    'descriptions' => 'Uknown request data',
                    'contents' => [
                        'data' => $error
                    ]
                ];
                break;

            case 'eformBriguna':
                $inquiry = $ApiLas->eform_briguna($data);
                return $inquiry;
                break;

            case 'inquiryUserLAS':
                // $inquiry = $ApiLas->inquiryUserLAS($data);
                // $conten  = $this->return_conten($inquiry);
                // return $conten;
                $result = $this->loginLAS($data);
                return $result;
                break;

            case 'inquiryInstansiBriguna':
                // $inquiry = $ApiLas->inquiryInstansiBriguna();
                // $conten  = $this->return_conten($inquiry);
                // return $conten;
                try {
                    $client = $this->client();
                    $resultclient = $client->inquiryInstansiBriguna();
                    if($resultclient->inquiryInstansiBrigunaResult){
                        $datadetail = json_decode($resultclient->inquiryInstansiBrigunaResult);
                        if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                            // get data sukses
                            if(isset($datadetail->items)){
                                $result = $this->return_conten($datadetail);
                                return $result;
                            }
                        }
                        $result = $this->return_conten($datadetail);
                        return $result;
                    }
                    $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                catch(SoapFault $f){
                    $error[0] = 'Gagal Koneksi Jaringan';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi Jaringan',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                break;

            case 'inquirySifatKredit':
                // $inquiry = $ApiLas->inquirySifatKredit($data);
                // $conten  = $this->return_conten($inquiry);
                // return $conten;
                try {
                    $client = $this->client();
                    $resultclient = $client->inquirySifatKredit();
                    if($resultclient->inquirySifatKreditResult){
                        $datadetail = json_decode($resultclient->inquirySifatKreditResult);
                        if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                            // get data sukses
                            if(isset($datadetail->items)){
                                $result = $this->return_conten($datadetail);
                                return $result;
                            }
                        }
                        $result = $this->return_conten($datadetail);
                        return $result;
                    }
                    $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                catch(SoapFault $f){
                    $error[0] = 'Gagal Koneksi Jaringan';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi Jaringan',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                break;

            case 'inquiryGelar':
                // $inquiry = $ApiLas->inquiryGelar();
                // $conten  = $this->return_conten($inquiry);
                // return $conten;
                try {
                    $client = $this->client();
                    $resultclient = $client->inquiryGelar();
                    if($resultclient->inquiryGelarResult){
                        $datadetail = json_decode($resultclient->inquiryGelarResult);

                        if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                            // get data sukses
                            if(isset($datadetail->items)){
                                $result = $this->return_conten($datadetail);
                                return $result;
                            }
                        }
                        $result = $this->return_conten($datadetail);
                        return $result;
                    }
                    $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                catch(SoapFault $f){
                    $error[0] = 'Gagal Koneksi Jaringan';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi Jaringan',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                break;
                
            case 'inquiryLoantype':
                // $inquiry = $ApiLas->inquiryLoantype();
                // $conten  = $this->return_conten($inquiry);
                // return $conten;
                try {
                    $client = $this->client();
                    $resultclient = $client->inquiryLoantype();
                    if($resultclient->inquiryLoantypeResult){
                        $datadetail = json_decode($resultclient->inquiryLoantypeResult);
                        if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                            // get data sukses
                            if(isset($datadetail->items)){
                                $result = $this->return_conten($datadetail);
                                return $result;
                            }
                        }
                        $result = $this->return_conten($datadetail);
                        return $result;
                    }
                    $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                catch(SoapFault $f){
                    $error[0] = 'Gagal Koneksi Jaringan';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi Jaringan',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                break;

            case 'inquiryJenisPenggunaan':
                // $inquiry = $ApiLas->inquiryJenisPenggunaan();
                // $conten  = $this->return_conten($inquiry);
                // return $conten;
                try {
                    $client = $this->client();
                    $resultclient = $client->inquiryJenisPenggunaan();
                    if($resultclient->inquiryJenisPenggunaanResult){
                        $datadetail = json_decode($resultclient->inquiryJenisPenggunaanResult);
                        if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                            // get data sukses
                            if(isset($datadetail->items)){
                                $result = $this->return_conten($datadetail);
                                return $result;
                            }
                        }
                        $result = $this->return_conten($datadetail);
                        return $result;
                    }
                    $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                catch(SoapFault $f){
                    $error[0] = 'Gagal Koneksi Jaringan';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi Jaringan',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                break;

            case 'inquiryJenisPenggunaanLBU':
                // $inquiry = $ApiLas->inquiryJenisPenggunaanLBU();
                // $conten  = $this->return_conten($inquiry);
                // return $conten;
                try {
                    $client = $this->client();
                    $resultclient = $client->inquiryJenisPenggunaanLBU();
                    if($resultclient->inquiryJenisPenggunaanLBUResult){
                        $datadetail = json_decode($resultclient->inquiryJenisPenggunaanLBUResult);
                        if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                            // get data sukses
                            if(isset($datadetail->items)){
                                $result = $this->return_conten($datadetail);
                                return $result;
                            }
                        }
                        $result = $this->return_conten($datadetail);
                        return $result;
                    }
                    $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                catch(SoapFault $f){
                    $error[0] = 'Gagal Koneksi Jaringan';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi Jaringan',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                break;

            case 'inquirySektorEkonomiLBU':
                // $inquiry = $ApiLas->inquirySektorEkonomiLBU();
                // $conten  = $this->return_conten($inquiry);
                // return $conten;
                try {
                    $client = $this->client();
                    $resultclient = $client->inquirySektorEkonomiLBU();
                    if($resultclient->inquirySektorEkonomiLBUResult){
                        $datadetail = json_decode($resultclient->inquirySektorEkonomiLBUResult);
                        if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                            // get data sukses
                            if(isset($datadetail->items)){
                                $result = $this->return_conten($datadetail);
                                return $result;
                            }
                        }
                        $result = $this->return_conten($datadetail);
                        return $result;
                    }
                    $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                catch(SoapFault $f){
                    $error[0] = 'Gagal Koneksi Jaringan';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi Jaringan',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                break;

            case 'inquirySifatKreditLBU':
                // $inquiry = $ApiLas->inquirySifatKreditLBU();
                // $conten  = $this->return_conten($inquiry);
                // return $conten;
                try {
                    $client = $this->client();
                    $resultclient = $client->inquirySifatKreditLBU();
                    if($resultclient->inquirySifatKreditLBUResult){
                        $datadetail = json_decode($resultclient->inquirySifatKreditLBUResult);
                        if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                            // get data sukses
                            if(isset($datadetail->items)){
                                $result = $this->return_conten($datadetail);
                                return $result;
                            }
                        }
                        $result = $this->return_conten($datadetail);
                        return $result;
                    }
                    $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                catch(SoapFault $f){
                    $error[0] = 'Gagal Koneksi Jaringan';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi Jaringan',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                break;

            case 'inquiryJenisKreditLBU':
                // $inquiry = $ApiLas->inquiryJenisKreditLBU();
                // $conten  = $this->return_conten($inquiry);
                // return $conten;
                try {
                    $client = $this->client();
                    $resultclient = $client->inquiryJenisKreditLBU();
                    if($resultclient->inquiryJenisKreditLBUResult){
                        $datadetail = json_decode($resultclient->inquiryJenisKreditLBUResult);
                        if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                            // get data sukses
                            if(isset($datadetail->items)){
                                $result = $this->return_conten($datadetail);
                                return $result;
                            }
                        }
                        $result = $this->return_conten($datadetail);
                        return $result;
                    }
                    $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                catch(SoapFault $f){
                    $error[0] = 'Gagal Koneksi Jaringan';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi Jaringan',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                break;

            case 'inquiryPromoBriguna':
                // $inquiry = $ApiLas->inquiryPromoBriguna();
                // $conten  = $this->return_conten($inquiry);
                // return $conten;
                try {
                    $client = $this->client();
                    $resultclient = $client->inquiryPromoBriguna();
                    if($resultclient->inquiryPromoBrigunaResult){
                        $datadetail = json_decode($resultclient->inquiryPromoBrigunaResult);
                        if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                            // get data sukses
                            if(isset($datadetail->items)){
                                $result = $this->return_conten($datadetail);
                                return $result;
                            }
                        }
                        $result = $this->return_conten($datadetail);
                        return $result;
                    }
                    $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                catch(SoapFault $f){
                    $error[0] = 'Gagal Koneksi Jaringan';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi Jaringan',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                break;

            case 'inquiryTujuanPenggunaan':
                // $inquiry = $ApiLas->inquiryTujuanPenggunaan();
                // $conten  = $this->return_conten($inquiry);
                // return $conten;
                try {
                    $client = $this->client();
                    $resultclient = $client->inquiryTujuanPenggunaan();
                    if($resultclient->inquiryTujuanPenggunaanResult){
                        $datadetail = json_decode($resultclient->inquiryTujuanPenggunaanResult);
                        if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                            // get data sukses
                            if(isset($datadetail->items)){
                                $result = $this->return_conten($datadetail);
                                return $result;
                            }
                        }
                        $result = $this->return_conten($datadetail);
                        return $result;
                    }
                    $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                catch(SoapFault $f){
                    $error[0] = 'Gagal Koneksi Jaringan';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi Jaringan',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                break;

            case 'inquiryBidangUsaha':
                // $inquiry = $ApiLas->inquiryBidangUsaha();
                // $conten = $this->return_conten($inquiry);
                // return $conten;
                try {
                    $client = $this->client();
                    $resultclient = $client->inquiryBidangUsaha();
                    if($resultclient->inquiryBidangUsahaResult){
                        $datadetail = json_decode($resultclient->inquiryBidangUsahaResult);
                        if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                            // get data sukses
                            if(isset($datadetail->items)){
                                $result = $this->return_conten($datadetail);
                                return $result;
                            }
                        }
                        $result = $this->return_conten($datadetail);
                        return $result;
                    }
                    $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                catch(SoapFault $f){
                   $error[0] = 'Gagal Koneksi Jaringan';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi Jaringan',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                break;

            case 'inquiryBank':
                // $inquiry = $ApiLas->inquiryBank();
                // $conten = $this->return_conten($inquiry);
                // return $conten;
                try {
                    $client = $this->client();
                    $resultclient = $client->inquiryBank();
                    if($resultclient->inquiryBankResult){
                        $datadetail = json_decode($resultclient->inquiryBankResult);
                        if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                            // get data sukses
                            if(isset($datadetail->items)){
                                $result = $this->return_conten($datadetail);
                                return $result;
                            }
                        }
                        $result = $this->return_conten($datadetail);
                        return $result;
                    }
                    $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                catch(SoapFault $f){
                    $error[0] = 'Gagal Koneksi Jaringan';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi Jaringan',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                break;

            case 'inquiryHubunganBank':
                // $inquiry = $ApiLas->inquiryHubunganBank();
                // $conten = $this->return_conten($inquiry);
                // return $conten;
                try {
                    $client = $this->client();
                    $resultclient = $client->inquiryHubunganBank();
                    if($resultclient->inquiryHubunganBankResult){
                        $datadetail = json_decode($resultclient->inquiryHubunganBankResult);
                        if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                            // get data sukses
                            if(isset($datadetail->items)){
                                $result = $this->return_conten($datadetail);
                                return $result;
                            }
                        }
                        $result = $this->return_conten($datadetail);
                        return $result;
                    }
                    $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                catch(SoapFault $f){
                    $error[0] = 'Gagal Koneksi Jaringan';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi Jaringan',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                break;

            case 'inquiryPekerjaan':
                // $inquiry = $ApiLas->inquiryPekerjaan();
                // $conten = $this->return_conten($inquiry);
                // return $conten;
                try {
                    $client = $this->client();
                    $resultclient = $client->inquiryPekerjaan();
                    if($resultclient->inquiryPekerjaanResult){
                        $datadetail = json_decode($resultclient->inquiryPekerjaanResult);
                        if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                            // get data sukses
                            if(isset($datadetail->items)){
                                $result = $this->return_conten($datadetail);
                                return $result;
                            }
                        }
                        $result = $this->return_conten($datadetail);
                        return $result;
                    }
                    $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                catch(SoapFault $f){
                    $error[0] = 'Gagal Koneksi Jaringan';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi Jaringan',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                break;

            case 'inquiryJabatan':
                // $inquiry = $ApiLas->inquiryJabatan();
                // $conten = $this->return_conten($inquiry);
                // return $conten;
                try {
                    $client = $this->client();
                    $resultclient = $client->inquiryJabatan();
                    if($resultclient->inquiryJabatanResult){
                        $datadetail = json_decode($resultclient->inquiryJabatanResult);
                        if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                            // get data sukses
                            if(isset($datadetail->items)){
                                $result = $this->return_conten($datadetail);
                                return $result;
                            }
                        }
                        $result = $this->return_conten($datadetail);
                        return $result;
                    }
                    $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                catch(SoapFault $f){
                    $error[0] = 'Gagal Koneksi Jaringan';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi Jaringan',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                break;

            case 'inquiryJenisPekerjaan':
                // $inquiry = $ApiLas->inquiryJenisPekerjaan();
                // $conten = $this->return_conten($inquiry);
                // return $conten;
                try {
                    $client = $this->client();
                    $resultclient = $client->inquiryJenisPekerjaan();
                    if($resultclient->inquiryJenisPekerjaanResult){
                        $datadetail = json_decode($resultclient->inquiryJenisPekerjaanResult);

                        if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                            // get data sukses
                            if(isset($datadetail->items)){
                                $result = $this->return_conten($datadetail);
                                return $result;
                            }
                        }
                        $result = $this->return_conten($datadetail);
                        return $result;
                    }
                    $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                catch(SoapFault $f){
                    $error[0] = 'Gagal Koneksi Jaringan';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi Jaringan',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                break;

            case 'inquiryDati2':
                // $inquiry = $ApiLas->inquiryDati2();
                // $conten = $this->return_conten($inquiry);
                // return $conten;
                try {
                    $client = $this->client();
                    $resultclient = $client->inquiryDati2();
                    if($resultclient->inquiryDati2Result){
                        $datadetail = json_decode($resultclient->inquiryDati2Result);

                        if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                            // get data sukses
                            if(isset($datadetail->items)){
                                $result = $this->return_conten($datadetail);
                                return $result;
                            }
                        }
                        $result = $this->return_conten($datadetail);
                        return $result;
                    }
                    $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                    return [
                        'code' => 04, 
                        'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                catch(SoapFault $f){
                    $error[0] = 'Gagal Koneksi Jaringan';
                    return [
                        'code' => 05, 
                        'descriptions' => 'Gagal Koneksi Jaringan',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                break;

            case 'inquiryKodePos':
                try {
                    $data_pos = Asmx::setEndpoint('GetDataKodePosBriguna')
                    ->setQuery([
                        'search' => $data['search'],
                        'limit' => $data['limit'],
                        'page' => $data['page'],
                        'sort' => $data['sort']
                    ])->post();
                    return $data_pos;
                } catch (Exception $e) {
                    $error[0] = 'Gagal Koneksi Jaringan';
                    return [
                        'code' => 05, 
                        'descriptions' => 'Gagal Koneksi Jaringan',
                        'contents' => [
                            'data' => $error
                        ]
                    ];
                }
                break;

            default:
                $error[0] = 'Uknown request data';
                return [
                    'code' => 05, 
                    'descriptions' => 'Uknown request method',
                    'contents' => [
                        'data' => $error
                    ]
                ];
                break;
        }
    }

    public function return_conten($respons){
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
            $error[0] = 'Gagal Koneksi Jaringan';
            return [
                'code' => 05, 
                'descriptions' => 'Gagal Koneksi Jaringan',
                'contents' => [
                    'data' => $error
                ]
            ];
        }
    }

    public function putusan($data) {
        if (!empty($data)) {
            // $ApiLas  = new ApiLas();
            $conten_putusan['JSONData'] = json_encode([
                "id_aplikasi" => !isset($data['id_aplikasi'])? "":$data['id_aplikasi'],
                "uid"         => !isset($data['uid'])? "":$data['uid'],
                "flag_putusan"=> !isset($data['flag_putusan'])? "":$data['flag_putusan'],
                "catatan"     => !isset($data['catatan'])? "":$data['catatan']
            ]);
            \Log::info($conten_putusan);
            // $putus = $ApiLas->putusSepakat($conten_putusan);
            // return $putus;
            try {
                // save json_ws_log
                $data_log = [
                    'json_data' => $conten_putusan['JSONData'],
                    'function_name' => 'putusSepakat',
                    'created_at'=> date('Y-m-d H:i:s')
                ];
                $save = \DB::table('json_ws_log')->insert($data_log);
                \Log::info('berhasil save putusSepakat json_ws_log'.$save);
                $client = $this->client();
                $resultclient = $client->putusSepakat($conten_putusan);
                if($resultclient->putusSepakatResult){
                    $datadetail = json_decode($resultclient->putusSepakatResult);
                    $dataResult = (array) $datadetail;

                    if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                        if ($data['flag_putusan'] == '2' || $data['flag_putusan'] == '6') {
                            // update table eforms
                            $eform = EForm::findOrFail($data['eform_id']);
                            $base_request['pinca_name'] = $data['pinca_name'];
                            $base_request['pinca_position'] = $data['pinca_position'];
                            $eform->update($base_request);
                            \Log::info("-------- putusan update table eforms sukses---------");

                            $data_briguna = [
                                'is_send'         => !isset($data['is_send'])? null:$data['is_send'],
                                'tgl_putusan'     => !isset($data['tgl_putusan'])? "":$data['tgl_putusan'],
                                'catatan_pemutus' => !isset($data['catatan_pemutus'])? "":$data['catatan_pemutus']
                            ];
                        } elseif ($data['flag_putusan'] == '7') {
                            $data_briguna = [
                                'is_send'     => !isset($data['is_send'])? null:$data['is_send'],
                                'catatan_adk' => !isset($data['catat_adk'])? "":$data['catat_adk']
                            ];
                        } else {
                            $data_briguna = [
                                'is_send'        => !isset($data['is_send'])? null:$data['is_send'],
                                // 'tgl_putusan'     => !isset($data['tgl_putusan'])? "":$data['tgl_putusan'],
                                // 'catatan_pemutus' => !isset($data['catatan_pemutus'])? "":$data['catatan_pemutus']
                            ];
                        }
                        // update table briguna
                        $briguna = BRIGUNA::where("eform_id", "=", $data['eform_id']);
                        $briguna->update($data_briguna);
                        \Log::info("-------- putusan update table briguna sukses---------");
                        $result = $dataResult;

                        if($datadetail->nama=='TOLAKAN'){
                            $kode_sms = '4';
                        }else{
                            $kode_sms = '3';                            
                        }
                            $eform_sms = DB::table('eforms')
                             ->select('user_id')
                             ->where('eforms.id', $data['eform_id'])
                             ->get();
                    
                                    $eform_sms = $eform_sms->toArray();
                                    $eform_sms = json_decode(json_encode($eform_sms), True);
                                    
                            $customer = DB::table('customer_details')
                             ->select('mobile_phone','first_name','last_name')
                             ->where('users.id', $eform_sms[0]['user_id'])
                             ->get();
                    
                                    $customer = $customer->toArray();
                                    $customer = json_decode(json_encode($customer), True);
                                    
                                    
                                    $briguna = DB::table('briguna')
                                             ->select('year','request_amount')
                                             ->where('briguna.eform_id', $request->eform_id)
                                             ->get();
                                    
                                    $briguna = $briguna->toArray();
                                    $briguna = json_decode(json_encode($briguna), True);
                                    $message = ['no_hp'=>$customer[0]['mobile_phone'],
                                                'plafond'=>$briguna[0]['request_amount'],
                                                'year'=>$briguna[0]['year'],
                                                'nama_cust'=>$customer[0]['first_name'].' '.$customer[0]['last_name'],
                                                'kode_message'=>$kode_sms];             
                                    \Log::info("-------------------sms notifikasi-----------------");
                                    \Log::info($message);
                                    $testing = app('App\Http\Controllers\API\v1\SentSMSNotifController')->sentsms($message);
                                                    \Log::info($testing);

                        return $result;
                    }
                    $result = $dataResult;
                    return $result;
                    \Log::info($result);
                }
                $error[0] = 'Gagal Koneksi DB / Hasil Inquiry Kosong';
                return [
                    'code' => 04, 
                    'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong',
                    'contents' => [
                        'data' => $error
                    ]
                ];
            }
            catch(SoapFault $f){
                $error[0] = 'Gagal Koneksi Jaringan';
                return [
                    'code' => 05, 
                    'descriptions' => 'Gagal Koneksi Jaringan',
                    'contents' => [
                        'data' => $error
                    ]
                ];
            }
        }        

        $error[0] = 'Uknown request data';
        return [
            'code' => 05, 
            'descriptions' => 'Uknown request data',
            'contents' => [
                'data' => $error
            ]
        ];
    }

    public function insertAllAnalisa($request) {
        \Log::info($request);
        $ApiLas  = new ApiLas();
        $user_pn = request()->header('pn');
        $pn      = substr('00000000'. $user_pn, -8 );
        $inquiryUserLAS = $this->loginLAS($pn);
        if ($inquiryUserLAS['code'] == '01') {
            $uid  = $inquiryUserLAS['contents']['data'][0]->uid;
            $uker = substr($inquiryUserLAS['contents']['data'][0]->kode_cabang, -5);
        } else {
            $uid = "";
            $uker= "";
        }

        // insert data debitur
        $content_las_debt = [
            "uid"                   => $uid, // inquiry user las
            "kode_cabang"           => $uker, // inquiry user las
            "penghasilan_per_bulan" => !isset($request['gaji'])?"":$request['gaji'],
            "nama_debitur_1"        => !isset($request['nama_debitur'])?"":$request['nama_debitur'],
            "nama_tanpa_gelar"      => !isset($request['nama_debitur'])?"":$request['nama_debitur'],
            "alias"                 => !isset($request['nama_debitur'])?"":$request['nama_debitur'],
            "tgl_lahir"             => !isset($request['tgl_lahir'])?"":$request['tgl_lahir'],
            "id_instansi"           => !isset($request['instansi'])?"":$request['instansi'],
            "nama_pasangan"         => !isset($request['nama_pasangan'])?"":$request['nama_pasangan'],
            "tgl_lahir_pasangan"    => !isset($request['tgl_lahir_pasangan'])?"":$request['tgl_lahir_pasangan'],
            "no_ktp_pasangan"       => !isset($request['no_ktp_pasangan'])?"":$request['no_ktp_pasangan'],
            "perjanjian_pisah_harta"=> !isset($request['perjanjian_pisah_harta'])?"":$request['perjanjian_pisah_harta'],
            "status_gelar"          => !isset($request['status_gelar_id'])?"":$request['status_gelar_id'],
            "keterangan_status_gelar"=> !isset($request['status_gelar_name'])?"":$request['status_gelar_name'],
            "nama_ibu"              => !isset($request['nama_ibu'])?"":$request['nama_ibu'],
            "jenis_kelamin"         => !isset($request['jenis_kelamin'])?"":$request['jenis_kelamin'],
            "no_ktp"                => !isset($request['no_ktp'])?"":$request['no_ktp'],
            "tempat_lahir"          => !isset($request['tempat_lahir'])?"":$request['tempat_lahir'],
            "usia_mpp"              => !isset($request['usia_mpp'])?"":$request['usia_mpp'],
            "alamat"                => !isset($request['alamat'])?"":$request['alamat'],
            "alamat_usaha"          => !isset($request['alamat_domisili'])?"":$request['alamat_domisili'],
            "alamat_domisili"       => !isset($request['alamat_domisili'])?"":$request['alamat_domisili'],
            "fixed_line"            => !isset($request['no_tlp'])?"0":$request['no_tlp'],
            "no_hp"                 => !isset($request['no_hp'])?"0":$request['no_hp'],
            "lama_menetap"          => !isset($request['lama_menetap'])?"":$request['lama_menetap'],
            "email"                 => !isset($request['email'])?"":$request['email'],
            "tgl_mulai_usaha"       => !isset($request['tgl_mulai_bekerja'])?"":$request['tgl_mulai_bekerja'],
            "kepemilikan_tempat_tinggal" => !isset($request['kepemilikan_tempat_tinggal'])?"":$request['kepemilikan_tempat_tinggal'],
            "jumlah_tanggungan"     => !isset($request['jumlah_tanggungan'])?"":$request['jumlah_tanggungan'],
            "nama_kelg"             => !isset($request['nama_keluarga'])?"":$request['nama_keluarga'],
            "telp_kelg"             => !isset($request['no_tlp_keluarga'])?"":$request['no_tlp_keluarga'],
            "status_perkawinan"     => !isset($request['status_perkawinan'])?"":$request['status_perkawinan'],
            "jenis_rekening"       => !isset($request['jenis_rekening'])?"":$request['jenis_rekening'],
            "nama_bank_lain"       => !isset($request['nama_bank_lain'])?"":$request['nama_bank_lain'],
            "pekerjaan_debitur"     => !isset($request['pekerjaan_debitur'])?"":$request['pekerjaan_debitur'],
            "pernah_pinjam"         => !isset($request['pernah_pinjam'])?"":$request['pernah_pinjam'],
            "transaksi_normal_harian"=> !isset($request['transaksi_normal_harian'])?"":$request['transaksi_normal_harian'],
            "agama"                 => !isset($request['agama'])?"":$request['agama'],
            "ket_agama"             => !isset($request['ket_agama'])?"":$request['ket_agama'],
            "nama_perusahaan"       => !isset($request['company_name'])?"":$request['company_name'],
            "bidang_usaha"          => !isset($request['job_field_id'])?"":$request['job_field_id'],   
            "jenis_pekerjaan"       => !isset($request['job_type_id'])?"":$request['job_type_id'],
            "ket_pekerjaan"         => !isset($request['job_field_id'])?"":$request['job_field_id'],
            "jabatan"               => !isset($request['position'])?"":$request['position'],
            "kode_pos"              => !isset($request['kode_pos'])?"":$request['kode_pos'],
            "kodepos_usaha"         => !isset($request['kode_pos_domisili'])?"":$request['kode_pos_domisili'],
            "kodepos_domisili"      => !isset($request['kode_pos_domisili'])?"":$request['kode_pos_domisili'],
            "kelurahan"             => !isset($request['kelurahan'])?"":$request['kelurahan'],
            "kelurahan_domisili"    => !isset($request['kelurahan_domisili'])?"":$request['kelurahan_domisili'],
            "kelurahan_usaha"       => !isset($request['kelurahan_domisili'])?"":$request['kelurahan_domisili'],
            "kecamatan"             => !isset($request['kecamatan'])?"":$request['kecamatan'],
            "kecamatan_domisili"    => !isset($request['kecamatan_domisili'])?"":$request['kecamatan_domisili'],
            "kecamatan_usaha"       => !isset($request['kecamatan_domisili'])?"":$request['kecamatan_domisili'],
            "kabupaten"             => !isset($request['kabupaten'])?"":$request['kabupaten'],//"0394",
            "kota_domisili"         => !isset($request['kabupaten_domisili'])?"":$request['kabupaten_domisili'],
            "propinsi_domisili"     => !isset($request['propinsi_domisili'])?"":$request['propinsi_domisili'],
            "kota_usaha"            => !isset($request['kabupaten_domisili'])?"":$request['kabupaten_domisili'],
            "propinsi_usaha"        => !isset($request['propinsi_domisili'])?"":$request['propinsi_domisili'],
            "tp_produk"             => !isset($request['tp_produk'])?"":$request['tp_produk'],
            "nama_debitur_2"        => "",
            "nama_debitur_3"        => "",
            "nama_debitur_4"        => "",
            "sumber_utama"          => "1", // hardcode gaji dari mybri
            "cif_las"               => "0", // hardcode debitur baru
            "expired_ktp"           => "31122899", // hardcode
            "kategori_portofolio"   => "175", // hardcode las
            "kewarganegaraan"       => "ID", // hardcode dari las
            "negara_domisili"       => "ID", // hardcode dari las
            "golongan_debitur_sid"  => "907", // hardcode dari las
            "golongan_debitur_lbu"  => "886", // hardcode dari las
            "customer_type"         => "I", // hardcode dari las
            "sub_customer_type"     => "I", // hardcode dari las
            "hub_bank"              => "9900", // hardcode dari las
            "segmen_bisnis_bri"     => "RITEL", // hardcode dari las
            "tgl_mulai_debitur"     => date('dmY'), // hardcode tgl prakarsa
            "federal_wh_code"       => "1", // hardcode dari las
            "resident_flag"         => "Y", // hardcode dari las
            "tujuan_membuka_rekening"=> "ZZ", // hardcode
            "ket_buka_rekening"     => "Pinjaman" // hardcode
        ];

        $insertDebitur = $this->insertDataDebtPerorangan($content_las_debt);
        \Log::info("-------- masuk insert debitur ---------");
        \Log::info($insertDebitur);
        if ($insertDebitur['statusCode'] == '01') {
            // insert prescreening
            $content_prescreening = [
                "Fid_aplikasi"           => !isset($insertDebitur['items'][0]->ID_APLIKASI)?"":$insertDebitur['items'][0]->ID_APLIKASI,
                "Ps_krd"                 => "0",
                "Pks"                    => "0",
                "Daftar_hitam_bi"        => "0",
                "Daftar_kredit_macet_bi" => "0",
                "Daftar_hitam_bri"       => "0",
                "Tunggakan_di_bri"       => "0",
                "Npl_instansi"           => "0",
                "Sicd"                   => "0",
                "Hasil_prescreening"     => "Diproses lebih lanjut"
            ];
            $insertPrescreening = $this->insertPrescreeningBriguna($content_prescreening);
            \Log::info("-------- masuk insert prescreening ---------");
            \Log::info($insertPrescreening);
            if ($insertPrescreening['statusCode'] == '01') {
                // insert prescoring
                $content_las_prescoring = [
                    "Fid_aplikasi"          => !isset($insertDebitur['items'][0]->ID_APLIKASI)?"":$insertDebitur['items'][0]->ID_APLIKASI,
                    "Fid_cif_las"           => !isset($insertDebitur['items'][0]->CIF_LAS)?"":$insertDebitur['items'][0]->CIF_LAS,
                    "Tgl_perkiraan_pensiun" => !isset($request['Tgl_perkiraan_pensiun'])?"":$request['Tgl_perkiraan_pensiun'],
                    "Sifat_suku_bunga"     => !isset($request['Sifat_suku_bunga'])?"":$request['Sifat_suku_bunga'],
                    "Briguna_profesi"      => !isset($request['Briguna_profesi'])?"":$request['Briguna_profesi'],
                    "Gaji_per_bulan"       => !isset($request['Gaji_per_bulan'])?"":$request['Gaji_per_bulan'],
                    "Pendapatan_profesi"   => !isset($request['Pendapatan_profesi'])?"":$request['Pendapatan_profesi'],
                    "Potongan_per_bulan"   => !isset($request['Potongan_per_bulan'])?"":$request['Potongan_per_bulan'],
                    "Plafond_briguna_existing"  => !isset($request['Plafond_briguna_existing'])?"":$request['Plafond_briguna_existing'],
                    "Angsuran_briguna_existing" => !isset($request['Angsuran_briguna_existing'])?"":$request['Angsuran_briguna_existing'],
                    "Suku_bunga"     => !isset($request['Suku_bunga'])?"":$request['Suku_bunga'],
                    "Jangka_waktu"   => !isset($request['Jangka_waktu'])?"":$request['Jangka_waktu'],
                    "Maksimum_plafond"     => !isset($request['Maksimum_plafond'])?"":$request['Maksimum_plafond'],
                    "Permohonan_kredit"    => !isset($request['Permohonan_kredit'])?"":$request['Permohonan_kredit'],
                    "Baki_debet"           => !isset($request['Baki_debet'])?"":$request['Baki_debet'],
                    "Plafond_usulan"       => !isset($request['Plafond_usulan'])?"":$request['Plafond_usulan'],
                    "Angsuran_usulan"      => !isset($request['Angsuran_usulan'])?"":$request['Angsuran_usulan'],
                    "Rek_simpanan_bri"     => !isset($request['Rek_simpanan_bri'])?"":$request['Rek_simpanan_bri'],
                    "Riwayat_pinjaman"     => !isset($request['Riwayat_pinjaman'])?"":$request['Riwayat_pinjaman'],
                    "Penguasaan_cashflow"  => !isset($request['Penguasaan_cashflow'])?"":$request['Penguasaan_cashflow'],
                    "Payroll"              => !isset($request['pembayaran_gaji'])?"":$request['pembayaran_gaji'],
                    "Gaji_bersih_per_bulan" => !isset($request['Gaji_bersih_per_bulan'])?"":$request['Gaji_bersih_per_bulan'],
                    "Maksimum_angsuran"    => !isset($request['Maksimum_angsuran'])?"":$request['Maksimum_angsuran'],
                    "Tp_produk"            => !isset($request['tp_produk'])?"":$request['tp_produk'],
                    "Angsuran_lainnya"     => "0",                    
                    "Briguna_smart"        => "0",
                    "Kelengkapan_dokumen"  => "1"
                ];
                $insertPrescoring = $this->insertPrescoringBriguna($content_las_prescoring);
                \Log::info("-------- masuk insert prescoring ---------");
                \Log::info($insertPrescoring);
                if ($insertPrescoring['statusCode'] == '01') {
                    $jangka = $request['Jangka_waktu'];
                    $tgl_jatuh_tempo = date('dmY',strtotime('+'.$jangka.' months'));

                    // insert dataKredit
                    $content_insertKreditBriguna = [
                        "Fid_aplikasi"  => !isset($insertDebitur['items'][0]->ID_APLIKASI)?"":$insertDebitur['items'][0]->ID_APLIKASI,
                        "Cif_las"       => !isset($insertDebitur['items'][0]->CIF_LAS)?"":$insertDebitur['items'][0]->CIF_LAS,
                        "Pemrakarsa1"          => $uid,
                        "Uker_pemrakarsa"      => $uker,
                        "Tanggal_jatuh_tempo"  => $tgl_jatuh_tempo,
                        "Tujuan_membuka_rek"   => !isset($request['Tujuan_membuka_rek'])?"":$request['Tujuan_membuka_rek'],
                        "Jangka_waktu"         => !isset($request['Jangka_waktu'])?"":$request['Jangka_waktu'],
                        "Briguna_smart"        => !isset($request['Briguna_smart'])?"":$request['Briguna_smart'],
                        "Kode_fasilitas"       => !isset($request['Kode_fasilitas'])?"":$request['Kode_fasilitas'],
                        "Tujuan_penggunaan_kredit" => !isset($request['Tujuan_penggunaan_kredit'])?"":$request['Tujuan_penggunaan_kredit'],
                        "Penggunaan_kredit"     => !isset($request['Penggunaan_kredit'])?"":$request['Penggunaan_kredit'],
                        "Provisi_kredit"        => !isset($request['Provisi_kredit'])?"":$request['Provisi_kredit'],
                        "Biaya_administrasi"    => !isset($request['Biaya_administrasi'])?"":$request['Biaya_administrasi'],
                        "Penalty"               => !isset($request['Penalty'])?"":$request['Penalty'],
                        "Perusahaan_asuransi"   => !isset($request['Nama_perusahaan_asuransi'])?"":$request['Nama_perusahaan_asuransi'],
                        "Premi_asuransi_jiwa"   => !isset($request['Premi_asuransi_jiwa'])?"":$request['Premi_asuransi_jiwa'],
                        "Premi_beban_bri"       => !isset($request['Premi_beban_bri'])?"":$request['Premi_beban_bri'],
                        "Premi_beban_debitur"   => !isset($request['Premi_beban_debitur'])?"":$request['Premi_beban_debitur'],
                        "Flag_promo"       => !isset($request['promo'])?"":$request['promo'],
                        "Fid_promo"        => !isset($request['nama_program_promo'])?"":$request['nama_program_promo'],
                        "Pengadilan_terdekat"   => !isset($request['Pengadilan_terdekat'])?"":$request['Pengadilan_terdekat'],
                        "Bupln"            => !isset($request['Bupln'])?"":$request['Bupln'],
                        "Agribisnis"       => !isset($request['Agribisnis'])?"":$request['Agribisnis'],
                        "Sandi_stp"        => !isset($request['Sandi_stp'])?"":$request['Sandi_stp'],
                        "Sifat_kredit"     => !isset($request['Sifat_kredit'])?"":$request['Sifat_kredit'],
                        "Jenis_penggunaan" => !isset($request['Jenis_penggunaan'])?"":$request['Jenis_penggunaan'],
                        "Sektor_ekonomi_sid" => !isset($request['Sektor_ekonomi_sid'])?"":$request['Sektor_ekonomi_sid'],
                        "Jenis_kredit_lbu"   => !isset($request['Jenis_kredit_lbu'])?"":$request['Jenis_kredit_lbu'],
                        "Sifat_kredit_lbu"   => !isset($request['Sifat_kredit_lbu'])?"":$request['Sifat_kredit_lbu'],
                        "Kategori_kredit_lbu" => !isset($request['Kategori_kredit_lbu'])?"":$request['Kategori_kredit_lbu'],
                        "Jenis_penggunaan_lbu"=> !isset($request['Jenis_penggunaan_lbu'])?"":$request['Jenis_penggunaan_lbu'],
                        "Sumber_aplikasi"    => !isset($request['Sumber_aplikasi'])?"":$request['Sumber_aplikasi'],
                        "Sektor_ekonomi_lbu" => !isset($request['Sektor_ekonomi_lbu'])?"":$request['Sektor_ekonomi_lbu'],
                        "Maksimum_plafond"  => !isset($request['Maksimum_plafond'])?"":$request['Maksimum_plafond'],
                        "Tp_produk"         => !isset($request['tp_produk'])?"":$request['tp_produk'],
                        "Plafon_induk"      => "0", // hardcode las
                        "Id_kredit"         => "0", // hardcode las
                        "Baru_perpanjangan" => "0", // hardcode las
                        "Jenis_fasilitas"   => "0605", // hardcode las
                        "Sisa_jangka_waktu_sd_penyesuaian"=> "0", // hardcode
                        "Valuta"            => "IDR", // hardcode
                        "Segmen_owner"      => "RITEL", // hardcode
                        "Sub_segmen_owner"  => "RITEL", // hardcode
                        "Kode_jangka_waktu" => "M", // hardcode las
                        "Interest_payment_frequency" => "1", // hardcode las
                        "Sifat_suku_bunga"  => "FIXED", // hardcode
                        "Discount"          => "0", // hardcode las
                        "Golongan_kredit"   => "20", // hardcode las
                        "Orientasi_penggunaan" => "9", // hardcode las
                        "Lokasi_proyek"     => "0591", // hardcode las
                        "Nilai_proyek"      => "0", // hardcode las
                        "Fasilitas_penyedia_dana" => "1999", // hardcode las
                        "Baki_debet"        => "0", // hardcode las
                        "Original_amount"   => "0", // hardcode las
                        "Kelonggaran_tarik" => "0", // hardcode las
                        "Denda"             => "0", // hardcode las
                        "Grace_period"      => "0", // hardcode las
                        "Status_takeover"   => "0", // hardcode las
                        "Bank_asal_takeover" => "", // hardcode las
                        "Data2"             => "" // kosongin aja
                    ];
                    $insertKredit = $this->insertDataKreditBriguna($content_insertKreditBriguna);
                    \Log::info("-------- masuk insert kredit ---------");
                    \Log::info($insertKredit);
                    if ($insertKredit['statusCode'] == '01') {
                        // Hitung CRS
                        $hitung= $this->hitungCRSBrigunaKarya($insertDebitur['items'][0]->ID_APLIKASI);
                        \Log::info("-------- masuk hitungCRS ---------");
                        \Log::info($hitung);
                        if ($hitung['statusCode'] == '01') {
                            $override = 'Y';
                            if ($hitung['items'][0]->cutoff == 'Y') {
                                $override = 'N';
                            }
                            // Kirim Pemutus
                            $conten = [
                                'id_aplikasi'   => $insertDebitur['items'][0]->ID_APLIKASI,
                                'uid'           => $uid,
                                'flag_override' => $override
                            ];
                            $kirim  = $this->kirimPemutus($conten);
                            \Log::info("-------- masuk kirimPemutus ---------");
                            \Log::info($kirim);
                            if ($kirim['statusCode'] != '01') {
                                // $error[0] = $kirim['nama'].' gagal, '.$kirim['statusDesc'];
                                return [
                                    'code' => $kirim['statusCode'], 
                                    'descriptions' => $kirim['nama'].' gagal, '.$kirim['statusDesc'],
                                    // 'contents' => [
                                    //     'data' => $error
                                    // ]
                                ];
                            }

                            $param_briguna = [
                                "uid"                       => $uid, // inquiry user las
                                "uid_pemrakarsa"            => $uker, // inquiry user las
                                "tp_produk"                 => $request['tp_produk'],
                                "id_aplikasi"               => $insertDebitur['items'][0]->ID_APLIKASI,
                                "cif_las"                   => $insertDebitur['items'][0]->CIF_LAS,
                                "Tgl_perkiraan_pensiun"     => $request['Tgl_perkiraan_pensiun'],
                                "Sifat_suku_bunga"          => $request['Sifat_suku_bunga'],
                                "Briguna_profesi"           => $request['Briguna_profesi'],
                                "Pendapatan_profesi"        => $request['Pendapatan_profesi'],
                                "Potongan_per_bulan"        => $request['Potongan_per_bulan'],
                                "Plafond_briguna_existing"  => $request['Plafond_briguna_existing'],
                                "Angsuran_briguna_existing" => $request['Angsuran_briguna_existing'],
                                "Suku_bunga"                => $request['Suku_bunga'],
                                "Jangka_waktu"              => $request['Jangka_waktu'],
                                "Baki_debet"                => $request['Baki_debet'],
                                "Plafond_usulan"            => $request['Plafond_usulan'],
                                "Rek_simpanan_bri"          => $request['Rek_simpanan_bri'],
                                "Riwayat_pinjaman"          => $request['Riwayat_pinjaman'],
                                "Penguasaan_cashflow"       => $request['Penguasaan_cashflow'],
                                "Payroll"                   => $request['pembayaran_gaji'],
                                "Gaji_bersih_per_bulan"     => $request['Gaji_bersih_per_bulan'],
                                "Maksimum_angsuran"         => $request['Maksimum_angsuran'],
                                "Tujuan_membuka_rek"        => $request['Tujuan_membuka_rek'],
                                "Briguna_smart"             => $request['Briguna_smart'],
                                "Kode_fasilitas"            => $request['Kode_fasilitas'],
                                "Tujuan_penggunaan_kredit"  => $request['Tujuan_penggunaan_kredit'],
                                "Penggunaan_kredit"         => $request['Penggunaan_kredit'],
                                "Provisi_kredit"            => $request['Provisi_kredit'],
                                "Biaya_administrasi"        => $request['Biaya_administrasi'],
                                "Penalty"                   => $request['Penalty'],
                                "Perusahaan_asuransi"       => $request['Nama_perusahaan_asuransi'],
                                "Premi_asuransi_jiwa"       => $request['Premi_asuransi_jiwa'],
                                "Premi_beban_bri"           => $request['Premi_beban_bri'],
                                "Premi_beban_debitur"       => $request['Premi_beban_debitur'],
                                "Flag_promo"                => $request['promo'],
                                "Fid_promo"                 => $request['nama_program_promo'],
                                "Pengadilan_terdekat"       => $request['Pengadilan_terdekat'],
                                "Bupln"                     => $request['Bupln'],
                                "Agribisnis"                => $request['Agribisnis'],
                                "Sandi_stp"                 => $request['Sandi_stp'],
                                "Sifat_kredit"              => $request['Sifat_kredit'],
                                "Jenis_penggunaan"          => $request['Jenis_penggunaan'],
                                "Sektor_ekonomi_sid"        => $request['Sektor_ekonomi_sid'],
                                "Jenis_kredit_lbu"          => $request['Jenis_kredit_lbu'],
                                "Sifat_kredit_lbu"          => $request['Sifat_kredit_lbu'],
                                "Kategori_kredit_lbu"       => $request['Kategori_kredit_lbu'],
                                "Jenis_penggunaan_lbu"      => $request['Jenis_penggunaan_lbu'],
                                "Sumber_aplikasi"           => $request['Sumber_aplikasi'],
                                "Sektor_ekonomi_lbu"        => $request['Sektor_ekonomi_lbu'],
                                "id_Status_gelar"           => $request['status_gelar_id'],
                                "Status_gelar"              => $request['status_gelar_name'],
                                "score"                     => $hitung['items'][0]->score,
                                "grade"                     => $hitung['items'][0]->grade,
                                "cutoff"                    => $hitung['items'][0]->cutoff,
                                "definisi"                  => $hitung['items'][0]->definisi,
                                // "NPWP_nasabah"              => $request['NPWP_nasabah'],
                                "NIP"                       => $request['nip'],
                                "Status_Pekerjaan"          => $request['status_pekerjaan'],
                                "Nama_atasan_Langsung"      => empty($request['nama_atasan_langsung'])?"":$request['nama_atasan_langsung'],
                                "Jabatan_atasan"            => empty($request['jabatan_atasan'])?"":$request['jabatan_atasan'],
                                // "KK"                        => $request['KK'],
                                // "SLIP_GAJI"                 => $request['SLIP_GAJI'],
                                // "SK_AWAL"                   => $request['SK_AWAL'],
                                // "SK_AKHIR"                  => $request['SK_AKHIR'],
                                // "REKOMENDASI"               => $request['REKOMENDASI'],
                                // "SKPG"                      => $request['SKPG'],
                                "request_amount"            => $request['Permohonan_kredit'],
                                "mitra_id"                  => $request['mitra_id'],
                                "mitra"                     => $request['mitra_name'],
                                "tujuan_penggunaan_id"      => $request['Penggunaan_kredit'],
                                "jenis_pinjaman_id"         => $request['jenis_pinjaman_id'],
                                "year"                      => $request['Jangka_waktu'],
                                "angsuran_usulan"           => $request['Angsuran_usulan'],
                                "maksimum_plafond"          => $request['Maksimum_plafond'],
                                // baru
                                "no_npwp"                   => $request['no_npwp'],
                                "no_dan_tanggal_sk_awal"    => $request['no_dan_tanggal_sk_awal'],
                                "no_dan_tanggal_sk_akhir"   => $request['no_dan_tanggal_sk_akhir'],
                                "branch_name"               => $request['kantor_cabang_name'],
                                "baru_atau_perpanjang"      => $request['baru_atau_perpanjang'],
                                "total_exposure"            => $request['total_exposure'],
                                // "program_asuransi"          => $request['program_asuransi'],
                                "kredit_take_over"          => $request['kredit_take_over'],
                                "pemrakarsa_name"           => $request['kantor_cabang_name'],
                                "agama"                     => $request['agama'],
                                "npl_instansi"              => $request['npl_instansi'],
                                "npl_unitkerja"             => $request['npl_unitkerja'],
                                "gimmick"                   => $request['nama_program_promo'],
                                "jumlah_pekerja"            => $request['jumlah_pekerja'],
                                "jumlah_debitur"            => $request['jumlah_debitur'],
                                "scoring_mitra"             => $request['scoring_mitra'],
                                "is_send"                   => 0,
                                // baru
                                "usia_mpp"                  => $request['usia_mpp'],
                                "lama_menetap"              => $request['lama_menetap'],
                                "kode_pos"                  => $request['kode_pos'],
                                "kode_pos_dom"              => $request['kode_pos_domisili'],
                                "kelurahan"                 => $request['kelurahan'],
                                "kelurahan_dom"             => $request['kelurahan_domisili'],
                                "kecamatan"                 => $request['kecamatan'],
                                "kecamatan_dom"             => $request['kecamatan_domisili'],
                                "kota"                      => $request['propinsi'],
                                "kota_dom"                  => $request['propinsi_domisili'],
                                "perjanjian_pisah_harta"    => $request['perjanjian_pisah_harta'],
                                "trans_normal_harian"       => $request['transaksi_normal_harian'],
                                "pernah_pinjam"             => $request['pernah_pinjam'],
                                "tgl_mulai_kerja"           => $request['tgl_mulai_bekerja'],
                                "tgl_analisa"               => $request['tgl_analisa'] 
                            ];
                            $eform_id = $request['eform_id'];
                            $param_eform["branch_id"] = $request['kantor_cabang_id'];
                            $briguna  = BRIGUNA::where("eform_id","=",$eform_id);
                            $eform    = EForm::findOrFail($eform_id);

                            //------------hapus file----------------------------------
                            $brigunas = $briguna->get();
                            $npwp = $this->datafoto($request['NPWP_nasabah'],$brigunas[0]['id_foto'],$brigunas[0]['NPWP_nasabah'],'NPWP_nasabah');
                            $kk   = $this->datafoto($request['KK'],$brigunas[0]['id_foto'],$brigunas[0]['KK'],'KK');
                            $gaji = $this->datafoto($request['SLIP_GAJI'],$brigunas[0]['id_foto'],$brigunas[0]['SLIP_GAJI'],'SLIP_GAJI');
                            $skpg = $this->datafoto($request['SKPG'],$brigunas[0]['id_foto'],$brigunas[0]['SKPG'],'SKPG');
                            $sk_awal = $this->datafoto($request['SK_AWAL'],$brigunas[0]['id_foto'],$brigunas[0]['SK_AWAL'],'SK_AWAL');
                            $sk_akhir = $this->datafoto($request['SK_AKHIR'],$brigunas[0]['id_foto'],$brigunas[0]['SK_AKHIR'],'SK_AKHIR');
                            $rekomend = $this->datafoto($request['REKOMENDASI'],$brigunas[0]['id_foto'],$brigunas[0]['REKOMENDASI'],'REKOMENDASI');

                            $param_briguna['NPWP_nasabah'] = $npwp;
                            $param_briguna['KK']           = $kk;
                            $param_briguna['SLIP_GAJI']    = $gaji;
                            $param_briguna['SKPG']         = $skpg;
                            $param_briguna['SK_AWAL']      = $sk_awal;
                            $param_briguna['SK_AKHIR']     = $sk_akhir;
                            $param_briguna['REKOMENDASI']  = $rekomend;
                            \Log::info($param_briguna);
                            // $npwp = substr($request['NPWP_nasabah'], -4);
                            // if ($npwp == '.jpg' || $npwp == '.pdf' || $npwp == 'jpeg') {
                            //     $param_briguna['NPWP_nasabah'] = $request['NPWP_nasabah'];
                            // } else {
                            //     unlink($path.'/'.$brigunas[0]['NPWP_nasabah']);
                            //     $upload_file = $this->updateimage($request['NPWP_nasabah'],$brigunas[0]['id_foto'],'NPWP_nasabah');
                            //     $param_briguna['NPWP_nasabah'] = $upload_file;
                            // }

                            $eform->update($param_eform);
                            $briguna->update($param_briguna);
                            \Log::info("----- analisa update table eforms dan briguna sukses -----");
                            $result = [
                                'code'         => $kirim['statusCode'], 
                                'descriptions' => $kirim['statusDesc'].' '.$kirim['nama'],
                                'contents'     => [
                                    'data' => [
                                        'id_aplikasi' => $insertDebitur['items'][0]->ID_APLIKASI,
                                        'cif_las'     => $insertDebitur['items'][0]->CIF_LAS,
                                        'score'       => $hitung['items'][0]->score,
                                        'grade'       => $hitung['items'][0]->grade,
                                        'cutoff'      => $hitung['items'][0]->cutoff,
                                        'definisi'    => $hitung['items'][0]->definisi
                                    ]
                                ]
                            ];
                            return $result;
                        } else {
                            // $error[0] = $hitung['statusDesc'];
                            return [
                                'code' => $hitung['statusCode'], 
                                'descriptions' => 'hitung '.$hitung['nama'].' gagal, '.$hitung['statusDesc'],
                                // 'contents' => [
                                //     'data' => $error
                                // ]
                            ];
                        }
                    } else {
                        // $error[0]  = $insertKredit['statusDesc'];
                        return [
                            'code' => $insertKredit['statusCode'], 
                            'descriptions' => 'insert '.$insertKredit['nama'].' gagal, '.$insertKredit['statusDesc'],
                            // 'contents' => [
                            //     'data' => $error
                            // ]
                        ];
                    }
                } else {
                    // $error[0] = $insertPrescoring['statusDesc'];
                    return [
                        'code' => $insertPrescoring['statusCode'], 
                        'descriptions' => 'insert '.$insertPrescoring['nama'].' gagal, '.$insertPrescoring['statusDesc'],
                        // 'contents' => [
                        //     'data' => $error
                        // ]
                    ];
                }
            } else {
                // $error[0] = $insertPrescreening['statusDesc'];
                return [
                    'code' => $insertPrescreening['statusCode'], 
                    'descriptions' => 'insert '.$insertPrescreening['nama'].' gagal, '.$insertPrescreening['statusDesc'],
                    // 'contents' => [
                    //     'data' => $error
                    // ]
                ];
            }
        } else {
            // $error[0] = $insertDebitur['statusDesc'];
            return [
                'code' => $insertDebitur['statusCode'], 
                'descriptions' => 'insert '.$insertDebitur['nama'].' gagal, '.$insertDebitur['statusDesc'],
                // 'contents' => [
                //     'data' => $error
                // ]
            ];
        }
    }

    public function show_briguna(Request $request) {
        \Log::info($request->all());
        $limit = $request->input('limit') ? : 10;
        $eform = Apilas::filter($request);
        if (!empty($eform)) {
            // $eform[0]['Url'] = env('APP_URL').'/uploads/'.$eform[0]['user_id'];
            return response()->success( [
                'contents' => $eform
            ],200);
        }

        return response()->error( [
            'contents' => 'data tidak ditemukan'
        ],400);
    }

    public function download(Request $request) {
        \Log::info($request->all());
        $response = $request->all();
        $briguna = Briguna::where('eform_id',$response['eform_id'])->first();
        \Log::info($briguna->toArray());
        if (!empty($briguna)) {
            // $publicPath = public_path('uploads/'.$briguna['id_foto']);
            $publicPath = public_path('uploads/146');
            /*$files = [
                $briguna['KK'], $briguna['NPWP_nasabah'], $briguna['SK_AWAL'],
                $briguna['SK_AKHIR'], $briguna['REKOMENDASI'], $briguna['SKPG']
            ];*/
            $files = [
                '146-20180207141609.jpg', '146-20180207141908.jpeg', '146-20180207143122.jpg',
                '146-20180207142139.png', '146-20180207142448.jpg'
            ];
            \Log::info($files);

            // $zip = Zip::open($publicPath,$image_path);
            $zip = Zip::setPath($publicPath)->add('file');
            $zip = Zip::create($briguna['id_foto'].'-all_image.zip');
            foreach ($files as $file) {
                \Log::info($file);
                $zip = Zip::add($publicPath.'/'.$file);
            }
            $zip->close($zip);
            \Log::info($zip);
            dd('development test');
            // $eform[0]['Url'] = env('APP_URL').'/uploads/'.$eform[0]['user_id'];
            return response()->success( [
                'contents' => $zip
            ],200);
        }

        return response()->error( [
            'contents' => 'data tidak ditemukan'
        ],400);
    }

    public function update_briguna(Request $request) {
        $response = $request->all();
        if (!empty($response)) {
            try {
                $briguna = BRIGUNA::where("eform_id", "=", $response['eform_id']);
                $briguna->update($response);
                $message = [
                    'message' => 'Sukses update briguna',
                    'contents' => $briguna
                ];

                return response()->success($message, 200);
            } catch (Exception $e) {
                return response()->error( [
                    'message' => 'Koneksi Gagal',
                    'contents' => ''
                ], 400 );
            }
        } else {
            return response()->error( [
                    'message' => 'Request tidak ditemukan',
                    'contents' => ''
                ], 400 );
        }
    }

    public function update_foto_briguna(Request $request) {
        $response = $request->all();
        if (!empty($response)) {
            try {
                $image   = $response;
                $data_eforms = EForm::where('id',$response['eform_id'])->first();
                $detail  = CustomerDetail::where('user_id',$data_eforms['user_id'])->first();
                // $this->removeAllImage($detail);
                $id_foto = $data_eforms['id_foto'];
                $filename= $this->uploadimage($image, $response['eform_id'], $id_foto);
                $data_briguna = array_slice($response, 0,3);

                if (isset($image['identity'])) {
                    $data_eform   = ['identity' => $filename];
                    $detail->update($data_eform);
                } else if (isset($image['couple_identity'])) {
                    $data_eform   = ['couple_identity' => $filename];
                    $detail->update($data_eform);
                } else if (isset($image['NPWP_nasabah'])) {
                    $data_briguna['id_foto'] = $id_foto;
                    $data_briguna['NPWP_nasabah'] = $filename;
                } else if (isset($image['SLIP_GAJI'])) {
                    $data_briguna['id_foto']   = $id_foto;
                    $data_briguna['SLIP_GAJI'] = $filename;
                } else if (isset($image['KK'])) {
                    $data_briguna['id_foto'] = $id_foto;
                    $data_briguna['KK'] = $filename;
                } else if (isset($image['SK_AWAL'])) {
                    $data_briguna['id_foto'] = $id_foto;
                    $data_briguna['SK_AWAL'] = $filename;
                } else if (isset($image['SK_AKHIR'])) {
                    $data_briguna['id_foto']  = $id_foto;
                    $data_briguna['SK_AKHIR'] = $filename;
                } else if (isset($image['REKOMENDASI'])) {
                    $data_briguna['id_foto']  = $id_foto;
                    $data_briguna['REKOMENDASI'] = $filename;
                } else if (isset($image['SKPG'])) {
                    $data_briguna['id_foto']   = $id_foto;
                    $data_briguna['SKPG'] = $filename;
                }
                \Log::info($data_briguna);

                $briguna = BRIGUNA::where("eform_id", "=", $response['eform_id']);
                $briguna->update($data_briguna);
                $message = [
                    'message' => 'Sukses update eforms atau briguna',
                    'contents' => $briguna
                ];
                return response()->success($message, 200);
            } catch (Exception $e) {
                return response()->error( [
                    'message' => 'Koneksi Gagal',
                    'contents' => ''
                ], 400 );
            }
        } else {
            return response()->error( [
                    'message' => 'Request tidak ditemukan',
                    'contents' => ''
                ], 400 );
        }
    }

    function loginLAS($params) {
        $pn['PN'] = $params;
        $result = false;
        try {
            $client = $this->client();
            $resultclient = $client->inquiryUserLAS($pn);
            if($resultclient->inquiryUserLASResult){
                $datadetail = json_decode($resultclient->inquiryUserLASResult);
                if(isset($datadetail->statusCode) && $datadetail->statusCode=='01'){
                    if(isset($datadetail->items)){
                        $result = $this->return_conten($datadetail);
                        return $result;
                    }
                }
                $result = $this->return_conten($datadetail);
                return $result;
            }
            $error[0] = 'Hasil Inquiry Kosong / Anda belum memiliki user LAS';
            return [
                'code' => 04, 
                'descriptions' => 'Hasil Inquiry Kosong / Anda belum memiliki user LAS',
                'contents' => [
                    'data' => $error
                ]
            ];
        }
        catch(SoapFault $f){
            $error[0] = 'Gagal Koneksi Jaringan';
            return [
                'code' => 04, 
                'descriptions' => 'Gagal Koneksi Jaringan',
                'contents' => [
                    'data' => $error
                ]
            ];
        }        
    }

    function kirimPemutus($params) {
        try {
            $parameter = [
                'id_aplikasi'   => !isset($params['id_aplikasi']) ? "" : $params['id_aplikasi'],
                'uid'           => !isset($params['uid']) ? "" : $params['uid'],
                'flag_override' => !isset($params['flag_override'])? "" : $params['flag_override']
            ];
            // save json_ws_log
            $data_log = [
                'json_data' => json_encode($parameter),
                'function_name' => 'kirimPemutus',
                'created_at'=> date('Y-m-d H:i:s')
            ];
            $save = \DB::table('json_ws_log')->insert($data_log);
            \Log::info('berhasil save kirimPemutus json_ws_log'.$save);

            $client = $this->client();
            $resultclient = $client->kirimPemutus($parameter);
            // print_r($resultclient);exit();
            if($resultclient->kirimPemutusResult){
                $datadetail = json_decode($resultclient->kirimPemutusResult);
                $dataResult = (array) $datadetail;
                return $dataResult;
            }
            $error[0] = 'Gagal Koneksi DB';
            return [
                'statusCode' => 04, 
                'statusDesc' => 'Gagal Koneksi DB',
                'items' => [
                    'data' => $error
                ]
            ];
        }
        catch(SoapFault $f){
            $error[0] = 'Gagal Koneksi Jaringan';
            return [
                'statusCode' => 05, 
                'statusDesc' => 'Gagal Koneksi Jaringan',
                'items' => [
                    'data' => $error
                ]
            ];
        }
    }

    function hitungCRSBrigunaKarya($params) {
        try {
            $parameter['id_Aplikasi'] = $params;
            // save json_ws_log
            $data_log = [
                'json_data' => $parameter['id_Aplikasi'],
                'function_name' => 'hitungCRS',
                'created_at'=> date('Y-m-d H:i:s')
            ];
            $save = \DB::table('json_ws_log')->insert($data_log);
            \Log::info('berhasil save hitungCRS json_ws_log'.$save);
            $client = $this->client();
            $resultclient = $client->hitungCRSBrigunaKarya($parameter);

            if($resultclient->hitungCRSBrigunaKaryaResult){
                $datadetail = json_decode($resultclient->hitungCRSBrigunaKaryaResult);
                $dataResult = (array) $datadetail;
                return $dataResult;
            }
            $error[0] = 'Gagal Koneksi DB';
            return [
                'statusCode' => 04, 
                'statusDesc' => 'Gagal Koneksi DB',
                'items' => [
                    'data' => $error
                ]
            ];
        }
        catch(SoapFault $f){
            $error[0] = 'Gagal Koneksi Jaringan';
            return [
                'statusCode' => 05, 
                'statusDesc' => 'Gagal Koneksi Jaringan',
                'items' => [
                    'data' => $error
                ]
            ];
        }
    }
    
    function insertDataKreditBriguna($params) {
        try {
            $parameter['JSON'] = json_encode($params);
            // save json_ws_log
            $data_log = [
                'json_data' => $parameter['JSON'],
                'function_name' => 'insertDataKreditBriguna',
                'created_at'=> date('Y-m-d H:i:s')
            ];
            $save = \DB::table('json_ws_log')->insert($data_log);
            \Log::info('berhasil save insertDataKreditBriguna json_ws_log'.$save);
            $client = $this->client();
            $resultclient = $client->insertDataKreditBriguna($parameter);

            if($resultclient->insertDataKreditBrigunaResult){
                $datadetail = json_decode($resultclient->insertDataKreditBrigunaResult);
                $dataResult = (array) $datadetail;
                return $dataResult;
            }
            $error[0] = 'Gagal Koneksi DB';
            return [
                'statusCode' => 04, 
                'statusDesc' => 'Gagal Koneksi DB',
                'items' => [
                    'data' => $error
                ]
            ];
        }
        catch(SoapFault $f){
            $error[0] = 'Gagal Koneksi Jaringan';
            return [
                'statusCode' => 05, 
                'statusDesc' => 'Gagal Koneksi Jaringan',
                'items' => [
                    'data' => $error
                ]
            ];
        }
    }

    function insertPrescoringBriguna($params) {
        try {
            $parameter['JSON'] = json_encode($params);
            // save json_ws_log
            $data_log = [
                'json_data' => $parameter['JSON'],
                'function_name' => 'insertPrescoringBriguna',
                'created_at'=> date('Y-m-d H:i:s')
            ];
            $save = \DB::table('json_ws_log')->insert($data_log);
            \Log::info('berhasil save insertPrescoringBriguna json_ws_log'.$save);
            $client = $this->client();
            $resultclient = $client->insertPrescoringBriguna($parameter);

            if($resultclient->insertPrescoringBrigunaResult){
                $datadetail = json_decode($resultclient->insertPrescoringBrigunaResult);
                $dataResult = (array) $datadetail;
                return $dataResult;
            }
            $error[0] = 'Gagal Koneksi DB';
            return [
                'statusCode' => 04, 
                'statusDesc' => 'Gagal Koneksi DB',
                'items' => [
                    'data' => $error
                ]
            ];
        }
        catch(SoapFault $f){
            $error[0] = 'Gagal Koneksi Jaringan';
            return [
                'statusCode' => 05, 
                'statusDesc' => 'Gagal Koneksi Jaringan',
                'items' => [
                    'data' => $error
                ]
            ];
        }
    }

    function insertPrescreeningBriguna($params) {
        try {
            $parameter['JSON'] = json_encode($params);
            // save json_ws_log
            $data_log = [
                'json_data' => $parameter['JSON'],
                'function_name' => 'insertPrescreeningBriguna',
                'created_at'=> date('Y-m-d H:i:s')
            ];
            $save = \DB::table('json_ws_log')->insert($data_log);
            \Log::info('berhasil save insertPrescreeningBriguna json_ws_log'.$save);
            $client = $this->client();
            $resultclient = $client->insertPrescreeningBriguna($parameter);

            if($resultclient->insertPrescreeningBrigunaResult){
                $datadetail = json_decode($resultclient->insertPrescreeningBrigunaResult);
                $dataResult = (array) $datadetail;
                return $dataResult;
            }
            $error[0] = 'Gagal Koneksi DB';
            return [
                'statusCode' => 04, 
                'statusDesc' => 'Gagal Koneksi DB',
                'items' => [
                    'data' => $error
                ]
            ];
        }
        catch(SoapFault $f){
            $error[0] = 'Gagal Koneksi Jaringan';
            return [
                'statusCode' => 05, 
                'statusDesc' => 'Gagal Koneksi Jaringan',
                'items' => [
                    'data' => $error
                ]
            ];
        }
    }

    function insertDataDebtPerorangan($params) {
        try {
            $parameter['JSONData'] = json_encode($params);
            $parameter['flag_sp']  = 1;
            // save json_ws_log
            $data_log = [
                'json_data' => $parameter['JSONData'],
                'function_name' => 'insertDataDebtPerorangan',
                'created_at'=> date('Y-m-d H:i:s')
            ];
            $save = \DB::table('json_ws_log')->insert($data_log);
            \Log::info('berhasil save insertDataDebtPerorangan json_ws_log'.$save);
            $client = $this->client();
            $resultclient = $client->insertDataDebtPerorangan($parameter);

            if($resultclient->insertDataDebtPeroranganResult){
                $datadetail = json_decode($resultclient->insertDataDebtPeroranganResult);
                $dataResult = (array) $datadetail;
                return $dataResult;
            }
            $error[0] = 'Gagal Koneksi DB';
            return [
                'statusCode' => 04, 
                'statusDesc' => 'Gagal Koneksi DB',
                'items' => [
                    'data' => $error
                ]
            ];
        }
        catch(SoapFault $f){
            $error[0] = 'Gagal Koneksi Jaringan';
            return [
                'statusCode' => 05, 
                'statusDesc' => 'Gagal Koneksi Jaringan',
                'items' => [
                    'data' => $error
                ]
            ];
        }
    }

    function datafoto($request, $id_foto, $exist_field, $field){
        $path = public_path( 'uploads/' . $id_foto . '/' );
        $image = substr($request, -4);
        if ($image == '.jpg' || $image == '.pdf' || $image == 'jpeg' || $image == '.png' || $image == '.gif') {
            $params = $request;
        } else if (empty($request)) {
            if (!empty($exist_field)) {
                unlink($path.'/'.$exist_field);
            }
            $params = $request;
        } else {
            if (!empty($exist_field)) {
                unlink($path.'/'.$exist_field);
            }
            
            $upload_file = $this->updateimage($request,$id_foto,$field);
            $params = $upload_file;
        }
        return $params;
    }

    function uploadimage($image, $id, $id_foto) {
        $eform = EForm::where('id', $id)->first();
        if (isset($image['identity']) || isset($image['couple_identity'])) {
            $path  = public_path('uploads/'.$eform->nik.'/');
        } else {
            $path  = public_path('uploads/'.$id_foto.'/');
        }
        $data_image = $image['uploadfoto'];
        $filename = null;
        if ($data_image) {
            if (!$data_image->getClientOriginalExtension()) {
                if ($data_image->getMimeType() == '.pdf') {
                    $extension = 'pdf';
                }elseif($data_image->getMimeType() == '.jpg'||$data_image->getMimeType() == '.jpeg'){
                    $extension = 'jpg';
                }else{
                    $extension = 'png';
                }
            }else{
                $extension = $data_image->getClientOriginalExtension();
            }
            
            $filename = $eform->user_id.'-'.$id_foto.'.'.$extension;
            $data_image->move( $path, $filename );
        }
        return $filename;
    }

    function updateimage($image, $id, $atribute) {
        $path = public_path( 'uploads/' . $id . '/' );
        $filename = null;
        if ($image) {
            if (!$image->getClientOriginalExtension()) {
                if ($image->getMimeType() == '.pdf') {
                    $extension = 'pdf';
                }elseif($image->getMimeType() == '.jpg' || $image->getMimeType() == '.jpeg'){
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

    function removeAllImage($eform) {
        $path = public_path('uploads/'.$eform->nik.'/');
        foreach (explode(',', $eform->uploadfoto) as $image) {
            if ( ! empty( $image ) ) {
                File::delete( $path . $image );
            }
        }
    }
}