<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasswordResetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
/*
            if (AuthHelper::AuthDriverName() == 'eloquent') {
                $table->string('username', 60)->unique();
            } else {
                $table->increments('uidnumber');
                $table->string('uid', 60)->unique();
            }
*/
		$table->string('email', 255)->unique();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
/*
            if (AuthHelper::AuthDriverName() == 'eloquent') {
                $table->index('username');
            } else {
                $table->index('uid');
            }
*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('password_resets');
    }
}
