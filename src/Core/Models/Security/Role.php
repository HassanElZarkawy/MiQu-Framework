<?php

namespace Miqu\Core\Models\Security;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Miqu\Core\Models\User;
use Miqu\Core\Views\FormBuilder\Types\Relation;

class Role extends Model
{
    protected $table = 'roles';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $formBuilder = [
        'name' => [
            'type' => 'text',
            'required' => true,
            'width' => 8
        ],
        'slug' => [
            'type' => 'text',
            'required' => true,
            'width' => 4,
        ],
        'description' => [
            'type' => 'textArea',
            'rows' => 7
        ],
        'permissions' => [
            'type' => 'relation',
            'model' => Permission::class,
            'key' => 'id',
            'value' => 'name',
            'display' => Relation::DISPLAY_MULTI_OPTIONS,
        ],
    ];

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