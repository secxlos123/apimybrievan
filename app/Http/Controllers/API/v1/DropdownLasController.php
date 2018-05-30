<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BRIGUNA;
use Artisaninweb\SoapWrapper\SoapWrapper;

class DropdownLasController extends Controller
{
    protected $soapWrapper;

    public function __construct(SoapWrapper $soapWrapper) {
        $this->soapWrapper = $soapWrapper;      
    }

    function client() {
        $url = config('restapi.asmx_las');
        return new \SoapClient($url);
    }

    public function list_dropdown (Request $request) {
    	try {
            $response = $request->all();
	        $list_bank  = $this->inquiryBank();
	        $list_gelar = $this->inquiryGelar();
            $list_jabatan = $this->inquiryJabatan();
            $list_loan_type = $this->inquiryLoantype();
            $list_pekerjaan = $this->inquiryPekerjaan();
            $list_hub_bank  = $this->inquiryHubunganBank();
            $list_sifat_kredit = $this->inquirySifatKredit();
            $list_bidang_usaha = $this->inquiryBidangUsaha();
            $list_promo_briguna = $this->inquiryPromoBriguna();
            $list_jenis_pekerjaan = $this->inquiryJenisPekerjaan();
            $list_sifat_kredit_lbu = $this->inquirySifatKreditLBU();
            $list_jenis_kredit_lbu = $this->inquiryJenisKreditLBU();
            $list_jenis_penggunaan = $this->inquiryJenisPenggunaan();
            $list_tujuan_penggunaan = $this->inquiryTujuanPenggunaan();
            $list_sektor_ekonomi_lbu = $this->inquirySektorEkonomiLBU();
            $list_jenis_penggunaan_lbu = $this->inquiryJenisPenggunaanLBU();
	        
	        // Route::post('list_dropdown_las', 'DropdownLasController@list_dropdown');
	        $data_dropdown = [
                'code'         => 01,
                'descriptions' => 'Berhasil',
                'contents' => [
                    'data' => [
                    	'ListBank'  => $list_bank['contents']['data'],
                    	'ListGelar' => $list_gelar['contents']['data'],
                        'ListJabatan' => $list_jabatan['contents']['data'],
                    	'ListLoanType' => $list_loan_type['contents']['data'],
                    	'ListHubBank'  => $list_hub_bank['contents']['data'],
                    	'ListPekerjaan'  => $list_pekerjaan['contents']['data'],
                        'ListSifatKredit' => $list_sifat_kredit['contents']['data'],
                        'ListBidangUsaha' => $list_bidang_usaha['contents']['data'],
                        'ListPromoBriguna' => $list_promo_briguna['contents']['data'],
                        'ListJenisPekerjaan' => $list_jenis_pekerjaan['contents']['data'],
                    	'ListSifatKreditLBU' => $list_sifat_kredit_lbu['contents']['data'],
                    	'ListJenisKreditLBU' => $list_jenis_kredit_lbu['contents']['data'],
                    	'ListJenisPenggunaan' => $list_jenis_penggunaan['contents']['data'],
                    	'ListTujuanPenggunaan' => $list_tujuan_penggunaan['contents']['data'],
                    	'ListSektorEkonomiLBU' => $list_sektor_ekonomi_lbu['contents']['data'],
                    	'ListJenisPenggunaanLBU' => $list_jenis_penggunaan_lbu['contents']['data'],
                    ]
                ]
            ];
            return $data_dropdown;
            // print_r($data_dropdown);exit();
	        // return [
	        //     'code' => 04, 
	        //     'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong'
	        // ];
	    } catch(SoapFault $f) {
	        return [
	            'code' => 05, 
	            'descriptions' => 'Gagal Koneksi Jaringan'
	        ];
	    }
    }

    function inquirySifatKredit() {
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
	        return [
	            'code' => 04, 
	            'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong'
	        ];
	    } catch(SoapFault $f) {
	        return [
	            'code' => 05, 
	            'descriptions' => 'Gagal Koneksi Jaringan'
	        ];
	    }
    }

    function inquiryGelar() {
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
            return [
                'code' => 04, 
                'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong'
            ];
        } catch(SoapFault $f) {
            return [
                'code' => 05, 
                'descriptions' => 'Gagal Koneksi Jaringan'
            ];
        }
    }

    function inquiryLoantype() {
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
            return [
                'code' => 04, 
                'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong'
            ];
        } catch(SoapFault $f) {
            return [
                'code' => 05, 
                'descriptions' => 'Gagal Koneksi Jaringan'
            ];
        }
    }

    function inquiryJenisPenggunaan() {
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
            return [
                'code' => 04, 
                'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong'
            ];
        } catch(SoapFault $f) {
            $error[0] = 'Gagal Koneksi Jaringan';
            return [
                'code' => 05, 
                'descriptions' => 'Gagal Koneksi Jaringan'
            ];
        }
    }

    function inquiryJenisPenggunaanLBU() {
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
            return [
                'code' => 04, 
                'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong'
            ];
        } catch(SoapFault $f) {
            return [
                'code' => 05, 
                'descriptions' => 'Gagal Koneksi Jaringan'
            ];
        }
    }

    function inquirySektorEkonomiLBU() {
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
            return [
                'code' => 04, 
                'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong'
            ];
        } catch(SoapFault $f) {
            return [
                'code' => 05, 
                'descriptions' => 'Gagal Koneksi Jaringan'
            ];
        }
    }

    function inquirySifatKreditLBU() {
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
            return [
                'code' => 04, 
                'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong'
            ];
        } catch(SoapFault $f) {
            return [
                'code' => 05, 
                'descriptions' => 'Gagal Koneksi Jaringan'
            ];
        }
    }

    function inquiryJenisKreditLBU() {
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
            return [
                'code' => 04, 
                'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong'
            ];
        } catch(SoapFault $f) {
            return [
                'code' => 05, 
                'descriptions' => 'Gagal Koneksi Jaringan'
            ];
        }
    }

    function inquiryPromoBriguna() {
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
            return [
                'code' => 04, 
                'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong'
            ];
        } catch(SoapFault $f) {
            return [
                'code' => 05, 
                'descriptions' => 'Gagal Koneksi Jaringan'
            ];
        }
    }

    function inquiryTujuanPenggunaan() {
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
            return [
                'code' => 04, 
                'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong'
            ];
        } catch(SoapFault $f) {
            return [
                'code' => 05, 
                'descriptions' => 'Gagal Koneksi Jaringan'
            ];
        }
    }

    function inquiryBidangUsaha() {
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
            return [
                'code' => 04, 
                'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong'
            ];
        } catch(SoapFault $f) {
            return [
                'code' => 05, 
                'descriptions' => 'Gagal Koneksi Jaringan'
            ];
        }
    }

    function inquiryBank() {
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
            return [
                'code' => 04, 
                'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong'
            ];
        } catch(SoapFault $f) {
            return [
                'code' => 05, 
                'descriptions' => 'Gagal Koneksi Jaringan'
            ];
        }
    }

    function inquiryHubunganBank() {
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
	        return [
	            'code' => 04, 
	            'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong'
	        ];
	    } catch(SoapFault $f) {
	        return [
	            'code' => 05, 
	            'descriptions' => 'Gagal Koneksi Jaringan'
	        ];
	    }
    }

    function inquiryPekerjaan() {
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
            return [
                'code' => 04, 
                'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong'
            ];
        } catch(SoapFault $f) {
            return [
                'code' => 05, 
                'descriptions' => 'Gagal Koneksi Jaringan'
            ];
        }
    }

    function inquiryJabatan() {
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
            return [
                'code' => 04, 
                'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong'
            ];
        } catch(SoapFault $f) {
            return [
                'code' => 05, 
                'descriptions' => 'Gagal Koneksi Jaringan'
            ];
        }
    }

    function inquiryJenisPekerjaan() {
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
            return [
                'code' => 04, 
                'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong'
            ];
        } catch(SoapFault $f) {
            return [
                'code' => 05, 
                'descriptions' => 'Gagal Koneksi Jaringan'
            ];
        }
    }

    function inquiryKodePos($data) {
        try {
            $data_pos = Asmx::setEndpoint('GetDataKodePosBriguna')
                    ->setQuery([
                        'search' => $data['search'],
                        'limit'  => $data['limit'],
                        'page'   => $data['page'],
                        'sort'   => $data['sort']
                    ])
                    ->post();
            return $data_pos;
        } catch (Exception $e) {
            return [
                'code' => 05, 
                'descriptions' => 'Gagal Koneksi Jaringan'
            ];
        }
    }

    function return_conten($respons) {
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
                'descriptions' => 'Gagal Koneksi Jaringan'
            ];
        }
    }
}