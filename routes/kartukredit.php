<?php


Route::group(['prefix' => 'v1/kk','namespace'=> 'API\v1'], function() {
    Route::get('get-data','KartuKreditController@example');
});
//example