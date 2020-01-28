<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinksTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_temp')->create('links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url');
            $table->bigInteger('creator_id');
            $table->bigInteger('moderator_id');
            $table->bigInteger('type_id')->unsigned();
            $table->integer('status')->default(0);
            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('types')
                ->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_temp')->table('links', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropIfExists();
        });
    }
}
