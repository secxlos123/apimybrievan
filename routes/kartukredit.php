<?php


Route::group(['prefix' => 'v1/kk','namespace'=> 'API\v1'], function() {
    Route::get('get-data','KartuKreditController@example');
     Route::post('eform','KartuKreditController@cekEform');


    Route::post('request-nik-crm', 'KartuKreditController@requestNikFromCRM');
   	Route::post('get-niks', 'KartuKreditController@getNiks');
    // Route::get('get-nik','') //cari nik di mybri(nasabah), kalau ada balikin, kalau engga cari lagi di crm baru balikin
});
