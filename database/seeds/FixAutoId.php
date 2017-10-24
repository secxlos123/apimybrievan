<?php

use Illuminate\Database\Seeder;

class FixAutoId extends Seeder
{
    /**
     * Fix AutoIncrement ID.
     * @author Akse
     * @return void
     */
    public function run()
    {
        $table = \DB::select('SELECT * FROM information_schema.tables WHERE table_schema=? AND table_type=?',array('public','BASE TABLE'));
        foreach ($table as $key => $value) {
        	$id = $value->table_name.'_id_seq';
        	$tab = $value->table_name;
        	if ($tab != 'role_users') {
        	\DB::statement("SELECT MAX(id) FROM $tab ");
        	\DB::statement("SELECT nextval('$id')");
        	\DB::statement("BEGIN");
        	\DB::statement("LOCK TABLE $tab IN EXCLUSIVE MODE");
        	\DB::statement("SELECT setval( '$id', COALESCE((SELECT MAX(id)+1 FROM $tab), 1),false)");
        	\DB::statement("COMMIT");
        	}
        	else
        	{
        		continue;
        	}
        }
        
    }
}
