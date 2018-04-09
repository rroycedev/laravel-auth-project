<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    protected $table = 'role_user';

    public function users()
    {
        return $this->belongsToMany(User::class, $this->table);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, $this->table);
    }
}
