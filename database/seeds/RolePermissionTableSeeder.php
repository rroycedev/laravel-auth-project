<?php
use App\Permission;
use App\Role;
use App\RolePermission;
use Illuminate\Database\Seeder;

class RolePermissionTableSeeder extends Seeder
{
    public function run()
    {
        $permission = Permission::where("name", "Super user")->first();

        $role = Role::where("name", "Application Administrator")->first();

        $rolePermission = new RolePermission();

        $rolePermission->role_id = $role->id;
        $rolePermission->permission_id = $permission->id;

        $rolePermission->save();

        $permission = Permission::where("name", "Maintain users")->first();

        $role = Role::where("name", "User Administrator")->first();

        $rolePermission = new RolePermission();

        $rolePermission->role_id = $role->id;
        $rolePermission->permission_id = $permission->id;

        $rolePermission->save();
    }
}
