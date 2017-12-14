<?php
Route::group(['prefix'=>'v1/int/crm', 'middleware' => 'api.auth', 'namespace' => 'API\v1\Int\Crm'], function(){
 // route dashboard
 Route::get( '/', 'DashboardController@index', [

 ] )->name('crm.index');

 //route account
 // Route::resource( 'account', 'AccountController', [
 //   'only' => ['index','show']
 // ] );

 //route activity
 Route::resource( 'activity', 'marketingActivityController', [
   'only' => ['index', 'create', 'store', 'update']
 ] );

 // Route::get('/activity/reSchedule', 'ActivityController@reSchedule')->name('activity.reschedule');

 //Route Marketing
 Route::resource('marketing', 'MarketingController', [
   'only' => ['index', 'create', 'store', 'update', 'show']
 ]);
});
