<?php

namespace App\Http\Controllers\API\v1\Int\mitra;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\GimmickRequest;
use App\Models\User;
use App\Models\Mitra\MitraKelayakan;
use DB;

class PenilaianKelayakanController extends Controller
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

    public function store( GimmickRequest $request )
    {
	try{
		$baseRequest = $request->all();
		$baseRequest['penilaian_kelayakan']['id_header'] = $baseRequest['penilaian_kelayakan']['id_mitra'];
		$mitra = MitraKelayakan::create( $baseRequest['penilaian_kelayakan'] );
		
		$penilaian_update = DB::table('mitra_detail_dasar')
            ->where('id_header','=', $baseRequest['penilaian_kelayakan']['id_header'])
			->update(['status' => 'penilaian']);
		
			$return = [
                    'message' => 'Data Berhasil Diberi Penilaian.',
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
