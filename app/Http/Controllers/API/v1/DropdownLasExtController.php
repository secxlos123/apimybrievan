<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BRIGUNA;
use App\Models\Mitra4;
use Artisaninweb\SoapWrapper\SoapWrapper;
use DB;
use Asmx;

class DropdownLasExtController extends Controller
{
	public function __construct(SoapWrapper $soapWrapper) {
        $this->soapWrapper = $soapWrapper;      
    }

    function client() {
        $url = config('restapi.asmx_las');
        return new \SoapClient($url);
    }

    public function dropdown_las_ext (Request $request) {
        try {
            $response = $request->all();

            $list_gelar = $this->inquiryGelar();
            if ($list_gelar['code'] == '01') {
                $data_value[]['data1']['ListGelar'] = $list_gelar['contents']['data'];
            } else {
                $data_value[]['data1']['ListGelar'] = [];
            }

            $list_tujuan_penggunaan = $this->inquiryTujuanPenggunaan();
            if ($list_tujuan_penggunaan['code'] == '01') {
                $data_value[]['data2']['ListTujuanPenggunaanLAS'] = $list_tujuan_penggunaan['contents']['data'];
            } else {
                $data_value[]['data2']['ListTujuanPenggunaanLAS'] = [];
            }
            
            // Get Mitra
            $limit = $request->input('limit') ?: 15000;
            $mitra = Mitra4::filter($request)->paginate($limit);
            $data_value[]['data3'] = $mitra;

            $jenis_pinjaman = $this->select();
            $data_value[]['data4']['ListJenisPinjaman'] = $jenis_pinjaman['JenisPinjaman'];
            $data_value[]['data5']['ListTujuanPenggunaan'] = $jenis_pinjaman['TujuanPenggunaan'];

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
