<?php

namespace Miqu\Core\Views\FormBuilder\Types;

use eftec\bladeone\BladeOne;
use Exception;
use Miqu\Core\Views\FormBuilder\Field;

class File extends Field
{
    /**
     * @param BladeOne $blade
     * @return string
     * @throws Exception
     */
    public function render(BladeOne $blade): string
    {
        $config = $this->getConfiguration();
        return $blade->run('file', $config);
    }
}