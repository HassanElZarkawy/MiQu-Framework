<?php

namespace Miqu\Core\Models\Security;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $formBuilder = [
        'name' => [
            'type' => 'text',
            'required' => true,
            'width' => 8,
        ],
        'slug' => [
            'type' => 'text',
            'required' => true,
            'width' => 4,
        ],
        'description' => [
            'type' => 'textArea',
            'rows' => 7,
        ],
    ];
}