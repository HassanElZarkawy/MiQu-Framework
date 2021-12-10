<?php

namespace Miqu\Core\Views\FormBuilder\Types;

use eftec\bladeone\BladeOne;
use Miqu\Core\Views\FormBuilder\Field;

class Time extends Field
{
    public function render(BladeOne $blade): string
    {
        return $blade->run('time', $this->getConfiguration());
    }
}