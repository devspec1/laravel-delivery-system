<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleCancelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_cancel', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('schedule_ride_id')->unsigned();
            $table->foreign('schedule_ride_id')->references('id')->on('schedule_ride')->onDelete('cascade');
            $table->string('cancel_reason');
            $table->integer('cancel_reason_id')->unsigned();
            $table->foreign('cancel_reason_id')->references('id')->on('cancel_reasons')->onDelete('cascade');
            $table->enum('cancel_by',['Rider','Driver','Admin','Company']);
            $table->timestamps();
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
        Schema::dropIfExists('schedule_cancel');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
