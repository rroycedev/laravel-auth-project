<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Permssion extends Migration
{
    public function up()
    {
        Schema::create("permission", function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->increments("id");
            $table->string("name", 60)->unique();
            $table->string("description", 100);
        });
    }

    public function down()
    {
        Schema::dropIfExists("permission");
    }
}
