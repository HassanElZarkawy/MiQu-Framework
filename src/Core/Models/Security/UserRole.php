<?php

namespace Miqu\Core\Models\Security;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'user_roles';

    protected $primaryKey = 'user_id';

    protected $guarded = [];
}