<?php
use App\Permission;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission = new Permission();

        $permission->name = "Super user";
        $permission->description = "Has full access to all functionality";

        $permission->save();

        $permission = new Permission();

        $permission->name = "Maintain users";
        $permission->description = "Allows a user to create, edit, or delete application users";

        $permission->save();
    }
}
