<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Crm\Status;

class CreateCrmStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('crm_statuses', function (Blueprint $table) {
          $table->increments('id');
          $table->string('status_name');
          $table->timestamps();
      });

      // $status_names = ['Prospek', 'Negosiasi', 'Done', 'Batal'];
      //   foreach ($status_names as $status_name){
	    //     Status::create(['status_name' => $status_name, 'created_at' => \Carbon\Carbon::now()->toDateTimeString(), 'updated_at' => \Carbon\Carbon::now()->toDateTimeString()]);
      //   }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crm_statuses');
    }
}
