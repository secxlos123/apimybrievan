<?php

namespace App\Http\Controllers\API\v1\Int\mitra;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\GimmickRequest;
use App\Models\User;
use App\Models\Mitra\registrasi_perjanjian;
/* use App\Models\Mitra\MitraHeader;
use App\Models\Mitra\MitraDetail;
use App\Models\Mitra\MitraPemutus; */
use DB;

class RegisterPerjanjianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        \Log::info($request->all());
        $limit = $request->input( 'limit' ) ?: 10;
        $newForm = GIMMICK::filter( $request )->paginate( $limit );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $newForm
        ], 200 );
    }

    public function uploadimage($image,$id,$atribute) {
        //$eform = EForm::findOrFail($id);
        $path = public_path( 'uploads/mitra/' . $id . '/' );

        if ( ! empty( $this->attributes[ $atribute ] ) ) {
            File::delete( $path . $this->attributes[ $atribute ] );
        }
        $filename = null;
        if ($image) {
            if (!$image->getClientOriginalExtension()) {
                if ($image->getMimeType() == '.pdf') {
                    $extension = '.pdf';
                }elseif ($image->getMimeType() == '.jpg') {
                    $extension = 'jpg';
                }elseif ($image->getMimeType() == '.jpeg') {
                    $extension = 'jpeg';
                }else{
                    $extension = 'png';
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

    public function store( Request $request )
    {
		try{
        $baseRequest = $request->all();
			$upload_perjanjian = $this->uploadimage($baseRequest['upload_perjanjian'],$baseRequest['id_header'],'upload_perjanjian');
			$baseRequest['upload_perjanjian'] = $upload_perjanjian;
			$registrasi_perjanjian = registrasi_perjanjian::create( $baseRequest );
			$return = [
                    'message' => 'Registrasi Perjanjian berhasil Diajukan.',
                    'contents' => 'Sukses'
                ];
		} catch (Exception $e) {
            DB::rollback();
           $return = [
                    'message' => 'Terjadi Kesalahan Silahkan Tunggu Beberapa Saat Dan Ulangi.',
                    'contents' => 'Gagal'
                ];
        }
		return $return;
    }

}
