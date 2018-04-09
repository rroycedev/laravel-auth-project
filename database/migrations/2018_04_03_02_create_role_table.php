<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleTable extends Migration
{
    public function up()
    {
        Schema::create("role", function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->increments("id");
            $table->string("name");
            $table->string("description");
            $table->tinyinteger("is_system_object")->default('0');
            $table->timestamps();
            $table->unique("name");
        });
    }

    public function down()
    {
        Schema::dropIfExists("role");
    }
}
