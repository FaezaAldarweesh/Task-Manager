<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Define the many-to-many relationship between roles and users.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user')->withTimestamps();
    }

    /**
     * Define the many-to-many relationship between roles and permissions.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role')->withTimestamps();
    }

    /**
     * Assign multiple permissions to the role by permission's IDs.

     * @param array $permissionIds
     * @return void
     */
    public function assignPermissions(array $permissionIds)
    {
        $this->permissions()->syncWithoutDetaching($permissionIds);
    }

    #TODO use it
    /**
     * Revoke a specific permission from a role.
     * 
     * @param $permissionId
     * @return void
     */
    public function removePermission($permissionId)
    {
        $this->permissions()->detach($permissionId);
    }
}
