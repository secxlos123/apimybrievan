<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Crm\ProductType;

class CreateCrmProductTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('crm_product_types', function (Blueprint $table) {
          $table->increments('id');
          $table->string('product_name');
          $table->timestamps();
      });

      // $product_names = ['Giro', 'Tabungan', 'Deposito', 'Britama', 'CMS', 'Prioritas'];
      //   foreach ($product_names as $product_name){
	    //     ProductType::create(['product_name' => $product_name, 'created_at' => \Carbon\Carbon::now()->toDateTimeString(), 'updated_at' => \Carbon\Carbon::now()->toDateTimeString()]);
      //   }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
