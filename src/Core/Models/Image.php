<?php

namespace Miqu\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    protected $table = 'images';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $hidden = [
        'object_id', 'object_type'
    ];

    /**
     * @return MorphTo
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'object_type', 'object_id');
    }
}