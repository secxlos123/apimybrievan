<?php

namespace App\Http\Controllers\API\v1\Int;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Mitra;
use App\Models\Jenispinjaman;
use App\Models\Tujuanpenggunaan;
use App\Models\Pendidikan_terakhir;
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
		$mitra = Mitra::get();
		DB::commit();
		$jpinjaman = Jenispinjaman::get();
		DB::commit();
		$tpenggunaan = Tujuanpenggunaan::get();
		DB::commit();
		$pendidikan_terakhir = Pendidikan_terakhir::get();
		DB::commit();
		$select = ['mitra'=>$mitra,'jpinjaman'=>$jpinjaman,'tpenggunaan'=>$tpenggunaan,'pendidikan_terakhir'=>$pendidikan_terakhir];
		return response()->success( [
			'message' => 'Ok.',
			'contents' => $select
		], 201 );
	}


}
