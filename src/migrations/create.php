<?php
/**
 * This file belongs to Trust.
 *
 * Author: Rahul Kadyan, <hi@znck.me>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RolesAndPermissionsTables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 64)->unique();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->unsignedInteger('role_id');
            $table->unsignedInteger('user_id');
            $table->timestamp('expires')->nullable();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->unsignedInteger('role_id');
            $table->unsignedInteger('permission_id');

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permission_role', function (Blueprint $table) {
            $table->dropForeign('permission_role_role_id_foreign');
            $table->dropForeign('permission_role_permission_id_foreign');
        });

        Schema::table('role_user', function (Blueprint $table) {
            $table->dropForeign('role_user_role_id_foreign');
            $table->dropForeign('role_user_user_id_foreign');
        });

        Schema::drop('permission_role');
        Schema::drop('role_user');
        Schema::drop('permissions');
        Schema::drop('roles');
    }

}