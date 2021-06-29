<?php

namespace Miqu\Core\Models\Security;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Miqu\Core\Models\User;

class Role extends Model
{
    protected $table = 'roles';

    protected $primaryKey = 'id';

    protected $guarded = [];

    /**
     * @return HasManyThrough
     */
    public function permissions(): HasManyThrough
    {
        return $this->hasManyThrough(Permission::class, RolePermission::class);
    }

    /**
     * @return HasManyThrough
     */
    public function users() : HasManyThrough
    {
        return $this->hasManyThrough(User::class, UserRole::class);
    }

    /**
     * Checks if the current role has a specific permission
     * @param string $slug
     * @return bool
     * @throws Exception
     */
    public function hasPermission(string $slug): bool
    {
        $permission = Permission::where('slug', $slug)->getOne();
        return RolePermission::where('role_id', $this->id)->where('permission_id', $permission->id)->getOne() !== null;
    }
}