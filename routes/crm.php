<?php
Route::group(['prefix'=>'v1/int/crm', 'middleware' => 'api.auth', 'namespace' => 'API\v1\Int\Crm'], function(){
  // route dashboard
  Route::get( '/', 'DashboardController@index', [

  ] )->name('crm.index');

  //route account
  Route::post( 'account', 'AccountController@index')->name('crm.account');
 
  //route activity
  Route::resource( 'activity', 'marketingActivityController', [
    'only' => ['index', 'create', 'store', 'update']
  ] );

  // Route reschedule Activity
  Route::post('/activity/{activity}/reschedule', 'marketingActivityController@reSchedule')->name('crm.reschedule');

  // Route storeFollowUp Activity
  Route::post('/activity/{activity}/storeFollowUp', 'marketingActivityController@storeFollowUp')->name('crm.storeFollowUp');

  //Route Marketing
  Route::resource('marketing', 'MarketingController', [
    'only' => ['index', 'create', 'store', 'update', 'show']
  ]);
});
