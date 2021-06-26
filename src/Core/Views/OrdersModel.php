<?php

namespace Miqu\Core\Views;

trait OrdersModel
{
    public function processOrderBy()
    {
        $this->instance->orderBy($this->orderBy, $this->direction);
    }
}