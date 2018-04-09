<?php
namespace App;

use Adldap\Laravel\Traits\HasLdapUser;
use App\Helpers\DatabaseAuthHelper;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements CanResetPasswordContract
{
    use Notifiable;
    use CanResetPassword;
    use HasLdapUser;

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = array(
        'username', 'first_name', 'last_name', 'email', 'password', 'is_system_object', 'role_id',
        'roleName', 'role', 'rolePermissions',
    );

    protected $hidden = array(
        'password', 'remember_token',
    );

    public function getAuthIdentifierName()
    {
        return 'username';
    }

    public function getUsername()
    {
        return $this->attributes['username'];
    }

    public function getEmailForPasswordReset()
    {
        return $this->attributes['email'];
    }

    public function getEmail()
    {
        return $this->attributes['mail'];
    }

    public function getFirstName()
    {
        return $this->attributes['first_name'];
    }

    public function getLastName()
    {
        return $this->attributes['last_name'];
    }

    public function getRoleId()
    {
        return $this->attributes['role_id'];
    }

    public function getRoleInfo()
    {
        $this->roleName = DatabaseAuthHelper::getRoleName($this->attributes['role_id']);

        $this->role = Role::where('id', $this->getRoleId())->first();
        $allRolePermissions = RolePermission::where('role_id', $this->getRoleId())->get();

        $rolePerms = array();

        foreach ($allRolePermissions as $rolePermission) {
            $rolePerm = $rolePermission;

            $rolePerm->permission_name = $rolePermission->permissions()->name;

            $rolePerms[] = $rolePerm;
        }

        $this->rolePermissions = $rolePerms;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function getRoles()
    {
        $roles = $this->roles()->get();

        $this->attributes['roles'] = $this->roles;

        return $roles;
    }

    public function authorizeRoles($roles)
    {
        if (is_array($roles)) {
            return $this->hasAnyRole($roles) ||
            abort(401, 'This action is unauthorized.');
        }

        return $this->hasRole($roles) ||
        abort(401, 'This action is unauthorized.');
    }

    public function hasAnyRole($roles)
    {
        return null !== $this->roles()->whereIn(‘name’, $roles)->first();
    }

    public function hasRole($role)
    {
        return null !== $this->roles()->where(‘name’, $role)->first();
    }
}
