<?php

namespace App\Http\Controllers\API\v1;

use DB;
use File;
use App\Models\KPR;
use App\Models\EForm;
use App\Models\Collateral;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
            $doc_col = $this->getAllImage($request);
            \Log::info("===COUNT DOC UPLOAD : ".count($image));
            \Log::info("===COUNT DOC COLLATERAL : ".count($doc_col));
            $dataALL = array_merge($image, $doc_col);
            \Log::info("===COUNT ALL DOC : ".count($dataALL));
                return response()->json([
                    "responseCode"=> "01",
                    "responseDesc"=> "Inquiry Sukses.",
                    "responseData"=> $dataALL
                ],200);
                }
            }
            return response()->json([
            "responseCode"=> "02",
            "responseDesc"=> "Inquiry Gagal.",
            "responseData"=> ["image"=>"Nik Tidak Ditemukan"]
        ], 200 );
    }

    /**
     * This function for get Document Uploads Collateral
     * @param $request->nik
     * @author rangga.darmajati <rangga.darmajati@wgs.co.id>
     */
    public function getAllImage(Request $request){
        $nik        = $request->nik;
        $EForm      = EForm::where('nik', $nik)->first();
        $id         = $EForm->id;
        $kpr        = KPR::where('eform_id', $id)->first();
        $dev_id     = $kpr->developer_id;
        
        if($dev_id != 1){
            $image = array();
            return $image;
        }

        $prop_id    = $kpr->property_id;
        $collateral = Collateral::where('developer_id', $dev_id)->where('property_id', $prop_id)->first();
        $col_id     = $collateral->id;
        $path       = public_path('uploads/collateral/'.$col_id);
        
        if( is_dir($path) ){
            $files = File::allFiles($path);
            if( count($files) > 0 ){
                $image = array();
                foreach ($files as $file) {
                    if ( !empty($file) ) {
                    $image[]['name'] = url('uploads/collateral/'.$col_id.'/'.$file->getFilename());
                    }
                }
            }

            return $image;
        }
            $image = array();
            return $image;  
    }
}
