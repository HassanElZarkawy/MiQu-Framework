<?php

namespace Miqu\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Miqu\Core\Models\Security\Role;
use Miqu\Core\Models\Security\Token;
use Miqu\Core\Models\Traits\HasPermission;
use Miqu\Core\Models\Traits\HasRole;
use Miqu\Core\Models\Traits\InteractsWithMedia;
use Miqu\Core\Models\Traits\Notifiable;
use Miqu\Core\Views\FormBuilder\Types\Relation;

class User extends Model
{
    use InteractsWithMedia, HasPermission, HasRole, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $hidden = [
        'password', 'username', 'type'
    ];

    public $formBuilder = [
        'name' => [
            'type' => 'text',
            'required' => true,
            'width' => 12
        ],
        'username' => [
            'type' => 'text',
            'width' => 6
        ],
        'email' => [
            'type' => 'email',
            'width' => 6
        ],
        'password' => 'password',
        'type' => [
            'type' => 'options',
            'options' => [
                'admin' => 'Admin',
                'subscriber' => 'Subscriber',
            ]
        ],
        'status' => [
            'type' => 'options',
            'required' => true,
            'options' => [
                'active' => 'Active',
                'suspended' => 'Suspended'
            ],
        ],
        'roles' => [
            'type' => 'relation',
            'relation' => 'single',
            'model' => Role::class,
            'key' => 'id',
            'value' => 'name',
            'display' => Relation::DISPLAY_MULTI_OPTIONS,
        ],
        'image' => [
            'type' => 'file',
            'label' => 'Image',
            'width' => 12,
        ],
    ];

    /**
     * @return HasOne
     */
    public function token(): HasOne
    {
        return $this->hasOne(Token::class);
    }

    /**
     * @return HasOne
     */
    public function resetToken(): HasOne
    {
        return $this->hasOne(PasswordResetToken::class);
    }
}