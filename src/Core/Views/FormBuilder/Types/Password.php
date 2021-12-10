<?php

namespace Miqu\Core\Views\FormBuilder\Types;

use eftec\bladeone\BladeOne;
use Miqu\Core\Views\FormBuilder\Field;

class Password extends Field
{
    public function render(BladeOne $blade): string
    {
        return $blade->run('password', $this->getConfiguration());
    }
}