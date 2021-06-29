<?php

namespace Miqu\Core\Models\Security;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Miqu\Core\Models\User;

class Token extends Model
{
    protected $table = 'tokens';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $dates = [ 'expires_at' ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}