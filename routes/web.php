<?php

use App\Mail\Register;
use App\Mail\VerificationEFormCustomer;
use App\Events\Customer\CustomerVerify;
use Illuminate\Http\Request;
use App\Http\Requests\ApkRequest;
use App\Models\ApkLogs;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/get-restwshc', function() {
	return \RestwsHc::setBody([
        'request' => json_encode([
            'requestMethod' => 'get_customer_profile_nik',
            'requestData' => [
                'app_id' => 'mybriapi'
                , 'nik'     => '3518136206930002'
            ],
        ])
    ])->post( 'form_params' );
});

Route::get('/get-kemendagri', function(){
	try {
		
		$data = RestwsHc::setBody([
                'request' => json_encode([
                    'requestMethod' => 'get_kemendagri_profile_nik',
                    'requestData' => [
                        'nik'     => '3518136206930002'
                        , 'id_user' => '00148421'
                    ],
                ])
            ])->post( 'form_params' );

        return $data;

	} catch (Exception $e) {
		return response()->error( [
                'message' => 'Service Kemendagri DOWN !',
                'contents' => null
            ], 404 );    
	}
});

Route::get('/get-asmx', function() {
	return \Asmx::setEndpoint( 'GetBidangPekerjaan' )->post();
});

if ( ENV( "APPLICATION_POSITION" ) != "production" ) {
	Route::get('/routes', function () {
		$routeCollection = Route::getRoutes();
		return view('routes', compact('routeCollection'));
	});
}

Route::get('/generate_pdf/{ref_number}', function ($ref_number) {
	$detail = \App\Models\EForm::with( 'visit_report.mutation.bankstatement', 'customer', 'kpr', 'recontest' )
		->where('ref_number', $ref_number)->first();
	$path = public_path('uploads/'.$detail->nik);
	File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

    echo "Generate " .generate_pdf('uploads/'. $detail->nik, 'lkn.pdf', view('pdf.approval', compact('detail'))->render());
    echo "<br/>";

    echo "Generate " . generate_pdf('uploads/'. $detail->nik, 'permohonan.pdf', view('pdf.permohonan', compact('detail')));
    echo "<br/>";

   	echo "Generate " . generate_pdf('uploads/'. $detail->nik, 'prescreening.pdf', view('pdf.prescreening', compact('detail')));
    echo "<br/>";

    echo "Generate " . generate_pdf('uploads/'. $detail->nik, 'recontest.pdf', view('pdf.recontest', compact('detail')));
    echo "<br/>";
	return $detail->nik;
});

Route::get('/collateral_pdf/{ref_number}', function ($ref_number) {
	$detail = \App\Models\EForm::with('kpr')
		->where('ref_number', $ref_number)->first();
	$collateral = \App\Models\Collateral::withAll()->where('property_id',$detail->kpr->property_id)->first();
	$eform = $detail;
	$path = public_path('uploads/'.$detail->nik);
	File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
    echo "Generate " . generate_pdf('uploads/'. $detail->nik, 'collateral.pdf', view('pdf.collateral', compact('eform','collateral')));
    echo "<br/>";
	return $detail->nik;
});

Route::get('/resend_verification/{ref_number}', function ($ref_number) {
	$eform = \App\Models\EForm::where( 'ref_number', $ref_number )->first();
	$customer = \App\Models\Customer::where( 'id', $eform->user_id )->first();

	event( new CustomerVerify( $customer, $eform ) );

	return "Resend Verification Email.";
});

Route::get('email', function () {
	$mail = [ 'url' => 'aktivasi akun 3', 'email' => 'rahmatramadhan13@gmail.com'];
	$send = Mail::to( $mail[ 'email' ] )->send( new Register( $mail ) );
	dd($send);
});

Route::get('/login', function () {
	$login = \RestwsHc::setBody([
		'request' => json_encode([
			'requestMethod' => 'login',
			'requestData' => [
				'id_user' => '00168857',
				'password' => '1234'
			]
		])
	])->post('form_params');
	dd($login);
});

Route::get('/logout', function () {
	$logout = \RestwsHc::setBody([
		'request' => json_encode([
			'requestMethod' => 'logout',
			'requestData' => [
				'id_user' => '00168857',
			]
		])
	])
	->setHeaders([
		'Authorization' => 'Bearer chn8mtnbea4mlntl70c7857g2sagmam4d6j4w0lach0889wuyqn8z6fh8yup6yac4sc3go03w7ugcyoyfrf72nxf30jjhuws0rjtu2qmw6n6qtcwmddi45wpmu8oejb1'
	])
	->post('form_params');
	dd($logout);
});

Route::get( '/getbidangpekerjaan', function() {
	$cek = \Asmx::setEndpoint( 'GetBidangPekerjaan' )->post();
	dd( $cek );
} );

Route::get( '/uploads-apk', function() {
	$data = ApkLogs::all();
	return view('uploads-apk.form', compact('data'));
} )->name('uploads-apk');

Route::post( '/post-apk', function(ApkRequest $request) {
	if (isset($request->apkEks) && $request->apkEks) {
		$destinationPath = 'uploads/apk/eks';
		$fileEks = $request->apkEks;
		$fileName = 'eks'.$request->versionEks.$request->versionNumEks.'.apk';
		$fileEks->move($destinationPath,$fileName);

		$insertEks = new ApkLogs();
		$insertEks->apk_type = 'eks';
		$insertEks->version_type = ($request->versionEks == 01) ? 'prod' : 'dev';
		$insertEks->version_number = $request->versionNumEks;
		$insertEks->file_name = $fileName;
		$insertEks->save();

		\Session::flash('status', 'apk eksternal berhasil disimpan');

	}
	if (isset($request->apkInt) && $request->apkInt) {
		$destinationPath = 'uploads/apk/int';
		$fileInt = $request->apkInt;
		$fileName = 'int'.$request->versionInt.$request->versionNumInt.'.apk';
		$fileInt->move($destinationPath,$fileName);

		$insertInt = new ApkLogs();
		$insertInt->apk_type = 'int';
		$insertInt->version_type = ($request->versionInt == 01) ? 'prod' : 'dev';
		$insertInt->version_number = $request->versionNumInt;
		$insertInt->file_name = $fileName;
		$insertInt->save();

		\Session::flash('status', 'apk internal berhasil disimpan');
	}
	return redirect()->route('uploads-apk');

} );

Route::get( '/seteform', function() {
	$cek = \Asmx::setEndpoint( 'InsertDataMaster' )->setBody( [
		'request' => '{"nik_pemohon":"3174062507890007", "nama_pemohon":"Gilang Bikin WS", "tempat_lahir_pemohon":"Jambi", "tanggal_lahir_pemohon":"1989-07-25", "alamat_pemohon":"ini alamat pemohon", "jenis_kelamin_pemohon":"l", "kewarganegaraan_pemohon":"ID", "pekerjaan_pemohon_value":"001", "status_pernikahan_pemohon_value":"2", "status_pisah_harta_pemohon":"Pisah Harta", "nik_pasangan":"3174062507891237", "nama_pasangan":"Nama Bojo", "status_tempat_tinggal_value":"0", "telepon_pemohon":"123456789", "hp_pemohon":"082177777669", "email_pemohon":"prayantaalfian@gmail.com", "jenis_pekerjaan_value":"17", "pekerjaan_value":"18", "nama_perusahaan":"Nama Perusahaan 19", "bidang_usaha_value":"20", "jabatan_value":"21", "lama_usaha":"12", "alamat_usaha":"ini alamat usaha", "jenis_penghasilan":"Singe Income", "gaji_bulanan_pemohon":"8100000", "pendapatan_lain_pemohon":"7100000", "gaji_bulanan_pasangan":"2100000", "pendapatan_lain_pasangan":"1100000", "angsuran":"500000", "jenis_kpp_value":"KPR Perorangan PNS / BUMN", "permohonan_pinjaman":"151000000", "uang_muka":"51000000", "jangka_waktu":"240", "jenis_dibiayai_value":"123456789", "sektor_ekonomi_value":"123456789", "project_value":"1086", "program_value":"27", "pihak_ketiga_value":"1016", "sub_pihak_ketiga_value":"1", "nama_keluarga":"siSepupu", "hubungan_keluarga":"Sepupu", "telepon_keluarga":"123456789", "jenis_kredit":"KPR", "tujuan_penggunaan_value":"3", "tujuan_penggunaan":"Pembelian Rumah Baru", "kode_cabang":"0206", "id_prescreening":"12", "nama_ibu":"Ibu Terbaik", "npwp_pemohon":"36.930.247.6-409.000","nama_pengelola":"Oblag","pn_pengelola":"00139644"}'
	] )->post( 'form_params' );
	dd( $cek );
} );
 // Route::group( ['middleware' => 'file' ], function () {
Route::get('files/{nik}/{file}', 'ImagesController@show3');
Route::get('files/{type}/{id}/{image}', 'ImagesController@show4');
Route::get('files/{type}/{other}/{id}/{image}', 'ImagesController@show5');
// Route::get('files/{nik}/{pdf}/mybri/{key}', 'ImagesController@show2');
 // });
Route::get('resend_activations/{email}', 'ImagesController@ResendActivations');
Route::get('resendpass/{email}', 'ImagesController@ResendPass');