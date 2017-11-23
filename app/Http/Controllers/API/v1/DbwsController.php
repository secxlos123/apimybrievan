<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class DbwsController extends Controller
{
   
   public function getimage(Request $request)
    {
        $data = DB::table('customer_view_table')->where('nik', '=', $request->nik)->first();

        if (count($data) > 0) {
        $image=array();
        $userId = $data->user_id;
        $eformId = $data->eforms_id;
        foreach ($data as $key => $value) {
        			if ( $key != 'user_id' && $key != 'eforms_id' && $key != 'nik' ) {
        				if ( !empty($value) ) {
		        			if ($key == 'identity' || $key == 'couple_identity') {
		        				$image[]['name'] = \Storage::disk('users')->url($userId.'/'.$value);
		        				continue;
		        			}
		        			$image[]['name'] = \Storage::disk('eforms')->url($eformId.'/visit_report/'.$value);
        				}
        			}
        		}
            return response()->json([
            	"responseCode"=> "01",
    			"responseDesc"=> "Inquiry Sukses.",
    			"responseData"=> $image
            ],200);	
        }
        
        return response()->json([
            "responseCode"=> "02",
    		"responseDesc"=> "Inquiry Gagal.",
    		"responseData"=> ["image"=>"Nik Tidak Ditemukan"]
    	], 200 );
    }
}
