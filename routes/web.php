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