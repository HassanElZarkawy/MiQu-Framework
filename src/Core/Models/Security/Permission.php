<?php

namespace Miqu\Core\Models\Security;

use Illuminate\Database\Eloquent\Model;
use Miqu\Core\Views\FormBuilder\Field;

class Permission extends Model
{
    protected $table = 'permissions';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function formDefinitions(): array
    {
        return [
            Field::builder('name')->text()->required()->width(8),
            Field::builder('slug')->text()->required()->width(4),
            Field::builder('description')->textArea()->rows(7),
        ];
    }
}