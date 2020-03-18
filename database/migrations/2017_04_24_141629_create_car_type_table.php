<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('car_type');
        Schema::create('car_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('car_name');
            $table->string('description')->nullable();
            $table->string('vehicle_image');
            $table->string('active_image');
            $table->enum('is_pool',['Yes', 'No'])->default('No');
            $table->enum('status',['Active', 'Inactive'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('car_type');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
