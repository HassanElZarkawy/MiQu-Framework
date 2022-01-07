<?php

namespace Miqu\Core\Models\Traits;

use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Miqu\Core\Models\Security\Role;
use Miqu\Core\Models\Security\UserRole;

trait HasRole
{
    /**
     * @var array
     */
    private $roles = [];

    /**
     * @throws Exception
     */
    private function initRoles()
    {
        if ( count( $this->roles ) === 0 )
        {
            $userRoles = UserRole::query()->where( 'user_id', $this->id )->pluck( 'role_id' )->all();
            if ( count( $userRoles ) === 0 )
                $userRoles = [ 0 ];

            $this->roles = Role::query()->whereIn( 'id', $userRoles )->get()->all();
        }
    }

    /**
     * @return BelongsToMany
     * @throws Exception
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    /**
     * @param string $name
     * @return bool
     * @throws Exception
     */
    public function hasRole(string $name ): bool
    {
        $this->initRoles();
        return collect($this->roles)->first(function($item) use ( $name ) {
            return $item->name === $name;
        }) !== null;
    }

    /**
     * @param array $roles
     * @return bool
     * @throws Exception
     */
    public function anyRole(array $roles ): bool
    {
        $this->initRoles();
        return collect($roles)->first(function($role) {
            return $this->hasRole($role);
        }) !== null;
    }

    /**
     * @param string $role_name
     * @throws Exception
     */
    public function assignToRole(string $role_name)
    {
        $this->initRoles();
        $role = collect($this->roles)->first(function($index, $item) use ( $role_name ) {
            return $item->name === $role_name;
        });

        if ( ! $role )
        {
            $role = Role::create([
                'name' => $role_name,
                'slug' => (string)string($role_name)->slugify(),
                'description' => ''
            ]);
        }

        $relation = UserRole::where( 'user_id', $this->id )->where( 'role_id', $role->id )->getOne();

        if ( ! $relation )
        {
            UserRole::create([
                'user_id' => $this->id,
                'role_id' => $role->id
            ]);
        }
    }
}