<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class ContentMYBRIController extends Controller
{
    function client() {
        $url = config('restapi.cmsmybri');
        return $url;
    }

    public function index() {
    	try {
            $client = new Client();
            $host = $this->client();
            $product = $client->request('GET',$host.'/api/v1/products');
            $listProduct = json_decode($product->getBody()->getContents(), true);
            // print_r($listProduct);exit();
	        
	        if ($listProduct['code'] == '200') {
                return $listProduct;
            } else {
    	        return [
    	            'code' => 04, 
    	            'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong'
    	        ];
            }
	    } catch(Exception $f) {
	        return [
	            'code' => 05, 
	            'descriptions' => 'Gagal Koneksi Jaringan'
	        ];
	    }
    }

    public function detail(Request $request) {
        try {
            $respons = $request->all();
            \Log::info($respons);
            // print_r($respons);exit();
            $client = new Client();
            $host = $this->client();
            $product = $client->request('GET',$host.'/api/v1/products/'.$respons['id']);
            $detailProduct = json_decode($product->getBody()->getContents(), true);
            // print_r($listProduct);exit();
            
            if ($detailProduct['code'] == '200') {
                return $detailProduct;
            } else {
                return [
                    'code' => 04, 
                    'descriptions' => 'Gagal Koneksi DB / Hasil Inquiry Kosong'
                ];
            }
        } catch(Exception $f) {
            return [
                'code' => 05, 
                'descriptions' => 'Gagal Koneksi Jaringan'
            ];
        }
    }
}