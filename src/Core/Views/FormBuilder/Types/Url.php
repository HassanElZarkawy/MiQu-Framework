<?php

namespace Miqu\Core\Views\FormBuilder\Types;

use eftec\bladeone\BladeOne;
use Miqu\Core\Views\FormBuilder\Field;

class Url extends Field
{
    /**
     * @var string|null
     */
    private $prepend = null;

    public function prepend(string $text): Url
    {
        $this->prepend = $text;
        return $this;
    }

    public function applyConfiguration(array $configuration): Field
    {
        parent::applyConfiguration($configuration);
        if (isset($configuration['prepend']))
            $this->prepend($configuration['prepend']);
        return $this;
    }

    public function render(BladeOne $blade): string
    {
        $config = $this->getConfiguration();
        $config['prepend'] = $this->prepend;
        return $blade->run('url', $config);
    }
}