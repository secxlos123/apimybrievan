<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\BRIGUNA;
use App\Models\User;
use App\Models\UserServices;
use DB;

class UploadtambahController extends Controller
{


    public function uploadimage($image,$id,$atribute) {
        //$eform = EForm::findOrFail($id);
        $path = public_path( 'uploads/' . $id . '/' );

        if ( ! empty( $this->attributes[ $atribute ] ) ) {
            File::delete( $path . $this->attributes[ $atribute ] );
        }
        $filename = null;
        if ($image) {
            if (!$image->getClientOriginalExtension()) {
                if ($image->getMimeType() == '.pdf') {
                    $extension = '.pdf';
                }else if($image->getMimeType() == '.png'){
                    $extension = 'png';
                }else{
                    $extension = 'jpg';
                }
            }else{
                $extension = $image->getClientOriginalExtension();
            }
            // log::info('image = '.$image->getMimeType());
            $filename = $id . '-'.$atribute.'.' . $extension;
            $image->move( $path, $filename );
        }
        return $filename;
    }


    public function upload( EFormRequest $request )
    {
        DB::beginTransaction();
        try {

            // Get User Login
            $user_login = \RestwsHc::getUser();


            \Log::info("=======================================================");

			$x = $request->tambahandata;
			$baseData = $request->all();
			for($i=0;$i<$x;$i++){
				$uploadtambahan = $baseData['datatambahan'.$i];
				$id = date('YmdHis');
				$uploadtambahan = $this->uploadimage($uploadtambahan,$id,'datatambahan');
					if($i==0){
						$baseRequest['datatambahan'] = $uploadtambahan;
					}else{
						$baseRequest['datatambahan'] .= '<|-.-|>'.$uploadtambahan;
					}
               }
				$upload = BRIGUNA::where("eform_id","=",$eform_id);
                $upload->update($baseRequest);

                $return = [
                    'message' => 'Upload data tambahan berhasil.',
                    'contents' => $upload
                ];
                    \Log::info($upload);

            DB::commit();
    } catch (Exception $e) {
            DB::rollback();
            return response()->error( [
                'message' => 'Terjadi Kesalahan Silahkan Tunggu Beberapa Saat Dan Ulangi',
            ], 422 );
        }
        return response()->success($return, 201);
    }

  }
