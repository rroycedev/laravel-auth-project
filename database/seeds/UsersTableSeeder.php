<?php
use App\Helpers\AuthHelper;
use App\LdapDbUser;
use App\Role;
use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_admin = Role::where("name", "Application Administrator")->first();

        if (AuthHelper::AuthDriverName() == 'eloquent') {
            $usernameColname = "username";
            $admin = User::where($usernameColname, "admin")->first();
            if (!$admin) {
                $admin = new User();
            }
        } else {
            $usernameColname = "uid";
            $admin = LdapDbUser::where($usernameColname, "admin")->first();
            if (!$admin) {
                $admin = new LdapDbUser();
            }
        }

        $admin->$usernameColname = env('APP_ADMIN_USERNAME', 'appadmin');
        $admin->first_name = env('APP_ADMIN_FIRST_NAME', 'Application');
        $admin->last_name = env('APP_ADMIN_LAST_NAME', 'Administrator');
        $admin->email = env('APP_ADMIN_EMAIL', 'appadmin@example.com');
        $admin->password = bcrypt(env('APP_ADMIN_PASSWORD', 'appadmin'));
        $admin->role_id = $role_admin->id;
        $admin->is_system_object = 1;
        $admin->save();
    }
}
