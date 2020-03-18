<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trip_id')->unsigned();
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
            $table->text('correlation_id')->nullable();
            $table->text('admin_transaction_id')->nullable();
            $table->text('driver_transaction_id')->nullable();
            $table->enum('admin_payout_status',['Pending', 'Processing', 'Paid'])->default('Pending');
            $table->enum('driver_payout_status',['Pending', 'Processing', 'Paid'])->default('Pending');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::dropIfExists('payment');
    }
}
