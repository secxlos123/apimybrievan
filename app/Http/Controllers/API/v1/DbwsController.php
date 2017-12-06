<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class DbwsController extends Controller
{

   public function getimage(Request $request)
    {
        $files = File::allFiles( public_path('uploads/' .$request->nik) );

        if (count($files) > 0) {
            $image = array();
            foreach ($files as $file) {
                if ( !empty($file) ) {
                    $image[]['name'] = \Storage::disk('public')->url($request->nik.'/'.$file->getFilename());
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
