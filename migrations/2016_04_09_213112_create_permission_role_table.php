<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePermissionRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'permission_role',
            function (Blueprint $table) {
                $table->unsignedInteger('permission_id')->index();
                $table->unsignedInteger('role_id')->index();
                $table->timestamps();

                $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
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
            'permission_role',
            function (Blueprint $table) {
                $table->dropForeign(['permission_id']);
                $table->dropForeign(['role_id']);
                $table->drop();
            }
        );
    }
}
