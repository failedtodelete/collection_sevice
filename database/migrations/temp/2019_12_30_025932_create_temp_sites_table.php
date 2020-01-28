<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_temp')->create('sites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('link_id')->unsigned();
            $table->decimal('rating')->nullable();
            $table->string('thumbnail', 12)->nullable();
            $table->string('hash', 12);
            $table->integer('status')->default(1);
            $table->bigInteger('creator_id');
            $table->timestamps();

            $table->foreign('link_id')->references('id')->on('links')
                ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_temp')->table('sites', function(Blueprint $table) {
            $table->dropForeign(['link_id']);
            $table->dropIfExists();
        });
    }
}
