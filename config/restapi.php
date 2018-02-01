<?php

return [

	/*
    |--------------------------------------------------------------------------
    | CLIENT_ASMX return xml
    |--------------------------------------------------------------------------
    */

    'asmx'      => env('CLIENT_ASMX', 'http://10.35.65.167:6969/service.asmx/'),

    'asmx_las'      => env('CLIENT_ASMX_LAS', 'http://10.35.65.165:1104/service.asmx/'),

    'Brispot'      => env('CLIENT_BRISPOT', 'http://api.briconnect.bri.co.id/bribranch/'),
    // 'asmx_las'      => env('CLIENT_ASMX_LAS', 'http://10.35.65.167:1104/service.asmx/'),

    /*
    |--------------------------------------------------------------------------
    | CLIENT_RESTWSHC return json
    |--------------------------------------------------------------------------
    */

    // 'restwshc'  => env('CLIENT_RESTWSHC', 'https://pinjaman.bri.co.id/restws_hc'),
    'dbwsrest'  => env('CLIENT_DBWSREST', 'http://10.35.65.111/skpp_concept/dbws_rest_briguna'),

	'restwshc'  => env('CLIENT_RESTWSHC', 'http://10.35.65.111/skpp_concept/restws_hc'),

  	'key'  		=> env('CLIENT_KEY', '$2y$10$OoDAS6saH1b3D/nZJ4DXKuOTqVumFTACUZDFkZfepS1h15jDNxdzK'),

    /*
    |--------------------------------------------------------------------------
    | CLIENT_APIPDM
    |--------------------------------------------------------------------------
    */
    'apipdm' => 'http://api.briconnect.bri.co.id',
     'apipdmdev' => 'http://172.18.44.182',
    'pdm_client_id' => '3f60d2edcd0399e6ea25290fe4022e0af91e5016',
    'pdm_client_secret' => 'ef3d569a4a609c636e114ff9056b8c324e0f2e7a',
];
