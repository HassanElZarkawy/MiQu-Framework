<?php /** @noinspection PhpUnused */

namespace Miqu\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'settings' => 'array'
    ];

    protected $attributes = [
        'is_read' => 0,
        'is_sent' => 0,
        'settings' => []
    ];

    protected $hidden = [
        'notifiable_type', 'notifiable_id'
    ];

    public function markAsRead()
    {
        $this->update([
            'is_read' => 1
        ]);
    }

    public function markAsUnread()
    {
        $this->update([
            'is_read' => 0
        ]);
    }
}