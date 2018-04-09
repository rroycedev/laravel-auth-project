<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    public $timestamps = false;
    protected $table = 'role_permission';

    public function permissions()
    {
        return Permission::where('id', $this->attributes['permission_id'])->first();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, $this->table)->withPivot('name');
    }
}
