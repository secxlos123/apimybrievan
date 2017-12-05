<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\ScoringRequest;
use App\Models\Scoring;
use App\Models\EForm;
use App\Models\User;
use Sentinel;
use DB;

class ScoringController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index( Request $request )
	{
		$limit = $request->input( 'limit' ) ?: 10;
		$customers = User::getCustomers( $request )->paginate( $limit );
		return response()->success( [
			'message' => 'Sukses',
			'contents' => $customers
		], 200 );
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \App\Http\Requests\API\v1\ScoringRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function storebefore( ScoringRequest $request )
	{

		$data = $request->all();
		if($data['product_leads']=='kpr'){
				DB::beginTransaction();
			$customer = Customer::create( $request->all() );

			DB::commit();
			return response()->success( [
				'message' => 'Data nasabah berhasil ditambahkan.',
				'contents' => $customer
			], 201 );
		}elseif($data['product_leads']=='briguna'){
			$data['address'] = $data['alamat'].' rt '.$data['rt'].'/rw '.$data['rw'].', kelurahan='.
								$data['kelurahan'].'kecamatan='.$data['kecamatan'].','.$data['kota'].' '.$data['kode_pos'];

			$data['address_domisili'] = $data['alamat_domisili'].' rt '.$data['rt_domisili'].'/rw '.
								$data['rw_domisili'].', kelurahan='.$data['kelurahan_domisili'].'kecamatan='.$data['kecamatan_domisili'].','.$data['kota_domisili'].' '.$data['kode_pos_domisili'];
			DB::beginTransaction();
			$customer = Customer::create( $data );

			DB::commit();
			return response()->success( [
				'message' => 'Data nasabah berhasil ditambahkan.',
				'contents' => $data
			], 201 );
		}
	}

	 public function uploadimage($image,$id){
	 	$eform = EForm::where('id', $id)->first();
		$path = public_path( 'uploads/' . $eform->nik . '/' );
		if ( ! empty( $this->attributes[ 'uploadscore' ] ) ) {
            File::delete( $path . $this->attributes[ 'uploadscore' ] );
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
	        $filename = $id . '-prescreening.' . $extension;
	        $image->move( $path, $filename );
        }
		return $filename;

	 }

	 public function uploadimagemulti($image,$id,$i){
	 	$eform = EForm::where('id', $id)->first();
		$path = public_path( 'uploads/' . $eform->nik . '/' );
		if ( ! empty( $this->attributes[ 'uploadscore'.$i ] ) ) {
            File::delete( $path . $this->attributes[ 'uploadscore'.$i ] );
        }
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
        $filename = $id.'-'.$i.'-prescreening.' . $extension;
        $image->move( $path, $filename );
		return $filename;

	 }
	public function store( ScoringRequest $request )
	{
		$id = $request->id;
		$filename2 = '';
		$countu = $request->countupload;
		$image = $request->uploadscore;
		$filename = $this->uploadimage($image,$id);
		$dats = $request->except('id');
		if($countu>2){
		for($i=2;$i<$countu;$i++){
			$image = $request['uploadscore'.$i];
			$filename2 .= $this->uploadimagemulti($image,$id,$i).',';
			unset($dats['uploadscore'.$i]);
		}
		}
		unset($dats['countupload']);
		//$request->uploadscore = $filename;

		//---------here
		$dats['uploadscore'] = $filename;
		if ($filename2 != '') {
			$dats['uploadscore'] .= ','.$filename2;

		}

		$data = EForm::findOrFail( $id );
		$personal = $data->customer->personal;

		$dhn = \RestwsHc::setBody( [
            'request' => json_encode( [
                'requestMethod' => 'get_dhn_consumer',
                'requestData' => [
                    'id_user' => request()->header( 'pn' ),
                    'nik'=> $data->nik,
                    'nama_nasabah'=> strtolower($personal['first_name'].' '.$personal['last_name']),
                    'tgl_lahir'=> $personal['birth_date']
                ]
            ] )
        ] )->setHeaders( [
            'Authorization' => request()->header( 'Authorization' )
        ] )->post( 'form_params' );
        \Log::info($dhn);

        if ($dhn['responseCode'] != '00') {
            $dhn = ['responseData' => [['warna' => 'Hijau']], 'responseCode' => '01'];

        }
        \Log::info($dhn);

        $sicd = \RestwsHc::setBody( [
            'request' => json_encode( [
                'requestMethod' => 'get_sicd_consumer',
                'requestData' => [
                    'id_user' => request()->header( 'pn' ),
                    'nik'=> $data->nik,
                    'nama_nasabah'=> strtolower($personal['first_name'].' '.$personal['last_name']),
                    'tgl_lahir'=> $personal['birth_date'],
                    'kode_branch'=> $data->branch_id
                ]
            ] )
        ] )->setHeaders( [
            'Authorization' => request()->header( 'Authorization' )
        ] )->post( 'form_params' );
         \Log::info($sicd);

        if ($sicd['responseCode'] != '00') {
            $sicd = ['responseData' => [['bikole' => '-']], 'responseCode' => '01'];

        }

		$score = $request->input('pefindo_score');
		$pefindoC = 'Kuning';
		if ( $score >= 250 && $score <= 573 ) {
			$pefindoC = 'Merah';

		} elseif ( $score >= 677 && $score <= 900 ) {
			$pefindoC = 'Hijau';

		}

        $dhnC = $dhn['responseData'][0]['warna'];

        $target = 1;

        foreach ($sicd['responseData'] as $responseData) {
        	if ($sicd['responseCode'] == '00') {
        		$date = explode(" ", $responseData['tgl_lahir']);

	        	if ( strtoupper($responseData['nama_debitur']) == strtoupper($personal['first_name'].' '.$personal['last_name']) && $personal['birth_date'] == $date[0] && $personal['nik'] == $responseData['no_identitas'] ) {
	        		if ( $responseData['bikole'] > $target ) {
	        			$target = $responseData['bikole'];
	        		}

	        	}
        	}
        }

        if ( $target == 1 ) {
            $sicdC = 'Hijau';

        } elseif ( $target == 2 ) {
            $sicdC = 'Kuning';

        } else {
            $sicdC = 'Merah';

        }

        $calculate = array($pefindoC, $dhnC, $sicdC);

        if ( in_array('Merah', $calculate) ) {
            $result = '3';

        } else if ( in_array('Kuning', $calculate) ) {
            $result = '2';

        } else {
            $result = '1';

        }

        $dats['prescreening_status'] = $result;
        $dats['dhn_detail'] = json_encode($dhn);
        $dats['sicd_detail'] = json_encode($sicd);

		DB::beginTransaction();
        $data->update($dats);
        generate_pdf('uploads/'. $data->nik, 'prescreening.pdf', view('pdf.prescreening', compact('data')));
		DB::commit();
		return response()->success( [
			'message' => 'Data nasabah berhasil dirubah.',
			'contents' => $data
		] );
	}

	public function show( $id )
	{
		$customer = Customer::findOrFail( $id );
		return response()->success( [
			'message' => 'Sukses',
			'contents' => $customer
		], 200 );
	}

}
