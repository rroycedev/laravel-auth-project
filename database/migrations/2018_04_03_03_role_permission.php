<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RolePermission extends Migration
{
    public function up()
    {
        Schema::create("role_permission", function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->increments("id");
            $table->integer("role_id")->unsigned();
            $table->integer("permission_id")->unsigned();
            $table->foreign("role_id")->references("id")->on("role");
            $table->foreign("permission_id")->references("id")->on("permission");
        });
    }

    public function down()
    {
        Schema::dropIfExists("role_permission");
    }
}
