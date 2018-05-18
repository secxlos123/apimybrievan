<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use File;

class DbwsController extends Controller
{

   public function getimage(Request $request)
    {
            $path = public_path('uploads/' .$request->nik);
            if (is_dir($path)) {
            $files = File::allFiles($path);
            if (count($files) > 0) {
                $image = array();
                foreach ($files as $file) {
                    if ( !empty($file) ) {
                        // \Log::info("================MAMAT STYLE=================");
                        //  \Log::info(\Storage::disk('public')->url($request->nik.'/'.$file->getFilename()));                        
                        //$image[]['name'] = \Storage::disk('public')->url($request->nik.'/'.$file->getFilename());

                        // \Log::info("================RANGGA STYLE=================");
                         // \Log::info(url('uploads/'.$request->nik.'/'.$file->getFilename()));
                         $image[]['name'] = url('uploads/'.$request->nik.'/'.$file->getFilename());
                    }
                }
                return response()->json([
                    "responseCode"=> "01",
                    "responseDesc"=> "Inquiry Sukses.",
                    "responseData"=> $image
                ],200);
                }
            }
            return response()->json([
            "responseCode"=> "02",
            "responseDesc"=> "Inquiry Gagal.",
            "responseData"=> ["image"=>"Nik Tidak Ditemukan"]
        ], 200 );
    }
}
