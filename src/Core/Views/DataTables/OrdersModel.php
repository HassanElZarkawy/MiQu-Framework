<?php

namespace Miqu\Core\Views\DataTables;

trait OrdersModel
{
    public function processOrderBy()
    {
        $this->instance->orderBy($this->orderBy, $this->direction);
    }
}