<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('pickup_latitude',100);
            $table->string('pickup_longitude',100);
            $table->string('drop_latitude',100);
            $table->string('drop_longitude',100);
            $table->string('pickup_location',255);
            $table->string('drop_location',255);
            $table->integer('car_id')->unsigned();
            $table->foreign('car_id')->references('id')->on('car_type')->onDelete('cascade');
            $table->integer('group_id')->nullable();
            $table->integer('driver_id')->unsigned();
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('payment_mode',50)->default('Credit Card');
            $table->string('schedule_id',100)->default('Null');
            $table->integer('location_id')->unsigned();
            $table->enum('additional_fare',['Peak']); 
            $table->string('peak_fare',100); 
            $table->string('timezone',100);
            $table->text('trip_path');
            $table->string('status',100)->default('Null');
            $table->nullableTimestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('request');
    }
}
