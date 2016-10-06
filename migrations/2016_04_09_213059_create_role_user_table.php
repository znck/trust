<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRoleUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'role_user',
            function (Blueprint $table) {
                $table->unsignedInteger('user_id')->index();
                $table->unsignedInteger('role_id')->index();
                $table->timestamps();

                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'role_user',
            function (Blueprint $table) {
                $table->dropForeign(['role_id']);
                $table->dropForeign(['user_id']);
                $table->drop();
            }
        );
    }
}
