<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempManyToManyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_temp')->create('site_tag', function(Blueprint $table) {
            $table->bigInteger('site_id')->unsigned();
            $table->bigInteger('tag_id')->unsigned();

            $table->foreign('site_id')->references('id')->on('sites')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')
                ->onUpdate('cascade')->onDelete('restrict');
        });

        Schema::connection('mysql_temp')->create('site_language', function(Blueprint $table) {
            $table->bigInteger('site_id')->unsigned();
            $table->bigInteger('language_id')->unsigned();

            $table->foreign('site_id')->references('id')->on('sites')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('language_id')->references('id')->on('languages')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_temp')->dropIfExists('site_tag');
        Schema::connection('mysql_temp')->dropIfExists('site_language');
    }
}
