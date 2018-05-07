<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'mysql2' => [
            'driver' => 'mysql',
            'host' => env('DB_EXT_HOST', '127.0.0.1'),
            'port' => env('DB_EXT_PORT', '3306'),
            'database' => env('DB_EXT_DATABASE', 'forge'),
            'username' => env('DB_EXT_USERNAME', 'forge'),
            'password' => env('DB_EXT_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],
		'sqlsrv' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_LAS', '10.35.65.166'),
            'database' => env('DB_DATABASE_LAS', 'forge'),
            'port' => env('DB_PORT_LAS', '1433'),
            'username' => env('DB_USERNAME_LAS', 'forge'),
            'password' => env('DB_PASSWORD_LAS', ''),
            'charset' => 'utf8',
            'prefix' => '',
        ],
		'sqlsrv_prod' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_LAS_PROD', '172.21.53.70'),
            'database' => env('DB_DATABASE_LAS_PROD', 'forge'),
            'port' => env('DB_PORT_LAS_PROD', '1433'),
            'username' => env('DB_USERNAME_LAS_PROD', 'forge'),
            'password' => env('DB_PASSWORD_LAS_PROD', ''),
            'charset' => 'utf8',
            'prefix' => '',
        ],
		'sqlsrv_clas_prod' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_CLAS_PROD', '172.18.41.118'),
            'database' => env('DB_DATABASE_CLAS_PROD', 'forge'),
            'port' => env('DB_PORT_CLAS_PROD', '1433'),
            'username' => env('DB_USERNAME_CLAS_PROD', 'forge'),
            'password' => env('DB_PASSWORD_CLAS_PROD', ''),
            'charset' => 'utf8',
            'prefix' => '',
        ],
		'sqlsrv_clas' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_CLAS', '10.35.65.167'),
            'database' => env('DB_DATABASE_CLAS', 'forge'),
            'port' => env('DB_PORT_CLAS', '1433'),
            'username' => env('DB_USERNAME_CLAS', 'forge'),
            'password' => env('DB_PASSWORD_CLAS', ''),
            'charset' => 'utf8',
            'prefix' => '',
        ]

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
