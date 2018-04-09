<?php
namespace App;

use Adldap\Laravel\Traits\HasLdapUser;
use App\Helpers\DatabaseAuthHelper;
use App\Helpers\LdapHelper;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class LdapDbUser extends Authenticatable implements CanResetPasswordContract
{
    use Notifiable;
    use CanResetPassword;
    use HasLdapUser;

    protected $table = 'users';

    protected $primaryKey = 'uidnumber';

    protected $fillable = array(
        'uid', 'first_name', 'last_name', 'email', 'password', 'is_system_object', 'role_id', 'roleName',
    );

    protected $hidden = array(
        'password', 'remember_token',
    );

    public function getAuthIdentifierName()
    {
        return 'uid';
    }

    public function getUsername()
    {
        return $this->attributes['uid'];
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

    public function role()
    {
        return $this->belongsToMany(Role::class, 'roles');
    }

    public function getRoles()
    {
        $roles = $this->roles()->get();

        $this->attributes['roles'] = $this->roles;

        return $roles;
    }

    public function getRoleId()
    {
        return $this->attributes['role_id'];
    }

    public function getRoleName()
    {
        $this->roleName = LdapHelper::getRoleName($this->getRoleId());
    }

    public function getRoleInfo()
    {
        $this->roleName = DatabaseAuthHelper::getRoleName($this->getRoleId());

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

    /**
     * @param string|array $roles
     */
    public function authorizeRoles($roles)
    {
        if (is_array($roles)) {
            return $this->hasAnyRole($roles) ||
            abort(401, 'This action is unauthorized.');
        }

        return $this->hasRole($roles) ||
        abort(401, 'This action is unauthorized.');
    }

    /**
     * Check multiple roles.
     *
     * @param array $roles
     */
    public function hasAnyRole($roles)
    {
        return null !== $this->roles()->whereIn(‘name’, $roles)->first();
    }

    /**
     * Check one role.
     *
     * @param string $role
     */
    public function hasRole($role)
    {
        return null !== $this->roles()->where(‘name’, $role)->first();
    }
}
