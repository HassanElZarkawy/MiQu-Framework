<?php

namespace Miqu\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'images';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $hidden = [
        'object_id', 'object_type'
    ];
}