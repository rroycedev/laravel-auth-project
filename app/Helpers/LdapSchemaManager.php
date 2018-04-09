<?php
namespace App\Helpers;

use Adldap\Laravel\Facades\Adldap;
use App\Helpers\LdapHelper;
use App\LdapDbUser;
use App\Permission;
use App\Role;
use App\RolePermission;
use App\User;
use Illuminate\Support\Facades\DB;

class LdapSchemaManager
{
    public static function initLdapObjects($command)
    {
        $ouName = env('ADLDAP_AUTH_APP_OU', 'Application Organization');

        $command->info("init-ldap: Creating organizational unit '$ouName'....");

        LdapHelper::createOu($ouName);

        $command->info("init-ldap: Creating LDAP role 'Application Administrator'....");

        $role = LdapHelper::getRole("Application Administrator");

        if ($role) {
            $role->delete();
        }

        $appAdminGidNumber = LdapHelper::createRole($ouName, "Application Administrator");

        $command->info("init-ldap: Creating LDAP role 'User Administrator'....");

        $role = LdapHelper::getRole("User Administrator");

        if ($role) {
            $role->delete();
        }

        $userAdminGidNumber = LdapHelper::createRole($ouName, "User Administrator");

        DB::statement("SET FOREIGN_KEY_CHECKS=0;");

        $command->info("init-ldap: Truncating database table 'users'....");
        User::truncate();
        $command->info("init-ldap: Truncating database table 'role_permssion'....");
        RolePermission::truncate();
        $command->info("init-ldap: Truncating database table 'role'....");
        Role::truncate();
        $command->info("init-ldap: Truncating database table 'permssion'....");
        Permission::truncate();

        DB::statement("SET FOREIGN_KEY_CHECKS=1;");

        $command->info("init-ldap: Inserting LDAP role 'Application Administrator' information to database table 'role'....");

        $role = new Role();

        $role->id = $appAdminGidNumber;
        $role->name = "Application Administrator";
        $role->description = "Administers the web application";
        $role->is_system_object = 1;

        $role->save();

        $command->info("init-ldap: Inserting LDAP role 'User Administrator' information to database table 'role'....");

        $role = new Role();

        $role->id = $userAdminGidNumber;
        $role->name = "User Administrator";
        $role->description = "Administers users for this web application";
        $role->is_system_object = 1;

        $role->save();

        $command->info("init-ldap: Inserting permission 'Super user' to database table 'permission'....");

        $permission = new Permission();
        $permission->name = "Super user";
        $permission->description = "Has full access to all functionality";

        $permission->save();

        $superUserPermissionId = $permission->id;

        $command->info("init-ldap: Inserting permission 'Maintain users' to database table 'permission'....");

        $permission = new Permission();
        $permission->name = "Maintain users";
        $permission->description = "Allows a user to create, edit, or delete application users";

        $permission->save();

        $maintainUsersPermissionId = $permission->id;

        $command->info("init-ldap: Inserting role/permission mapping for role 'Application Administrator'....");

        $params = array("role_id" => $appAdminGidNumber, "permission_id" => $superUserPermissionId);

        $rolePermission = new RolePermission();

        $rolePermission->role_id = $appAdminGidNumber;
        $rolePermission->permission_id = $superUserPermissionId;

        $rolePermission->save();

        $command->info("init-ldap: Inserting role/permission mapping for role 'User Administrator'....");

        $rolePermission = new RolePermission();

        $rolePermission->role_id = $userAdminGidNumber;
        $rolePermission->permission_id = $maintainUsersPermissionId;

        $rolePermission->save();

        $username = env('APP_ADMIN_USERNAME', 'appadmin');
        $firstName = env('APP_ADMIN_FIRST_NAME', 'Application');
        $lastName = env('APP_ADMIN_LAST_NAME', 'Administrator');
        $email = env('APP_ADMIN_EMAIL', 'appadmin@example.com');
        $password = env('APP_ADMIN_PASSWORD', 'appadmin');

        $user = LdapHelper::getLdapUser($username);

        if ($user) {
            $command->info("init-ldap: User $username exists.  Removing it first");
            $user->delete();
        }

        $command->info("init-ldap: Creating user $username....");

        $userId = LdapHelper::createUser($username, $firstName, $lastName, $email, $password, $appAdminGidNumber);

        if (AuthHelper::authDriverName() == 'eloquent') {
            $userIdColname = "id";
            $usernameColname = "username";
            $admin = User::where($usernameColname, $username)->first();
            if (!$admin) {
                $admin = new User();
            }
        } else {
            $userIdColname = "uidnumber";
            $usernameColname = "uid";
            $admin = LdapDbUser::where($usernameColname, $username)->first();
            if (!$admin) {
                $admin = new LdapDbUser();
            }
        }

        $admin->$userIdColname = $userId;
        $admin->$usernameColname = $username;
        $admin->first_name = $firstName;
        $admin->last_name = $lastName;
        $admin->email = $email;
        $admin->password = bcrypt($password);
        $admin->role_id = $appAdminGidNumber;
        $admin->is_system_object = 1;
        $admin->save();

        $command->info("init-ldap: Finished creating LDAP objects successfully");
    }

    public static function initLdapAdminUser($username, $password, $command)
    {
        $user = Adldap::search()->where("cn", $username)->first();

        if (!$user) {
            $operation = "add";
        } else {
            $operation = "change";
        }

        try {
            LdapHelper::createAdminUser($username, $password);
        } catch (\Exception $ex) {
            $command->error("init-ldap: Error: " . $ex->getMessage());
            return;
        }

        if ($operation == "add") {
            $command->info("init-ldap: LDAP administrator $username has been created in the organizational unit '" . env('ADLDAP_AUTH_APP_OU') . "'");
        } else {
            $command->info("init-ldap: LDAP administrator $username  password has been updated in the organizational unit '" . env('ADLDAP_AUTH_APP_OU') . "'");
        }
    }

}
