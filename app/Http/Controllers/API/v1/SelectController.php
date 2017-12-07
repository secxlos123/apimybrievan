<?php

namespace App\Http\Controllers\API\v1;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Mitra;
use App\Models\Jenispinjaman;
use App\Models\Tujuanpenggunaan;
use App\Models\Pendidikan_terakhir;
//use App\Models\EformBriguna;
use Sentinel;
use DB;

class SelectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	public function Select()
	{
//		$mitra = Mitra::get();
//		DB::commit();
		$jpinjaman = Jenispinjaman::get();
		DB::commit();
		$tpenggunaan = Tujuanpenggunaan::get();
		DB::commit();
		$pendidikan_terakhir = Pendidikan_terakhir::get();
		DB::commit();
		$select = ['jpinjaman'=>$jpinjaman,'tpenggunaan'=>$tpenggunaan,'pendidikan_terakhir'=>$pendidikan_terakhir];
		return response()->success( [
			'message' => 'Ok.',
			'contents' => $select
		], 201 );
	}
	 public function show( $type, $eform_id )
    {
/*               \Log::info($request->all());
        $limit = $request->input( 'limit' ) ?: 10;
        $newForm = EForm::filter( $request )->paginate();
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $newForm
        ], 200 ); */

    }


}
