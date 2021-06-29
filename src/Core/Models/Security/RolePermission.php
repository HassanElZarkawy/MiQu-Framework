<?php

namespace Miqu\Core\Models\Security;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = 'role_permissions';

    protected $primaryKey = 'role_id';

    protected $guarded = [];
}