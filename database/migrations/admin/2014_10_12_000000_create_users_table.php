<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::connection('mysql_admin')->create('user_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('display_name');
            $table->timestamps();
        });

        Schema::connection('mysql_admin')->create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->bigInteger('role_id')->unsigned();
            $table->rememberToken();
            $table->integer('balance')->default(0);
            $table->integer('paid_out')->default(0);
            $table->bigInteger('status_id')->unsigned();
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('status_id')->references('id')->on('user_statuses')
                ->onUpdate('cascade')->onDelete('restrict');
        });

        Schema::connection('mysql_admin')->create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_admin')->dropIfExists('password_resets');
        Schema::connection('mysql_admin')->table('users', function(Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['status_id']);
            $table->dropIfExists();
        });
        Schema::connection('mysql_admin')->dropIfExists('user_statuses');
    }
}
