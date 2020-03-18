<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManageFareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manage_fare', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('location_id');
            $table->integer('vehicle_id')->default(0);
            $table->decimal('base_fare', 5, 2);
            $table->integer('capacity');
            $table->decimal('min_fare', 5, 2);
            $table->decimal('per_min', 5, 2);
            $table->decimal('per_km', 5, 2);
            $table->decimal('schedule_fare', 5, 2);
            $table->decimal('schedule_cancel_fare', 5, 2);
            $table->integer('waiting_time')->nullable();
            $table->decimal('waiting_charge', 5, 2);
            $table->string('currency_code',10)->nullable();
            $table->foreign('currency_code')->references('code')->on('currency');
            $table->enum('apply_peak',['Yes', 'No']);
            $table->enum('apply_night',['Yes', 'No']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manage_fare');
    }
}
