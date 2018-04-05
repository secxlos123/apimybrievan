<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFunctionUkerDistance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE OR REPLACE FUNCTION distance_uker(i float8,j float8, k INT )  
        	RETURNS TABLE(kode_uker TEXT) AS $$
			DECLARE
			 var_r record;
			BEGIN
			FOR var_r IN(select newtabs.kode_uker from (select
								2 * 3961 * asin(sqrt((sin(radians((latitude - i) / 2))) ^ 2 
							+ cos(radians(i)) * cos(radians(latitude)) * (sin(radians((longitude - j) / 2))) ^ 2)) as distance,
							case when length(uker.kode_uker)='5' then uker.kode_uker
							when length(uker.kode_uker)='4' then '0'||uker.kode_uker
							when length(uker.kode_uker)='3' then '00'||uker.kode_uker
							when length(uker.kode_uker)='2' then '000'||uker.kode_uker
							when length(uker.kode_uker)='1' then '0000'||uker.kode_uker
							END AS kode_uker
							from
								uker_tables uker)newtabs where newtabs.distance<=k GROUP BY newtabs.kode_uker)
							
			LOOP
						  kode_uker := var_r.kode_uker ;

						  RETURN NEXT;
						END LOOP;
			END
		$$ LANGUAGE plpgsql");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
          DB::unprepared('DROP PROCEDURE IF EXISTS distance_uker');
    }
}
