<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Crm\ActivityType;

class CreateCrmActivityTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('crm_activity_types', function (Blueprint $table) {
          $table->increments('id');
          $table->string('activity_name');
          $table->timestamps();
      });

      // $activity_names = ['Pick up Service', 'TOP', 'CMS', 'Akuisisi'];
      //   foreach ($activity_names as $activity_name){
	    //     ActivityType::create(['activity_name' => $activity_name, 'created_at' => \Carbon\Carbon::now()->toDateTimeString(), 'updated_at' => \Carbon\Carbon::now()->toDateTimeString()]);
      //   }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crm_activity_types');
    }
}
