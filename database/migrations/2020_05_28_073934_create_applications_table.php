<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->enum('type',['Driver', 'Merchant', 'Community Leader'])->nullable();
            $table->string('vehicleType');
            $table->string('q_hear');
            $table->string('q_popularItem');
            $table->string('q_expectOrders');
            $table->string('asset_website');
            $table->string('asset_facebook');
            $table->string('asset_instagram');
            $table->string('asset_other');
            $table->string('logo');
            $table->string('photoItem');   
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
        Schema::dropIfExists('applications');
    }
}
