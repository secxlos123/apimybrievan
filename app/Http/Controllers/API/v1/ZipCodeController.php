<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Asmx;
use Illuminate\Http\Request;

class ZipCodeController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		$zip_code_service = Asmx::setEndpoint('GetDataKodePos')->setQuery([
			'search' => $request->search,
			'limit' => $request->limit,
			'page' => $request->page,
			'sort' => $request->sort,
		])->post();
		$zip_code_list = $zip_code_service['contents'];
		$zip_code_list['data'] = array_map(function ($content) {
			return [
				'id' => $content['kode_pos'],
				'kota' => $content['dati2'],
				'kecamatan' => $content['kecamatan'],
				'kelurahan' => $content['kelurahan'],
			];
		}, $zip_code_list['data']);
		\Log::info($zip_code_list['data'][0]);
		return response()->success([
			'message' => 'Sukses',
			'contents' => $zip_code_list,
		], 200);
	}
}
