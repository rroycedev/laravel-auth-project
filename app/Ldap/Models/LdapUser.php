<?php
namespace App\Ldap\Models;

use Adldap\Laravel\Traits\HasLdapUser;
use Adldap\Models\User;
use App\Role;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class LdapUser extends User implements CanResetPasswordContract
{
    use Notifiable;
    use CanResetPassword;
    use HasLdapUser;

    protected $table = 'users';

    protected $primaryKey = 'uidnumber';

    /* The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = array(
        'uidnumber', 'uid', 'first_name', 'last_name', 'email', 'password', 'is_system_object', 'roles',
    );

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = array(
        'password', 'remember_token',
    );

    public function getEmailForPasswordReset()
    {
        \Log::info('LDAPUSER Returning email for reset password of [' . $this->attributes['mail'][0] . ']');

        return $this->attributes['mail'][0];
    }

    public function routeNotificationFor($driver, $notification = null)
    {
        \Log::info('LdapUser::routeNotificationFor - At top');

        if (method_exists($this, $method = 'routeNotificationFor' . Str::studly($driver))) {
            \Log::info('LdapUser::routeNotificationFor - returning');

            return $this->{$method}($notification);
        }

        switch ($driver) {
            case 'database':
                return $this->notifications();
            case 'mail':
                \Log::info('LdapUser::routeNotificationFor - Returning email ' . $this->getEmailForPasswordReset());

                return $this->getEmailForPasswordReset();
            case 'nexmo':
                return $this->phone_number;
        }
    }

    public function getAuthIdentifierName()
    {
        return 'uidnumber';
    }

    public function getUUID()
    {
        $str = $this->attributes['entryuuid'];

        $num = base_convert(str_replace('-', '', $str), 16, 10);

        echo "UUID = [$str]<br>";
        echo 'This class ' . get_class($this) . '<br>';
        echo 'ToInt = ' . $num . ' <br>';

        return $num;
    }

    public function getAuthIdentifier()
    {
        return $this->attributes['uidnumber'];
    }

    public function getUsername()
    {
        echo 'User: Returning username ...<br>';

        return $this->attributes['uid'][0];
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
