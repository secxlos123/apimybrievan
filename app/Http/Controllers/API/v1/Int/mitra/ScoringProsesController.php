<?php

namespace App\Http\Controllers\API\v1\Int\mitra;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\GimmickRequest;
use App\Models\User;
use App\Models\Mitra\ScoringMitra;
use DB;

class ScoringProsesController extends Controller
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
	  public function getallmitra( Request $request )
    {
		
        $baseRequest = $request->all();
		$all = DB::table('mitra_utama')
                     ->select('*')
					 ->join('mitra_detail_fasilitas', 'mitra_utama.idMitrakerja', '=', 'mitra_detail_fasilitas.id_header')
					 ->join('mitra_detail_dasar', 'mitra_utama.idMitrakerja', '=', 'mitra_detail_dasar.id_header')
					 ->join('mitra_detail_data','mitra_utama.idMitrakerja','=','mitra_detail_data.id_header')
					 ->join('mitra_detail_payroll','mitra_utama.idMitrakerja','=','mitra_detail_payroll.id_header')
					 ->join('mitra_pemutus','mitra_utama.idMitrakerja','=','mitra_pemutus.id_header')
					 ->limit(1)
                     ->where('idMitrakerja', $baseRequest['id_header'])
                     ->get();
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $all
        ], 200 );
    }

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
        $baseRequest = $request->all();
		$mitra_scoring = ScoringMitra::create( $baseRequest['scoring_mitra'] );
		
		return $mitra_scoring;
    }

}
