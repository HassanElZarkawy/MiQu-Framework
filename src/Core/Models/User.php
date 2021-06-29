<?php

namespace Miqu\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Miqu\Core\Models\Security\Token;
use Miqu\Core\Models\Traits\HasPermission;
use Miqu\Core\Models\Traits\HasRole;
use Miqu\Core\Models\Traits\InteractsWithMedia;
use Miqu\Core\Models\Traits\Notifiable;

class User extends Model
{
    use InteractsWithMedia, HasPermission, HasRole, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $hidden = [
        'password', 'username', 'type'
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