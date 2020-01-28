<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_temp')->create('images', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url');
            $table->bigInteger('site_id')->unsigned();
            $table->timestamps();

            $table->foreign('site_id')->references('id')->on('sites')
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
        Schema::connection('mysql_temp')->table('images', function(Blueprint $table) {
            $table->dropForeign(['site_id']);
            $table->dropIfExists();
        });
    }
}
