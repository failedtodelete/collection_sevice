<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicManyToManyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_public')->create('site_tag', function(Blueprint $table) {
            $table->bigInteger('site_id')->unsigned();
            $table->bigInteger('tag_id')->unsigned();

            $table->foreign('site_id')->references('id')->on('sites')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')
                ->onUpdate('cascade')->onDelete('restrict');
        });

        Schema::connection('mysql_public')->create('site_language', function(Blueprint $table) {
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
        Schema::connection('mysql_public')->table('site_tag', function(Blueprint $table) {
            $table->dropForeign(['site_id']);
            $table->dropForeign(['tag_id']);
            $table->dropIfExists();
        });
        Schema::connection('mysql_public')->table('site_language', function(Blueprint $table) {
            $table->dropForeign(['site_id']);
            $table->dropForeign(['language_id']);
            $table->dropIfExists();
        });
    }
}
