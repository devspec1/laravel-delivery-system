<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleRideTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_ride', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('schedule_date',255);
            $table->string('schedule_time',255);
            $table->string('schedule_end_date',100);
            $table->string('schedule_end_time',100);
            $table->string('pickup_latitude',100);
            $table->string('pickup_longitude',100);
            $table->string('drop_latitude',100);
            $table->string('drop_longitude',100);
            $table->string('pickup_location',255);
            $table->string('drop_location',255);
            $table->text('trip_path');
            $table->integer('car_id')->unsigned();
            $table->foreign('car_id')->references('id')->on('car_type')->onDelete('cascade');
            $table->integer('location_id')->unsigned();
            $table->integer('peak_id')->unsigned();
            $table->enum('booking_type',['Manual Booking','Schedule Booking'])->default('Schedule Booking');
            $table->integer('driver_id')->default(0);
            $table->enum('status',['Pending','Completed','Cancelled','Car Not Found'])->nullable();
            $table->string('timezone',100); 
            $table->string('payment_method',50);     
            $table->enum('is_wallet',['Yes','No'])->nullable();
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
         DB::statement('SET FOREIGN_KEY_CHECKS = 0');
         Schema::dropIfExists('schedule_ride');
         DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
