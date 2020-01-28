<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_public')->create('sites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url')->unique();
            $table->bigInteger('type_id')->unsigned();
            $table->decimal('rating')->nullable();
            $table->string('thumbnail', 12);
            $table->string('hash', 12);
            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('types')
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
        Schema::connection('mysql_public')->table('sites', function(Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropIfExists();
        });
    }
}
