<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Dropbox;
use Illuminate\Http\Request;

class DropboxController extends Controller
{
    public function index(Request $request) {
    	print_r($request->all());exit();
    	$Dropbox = new Dropbox();
        $pn_user = substr('00000000'. request()->header('pn'), -8 );
    	$respons = $request->all();

        if (strtolower($respons['type']) == 'ktp' || strtolower($respons['type']) == 'ktp_pasangan') {
            $type = 'prakarsa';
        } else if (strtolower($respons['type']) == 'debitur') {
            $type = 'dokumentasi_kredit_debitur';
        } else if (strtolower($respons['type']) == 'debitur_pasangan') {
            $type = 'dokumentasi_kredit_pasangan';
        } else if (strtolower($respons['type']) == 'npwp') {
            $type = 'dokumentasi_kredit_npwp';
        } else if (strtolower($respons['type']) == 'kk') {
            $type = 'dokumentasi_kredit_kk';
        } else {
            $type = 'dokumentasi_kredit_lain';
        }

        $client = new Client();
        $upload = $client->request('POST', 'http://10.35.65.111/skpp_concept/upload_dokumentasi',
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'form_params' =>
                [
                    "files" => $response['file'], //file
                    "user"  => $pn_user, //8 digit pn pekerja
                    "ktp"   => $response['ktp'], //noktp debitur
                    "refno" => $response['ref_number'], // no ref dari dropbox
                    "tipe"  => $type, //$request->input('limit'),
                    "title" => $response['title'] //nama file image bersama ekstensi nya
                ]
            ]
        );
        $result = json_decode($upload->getBody()->getContents(), true);
        // dd($result);
        if ($result['responseCode'] == 01) {
            return response()->success([
                'message' => 'Sukses',
                'contents' => $result['responseData']
            ]);
        } else {
            return response()->success([
                'message' => 'Gagal',
                'contents' => $result['responseDesc']
            ]);
        }

    	/*switch ($method) {
    		case 'insertSkpp':
    			$postData = [
		            'requestMethod' => $method,
		            'requestData'   => json_encode([
		                'branch'  	=> $data,
		                'appname' 	=> 'MBR',
		                'jenis'   	=> 'BG',
		                'expdate' 	=> date('Y-m-d'),
		                'content' 	=> $data,
		                'status'  	=> '1'
		            ])
		        ];

		        $data_dropbox = $Dropbox->insertDropbox($postData);
    			break;

    		default:
    			# code...
    			break;
    	}*/
    }
}
