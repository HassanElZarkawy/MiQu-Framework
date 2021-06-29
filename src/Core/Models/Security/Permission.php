<?php

namespace Miqu\Core\Models\Security;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';

    protected $primaryKey = 'id';

    protected $guarded = [];
}