<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('companies');
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',100);
            $table->string('profile',100);
            $table->string('email',100);
            $table->string('country_code',100);
            $table->string('mobile_number',20);
            $table->string('vat_number',100)->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->enum('status',['Pending','Active', 'Inactive'])->default('Pending');
            $table->enum('device_type',['1', '2'])->nullable();
            $table->text('device_id');
            $table->string('language',50);
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country',100);
            $table->string('postal_code')->nullable();
            $table->string('company_commission',5)->default('0');
            $table->timestamps();
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
        Schema::drop('companies');
    }
}
