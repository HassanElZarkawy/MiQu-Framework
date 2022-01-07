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
use Miqu\Core\Views\FormBuilder\Field;
use Miqu\Core\Views\FormBuilder\Types\Relation;

class User extends Model
{
    use InteractsWithMedia, HasPermission, HasRole, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function formDefinitions(): array
    {
        return [
            Field::builder('name')->text()->required()->width(6),
            Field::builder('username')->text()->required()->width(6),
            Field::builder('email')->email()->required()->helpText('Sample help text'),
            Field::builder('password')->password()->required(),
            Field::builder('type')->select()->options([
                'admin' => 'Admin',
                'subscriber' => 'Subscriber',
            ]),
            Field::builder('status')->select()->options([
                'active' => 'Active',
                'suspended' => 'Suspended'
            ])->required(),
            Field::builder('roles')->relation()->model(Role::class)
                ->relationKey('id')->relationValue('name')->displayMode(Relation::DISPLAY_MULTI_OPTIONS),
            Field::builder('image')->file()->label('Image')->width(12),
        ];
    }

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