<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdCollumThirdParties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('third_parties', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable();
            $table->string('created_by')->nullable();
            $table->dropColumn('is_actived');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('third_parties', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('created_by');
            $table->enum('is_actived', ['active', 'disabled'])->default('active');
        });
    }
}
