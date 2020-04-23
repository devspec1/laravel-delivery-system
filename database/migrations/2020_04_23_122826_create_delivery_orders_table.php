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
            $table->text('customer_name');
            $table->text('customer_phone_number');
            $table->enum('status', ['new', 'assigned', 'delivered']);
            $table->text('estimate_time');
            $table->decimal('fee', 11, 2)->nullable();
            $table->char('currency_code', 3)->nullable();
            $table->text('pick_up_location');
            $table->string('pick_up_latitude',100);
            $table->string('pick_up_longitude',100);
            $table->text('drop_off_location');
            $table->string('drop_off_latitude',100);
            $table->string('drop_off_longitude',100);
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