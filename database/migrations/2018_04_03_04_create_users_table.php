<?php
use App\Helpers\AuthHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            if (AuthHelper::AuthDriverName() == 'eloquent') {
                $table->increments('id');
                $table->string('username', 60)->unique();
            } else {
                $table->increments('uidnumber');
                $table->string('uid', 60)->unique();
            }

            $table->string('first_name', 60);
            $table->string('last_name', 60);
            $table->string('email')->nullable();
            $table->string('password');
            $table->integer('role_id')->unsigned();
            $table->rememberToken();
            $table->tinyinteger('is_system_object')->default('0');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));

            echo "Foreign key on role table\n";

            $table->foreign("role_id")->references("id")->on("role");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
