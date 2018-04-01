<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class BackupDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "db:backup {time}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Backup database daily/monthly";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $time = $this->argument( "time" );
        $schema = "DB_" . date( "Ym" );

        if ( $time == "daily" ) {
            $schema .= date( "d" );
        }

        $baseSchema = "public";
        $user = env( "DB_USERNAME", "postgres" );

        DB::statement( "GRANT CREATE ON SCHEMA $baseSchema TO $user;" );
        DB::statement( "CREATE SCHEMA IF NOT EXISTS $schema AUTHORIZATION $user;" );
        $tables = DB::select( "SELECT table_name FROM information_schema.tables WHERE table_schema = '$baseSchema' AND table_name NOT IN ('migrations') ORDER BY table_name ASC;" );
        foreach ( $tables as $table ) {
            DB::select( "DROP TABLE IF EXISTS $schema.$table->table_name;" );
            DB::select( "SELECT * INTO $schema.$table->table_name FROM $baseSchema.$table->table_name;" );
            echo "Table $table->table_name Execute\n";
        };
    }
}
