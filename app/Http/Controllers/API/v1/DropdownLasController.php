<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BRIGUNA;
use App\Models\Mitra4;
use App\Models\Jenispinjaman;
use App\Models\Tujuanpenggunaan;
use App\Models\Pendidikan_terakhir;
use Artisaninweb\SoapWrapper\SoapWrapper;
use DB;
use Asmx;

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
            if ($list_bank['code'] == '02') {
                $data_value['data1']['ListBank'] = $list_bank['contents']['data'];
            } else {
                $data_value['data1']['ListBank'] = [];
            }

            $list_gelar = $this->inquiryGelar();
            if ($list_gelar['code'] == '02') {
                $data_value['data2']['ListGelar'] = $list_gelar['contents']['data'];
            } else {
                $data_value['data2']['ListGelar'] = [];
            }

            $list_jabatan = $this->inquiryJabatan();
            if ($list_jabatan['code'] == '02') {
                $data_value['data3']['ListJabatan'] = $list_jabatan['contents']['data'];
            } else {
                $data_value['data3']['ListJabatan'] = [];
            }

            $list_loan_type = $this->inquiryLoantype();
            if ($list_loan_type['code'] == '02') {
                $data_value['data4']['ListLoanType'] = $list_loan_type['contents']['data'];
            } else {
                $data_value['data4']['ListLoanType'] = [];
            }

            $list_pekerjaan = $this->inquiryPekerjaan();
            if ($list_pekerjaan['code'] == '02') {
                $data_value['data5']['ListPekerjaan'] = $list_pekerjaan['contents']['data'];
            } else {
                $data_value['data5']['ListPekerjaan'] = [];
            }

            $list_hub_bank  = $this->inquiryHubunganBank();
            if ($list_hub_bank['code'] == '02') {
                $data_value['data6']['ListHubBank'] = $list_hub_bank['contents']['data'];
            } else {
                $data_value['data6']['ListHubBank'] = [];
            }

            $list_sifat_kredit = $this->inquirySifatKredit();
            if ($list_sifat_kredit['code'] == '02') {
                $data_value['data7']['ListSifatKredit'] = $list_sifat_kredit['contents']['data'];
            } else {
                $data_value['data7']['ListSifatKredit'] = [];
            }

            $list_bidang_usaha = $this->inquiryBidangUsaha();
            if ($list_bidang_usaha['code'] == '02') {
                $data_value['data8']['ListBidangUsaha'] = $list_bidang_usaha['contents']['data'];
            } else {
                $data_value['data8']['ListBidangUsaha'] = [];
            }

            $list_promo_briguna = $this->inquiryPromoBriguna();
            if ($list_promo_briguna['code'] == '02') {
                $data_value['data9']['ListPromoBriguna'] = $list_promo_briguna['contents']['data'];
            } else {
                $data_value['data9']['ListPromoBriguna'] = [];
            }

            $list_jenis_pekerjaan = $this->inquiryJenisPekerjaan();
            if ($list_jenis_pekerjaan['code'] == '02') {
                $data_value['data10']['ListJenisPekerjaan'] = $list_jenis_pekerjaan['contents']['data'];
            } else {
                $data_value['data10']['ListJenisPekerjaan'] = [];
            }

            $list_sifat_kredit_lbu = $this->inquirySifatKreditLBU();
            if ($list_sifat_kredit_lbu['code'] == '02') {
                $data_value['data11']['ListSifatKreditLBU'] = $list_sifat_kredit_lbu['contents']['data'];
            } else {
                $data_value['data11']['ListSifatKreditLBU'] = [];
            }
            
            $list_jenis_kredit_lbu = $this->inquiryJenisKreditLBU();
            if ($list_jenis_kredit_lbu['code'] == '02') {
                $data_value['data12']['ListJenisKreditLBU'] = $list_jenis_kredit_lbu['contents']['data'];
            } else {
                $data_value['data12']['ListJenisKreditLBU'] = [];
            }

            $list_jenis_penggunaan = $this->inquiryJenisPenggunaan();
            if ($list_jenis_penggunaan['code'] == '02') {
                $data_value['data13']['ListJenisPenggunaan'] = $list_jenis_penggunaan['contents']['data'];
            } else {
                $data_value['data13']['ListJenisPenggunaan'] = [];
            }

            $list_tujuan_penggunaan = $this->inquiryTujuanPenggunaan();
            if ($list_tujuan_penggunaan['code'] == '02') {
                $data_value['data14']['ListTujuanPenggunaanLAS'] = $list_tujuan_penggunaan['contents']['data'];
            } else {
                $data_value['data14']['ListTujuanPenggunaanLAS'] = [];
            }

            $list_sektor_ekonomi_lbu = $this->inquirySektorEkonomiLBU();
            if ($list_sektor_ekonomi_lbu['code'] == '02') {
                $data_value['data15']['ListSektorEkonomiLBU'] = $list_sektor_ekonomi_lbu['contents']['data'];
            } else {
                $data_value['data15']['ListSektorEkonomiLBU'] = [];
            }

            $list_jenis_penggunaan_lbu = $this->inquiryJenisPenggunaanLBU();
            if ($list_jenis_penggunaan_lbu['code'] == '02') {
                $data_value['data16']['ListJenisPenggunaanLBU'] = $list_jenis_penggunaan_lbu['contents']['data'];
            } else {
                $data_value['data16']['ListJenisPenggunaanLBU'] = [];
            }
            
            // Get Mitra
            $limit = $request->input('limit') ?: 15000;
            $mitra = Mitra4::filter($request)->paginate($limit);
            $data_value['data17']['ListMitra'] = [];
            // Get KodePos
            $kodepos = $this->inquiryKodePos();
            if ($kodepos['code'] == '201') {
                $data_value['data18']['ListKodePos'] = $kodepos['contents']['data'];
            } else {
                $data_value['data18']['ListKodePos'] = [];
            }
            $jenis_pinjaman = $this->select();
            $data_value['data19']['ListJenisPinjaman'] = $jenis_pinjaman['JenisPinjaman'];
            $data_value['data20']['ListTujuanPenggunaan'] = $jenis_pinjaman['TujuanPenggunaan'];

            return response()->success([
                'contents' => $data_value,
                'message' => 'Sukses'
            ]);
            // print_r($mitra);exit();
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

    function select() {
        try {
            $jpinjaman = DB::table('jenis_pinjaman')->select('*')->get();
            $dpinjaman = $jpinjaman->toArray();
            if (!empty($dpinjaman)) {
                $data_pinjaman = $dpinjaman;
            } else {
                $data_pinjaman = [];
            }

            $tpenggunaan = DB::table('tujuan_penggunaan')->select('*')->get();
            $dpenggunaan = $tpenggunaan->toArray();
            if (!empty($dpenggunaan)) {
                $data_penggunaan = $dpenggunaan;
            } else {
                $data_penggunaan = [];
            }

            $pendidikan_terakhir = DB::table('pendidikan_terakhir')->select('*')->get();
            $dpendidikan = $pendidikan_terakhir->toArray();
            if (!empty($dpendidikan)) {
                $data_pendidikan = $dpendidikan;
            } else {
                $data_pendidikan = [];
            }

            return [
                'JenisPinjaman' => $data_pinjaman,
                'TujuanPenggunaan' => $data_penggunaan,
                'PendidikanTerakhir' => $data_pendidikan
            ];
        } catch (Exception $e) {
            return [
                'code' => 05, 
                'descriptions' => 'Gagal Koneksi Jaringan'
            ];
        }
    }

    function inquiryKodePos() {
        try {
            $data_pos = Asmx::setEndpoint('GetDataKodePosBriguna')
            ->setQuery([
                'search' => '1',
                'limit'  => '12000',
                'page'   => '',
                'sort'   => ''
            ])->post();
            return $data_pos;
        } catch (Exception $e) {
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