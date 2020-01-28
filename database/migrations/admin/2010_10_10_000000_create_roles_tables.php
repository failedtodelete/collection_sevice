<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        // Create table for storing roles
        Schema::connection('mysql_admin')->create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create table for storing permission types
        Schema::connection('mysql_admin')->create('permission_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create table for storing permissions
        Schema::connection('mysql_admin')->create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('type_id')->unsigned();
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->boolean('main')->default(false);
            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('permission_types')
                ->onUpdate('cascade')->onDelete('restrict');
        });

        // Create table for associating permissions to roles (Many-to-Many)
        Schema::connection('mysql_admin')->create('permission_role', function (Blueprint $table) {
            $table->bigInteger('permission_id')->unsigned();
            $table->bigInteger('role_id')->unsigned();

            $table->foreign('permission_id')->references('id')->on('permissions')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::connection('mysql_admin')->drop('permission_role');
        Schema::connection('mysql_admin')->table('permissions', function(Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropIfExists();
        });
        Schema::connection('mysql_admin')->drop('permission_types');
        Schema::connection('mysql_admin')->drop('roles');
    }
}
