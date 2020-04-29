<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('driver_id')->unsigned()->nullable();
            $table->foreign('driver_id')->references('id')->on('users');
            $table->integer('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('users');
            $table->integer('ride_request')->unsigned()->nullable();
            $table->foreign('ride_request')->references('id')->on('request');
            $table->enum('status', ['new', 'assigned', 'delivered', 'expired']);
            $table->integer('estimate_time')->unsigned();
            $table->decimal('fee', 11, 2)->nullable();
            $table->char('currency_code', 3)->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_orders');
    }
}
