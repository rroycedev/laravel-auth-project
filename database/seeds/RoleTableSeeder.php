<?php
use App\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    public function run()
    {
        $role_administrator = Role::where("name", "Application Administrator")->first();

        if (!$role_administrator) {
            $role_administrator = new Role();
        }

        $role_administrator->name = "Application Administrator";
        $role_administrator->description = "Administers the web application";
        $role_administrator->is_system_object = 1;
        $role_administrator->save();

        $role_useradmin = Role::where("name", "User Administrator")->first();

        if (!$role_useradmin) {
            $role_useradmin = new Role();
        }

        $role_useradmin->name = "User Administrator";
        $role_useradmin->description = "Administers users for this web application";
        $role_useradmin->is_system_object = 1;
        $role_useradmin->save();
    }
}
