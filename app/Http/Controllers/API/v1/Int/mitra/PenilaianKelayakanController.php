<?php

namespace App\Http\Controllers\API\v1\Int\mitra;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\GimmickRequest;
use App\Models\User;
use App\Models\Mitra\MitraHeader;
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
		$data_header = $request->all();
		$penilaian_update = DB::table('mitra_header')
            ->where('id_header', $data_header['penilaian_kelayakan']['id_header'])
            ->update(['rekomendasi_unit_kerja' => $data_header['penilaian_kelayakan']['rekomendasi_unit_kerja']]);
		
		return $penilaian_update;
    }

}
