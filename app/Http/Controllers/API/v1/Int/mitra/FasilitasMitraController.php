<?php

namespace App\Http\Controllers\API\v1\Int\mitra;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\GimmickRequest;
use App\Models\User;
use App\Models\Mitra\MitraFasilitasperbankan;
use DB;

class FasilitasMitraController extends Controller
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
        $newForm = MitraFasilitasperbankan::filter( $request )->paginate( $limit );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $newForm
        ], 200 );
    }

    public function uploadimage($image,$id,$atribute) {
        //$eform = EForm::findOrFail($id);
        $path = public_path( 'mitra/' . $id . '/' );

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

    public function store( Request $request )
    {
        $baseRequest = $request->all();
		$ijin_prinsip = $baseRequest['ijin_prinsip'];
		$id = date('YmdHis');
		$baseRequest['ijin_prinsip'] = $this->uploadimage($ijin_prinsip,$id,'ijin_prinsip');
		//$baseRequest['id_header'] = $baseRequest['id_mitra'];
		$fasilitas_data = MitraFasilitasperbankan::create($baseRequest);
		return response()->success( [
            'message' => 'Fasilitas Berhasil Tesimpan',
            'contents' => $fasilitas_data
        ] );
    }

}
