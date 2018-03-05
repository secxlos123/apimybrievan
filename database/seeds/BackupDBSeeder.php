<?php

use Illuminate\Database\Seeder;

class BackupDBSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $schema = "DB_" . date('Ym');
        $baseSchema = 'public';
        $user = env('DB_USERNAME', 'postgres');

        DB::statement("GRANT CREATE ON SCHEMA $baseSchema TO $user;");
        DB::statement("CREATE SCHEMA IF NOT EXISTS $schema AUTHORIZATION $user;");
        $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = '$baseSchema' AND table_name NOT IN ('migrations') ORDER BY table_name ASC;");
        foreach ( $tables as $table ) {
            DB::select("DROP TABLE IF EXISTS $schema.$table->table_name;");
            DB::select("SELECT * INTO $schema.$table->table_name FROM $baseSchema.$table->table_name;");
            echo "Table $table->table_name Execute\n";
        };
    }
}
