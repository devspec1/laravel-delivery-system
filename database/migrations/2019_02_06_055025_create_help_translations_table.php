<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHelpTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('help_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('help_id')->unsigned();
            $table->string('name');   
            $table->longText('description');   
            $table->string('locale',5)->index();
            $table->unique(['help_id','locale']);
            $table->foreign('help_id')->references('id')->on('help')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('help_translations');
    }
}
