<?php

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

Route::get('/routes', function () {
	$routeCollection = Route::getRoutes();
	return view('routes', compact('routeCollection'));
});

Route::get('/login', function () {
	$login = \RestwsHc::setBody([
		'request' => json_encode([
			'requestMethod' => 'login',
			'requestData' => [
				'user' => '00168857',
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
				'user' => '00168857',
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

Route::get( '/seteform', function() {
	$cek = \Asmx::setEndpoint( 'InsertDataMaster' )->setBody( [
		'request' => '{"nik_pemohon":"3174062507890007", "nama_pemohon":"Gilang Bikin WS", "tempat_lahir_pemohon":"Jambi", "tanggal_lahir_pemohon":"1989-07-25", "alamat_pemohon":"ini alamat pemohon", "jenis_kelamin_pemohon":"l", "kewarganegaraan_pemohon":"ID", "pekerjaan_pemohon_value":"001", "status_pernikahan_pemohon_value":"2", "status_pisah_harta_pemohon":"Pisah Harta", "nik_pasangan":"3174062507891237", "nama_pasangan":"Nama Bojo", "status_tempat_tinggal_value":"0", "telepon_pemohon":"123456789", "hp_pemohon":"082177777669", "email_pemohon":"prayantaalfian@gmail.com", "jenis_pekerjaan_value":"17", "pekerjaan_value":"18", "nama_perusahaan":"Nama Perusahaan 19", "bidang_usaha_value":"20", "jabatan_value":"21", "lama_usaha":"12", "alamat_usaha":"ini alamat usaha", "jenis_penghasilan":"Singe Income", "gaji_bulanan_pemohon":"8100000", "pendapatan_lain_pemohon":"7100000", "gaji_bulanan_pasangan":"2100000", "pendapatan_lain_pasangan":"1100000", "angsuran":"500000", "jenis_kpp_value":"KPR Perorangan PNS / BUMN", "permohonan_pinjaman":"151000000", "uang_muka":"51000000", "jangka_waktu":"240", "jenis_dibiayai_value":"123456789", "sektor_ekonomi_value":"123456789", "project_value":"1086", "program_value":"27", "pihak_ketiga_value":"1016", "sub_pihak_ketiga_value":"1", "nama_keluarga":"siSepupu", "hubungan_keluarga":"Sepupu", "telepon_keluarga":"123456789", "jenis_kredit":"KPR", "tujuan_penggunaan_value":"3", "tujuan_penggunaan":"Pembelian Rumah Baru", "kode_cabang":"0206", "id_prescreening":"12", "nama_ibu":"Ibu Terbaik", "npwp_pemohon":"36.930.247.6-409.000","nama_pengelola":"Oblag","pn_pengelola":"00139644"}'
	] )->post( 'form_params' );
	dd( $cek );
} );