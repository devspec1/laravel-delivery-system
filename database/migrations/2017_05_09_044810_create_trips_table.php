<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
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
            $table->integer('request_id')->unsigned();
            $table->foreign('request_id')->references('id')->on('request')->onDelete('cascade');
            $table->integer('driver_id')->unsigned();
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('trip_path');
            $table->text('map_image');
            $table->decimal('total_time', 7, 2);
            $table->decimal('total_km', 7, 2);
            $table->decimal('time_fare', 11, 2);
            $table->decimal('distance_fare', 11, 2);
            $table->decimal('base_fare', 11, 2);
            $table->decimal('peak_fare',11,2);
            $table->decimal('peak_amount',11,2);
            $table->decimal('driver_peak_amount',11,2);
            $table->decimal('schedule_fare', 11, 2);
            $table->decimal('access_fee', 11, 2);
            $table->decimal('tips', 11, 2)->default(0);
            $table->decimal('waiting_charge', 11, 2)->default(0);
            $table->integer('toll_reason_id')->unsigned()->nullable();
            $table->foreign('toll_reason_id')->references('id')->on('toll_reasons')->onDelete('cascade');
            $table->decimal('toll_fee', 11, 2)->default(0);
            $table->decimal('wallet_amount', 11, 2);
            $table->decimal('promo_amount', 11, 2);
            $table->decimal('subtotal_fare', 11, 2);
            $table->decimal('total_fare', 11, 2);
            $table->decimal('driver_payout', 11, 2);
            $table->decimal('driver_or_company_commission', 11, 2);
            $table->decimal('owe_amount', 11, 2);
            $table->decimal('remaining_owe_amount', 11, 2);
            $table->decimal('applied_owe_amount', 11, 2);
            $table->string('to_trip_id', 100);
            $table->timestamp('arrive_time')->default('0000-00-00 00:00:00');
            $table->timestamp('begin_trip')->default('0000-00-00 00:00:00');
            $table->timestamp('end_trip')->default('0000-00-00 00:00:00');
            $table->text('paykey');
            $table->string('payment_mode',50)->default('Braintree');
            $table->enum('payment_status',['Pending', 'Completed','Trip Cancelled'])->default('Pending');
            $table->enum('is_calculation',['1', '0'])->default('0');
            $table->string('currency_code',10);
            $table->foreign('currency_code')->references('code')->on('currency');
            $table->enum('status',['Scheduled', 'Cancelled','Begin trip','End trip','Payment','Rating','Completed','Null'])->default('Null');
            $table->string('otp','10')->default('Null');
            $table->timestamps();
            $table->softDeletes();
            });
        
        $statement = "ALTER TABLE users AUTO_INCREMENT = 10001;";

        DB::unprepared($statement);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::dropIfExists('trips');
    }
}
