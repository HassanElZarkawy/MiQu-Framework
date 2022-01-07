<?php

namespace Miqu\Core\Views\FormBuilder\Types;

use eftec\bladeone\BladeOne;
use Exception;
use Miqu\Core\Views\FormBuilder\FieldBuilder;
use Miqu\Core\Views\FormBuilder\FieldConfigurations;
use Miqu\Core\Views\FormBuilder\FieldMembers;
use Miqu\Core\Views\FormBuilder\IField;

class Url implements IField
{
    use FieldMembers, FieldConfigurations, FieldBuilder;

    /**
     * @param string $fieldName
     */
    public function __construct(string $fieldName)
    {
        $this->property = $fieldName;
        $this->type = static::getType();
    }

    /**
     * @param string $property
     * @return null
     */
    public function get(string $property)
    {
        if (property_exists($this, $property))
            return $this->{$property};
        return null;
    }

    /**
     * @return string
     */
    public static function getType(): string
    {
        return 'url';
    }

    /**
     * @var string|null
     */
    private $prepend = null;

    /**
     * @param string $text
     * @return $this
     */
    public function prepend(string $text): Url
    {
        $this->prepend = $text;
        return $this;
    }

    /**
     * @param array $configuration
     * @return $this
     */
    public function setConfiguration(array $configuration): IField
    {
        static::applyConfiguration($this, $configuration);
        if (isset($configuration['prepend']))
            $this->prepend($configuration['prepend']);
        return $this;
    }

    /**
     * @param BladeOne $blade
     * @return string
     * @throws Exception
     */
    public function render(BladeOne $blade): string
    {
        $config = $this->getConfiguration();
        $config['prepend'] = $this->prepend;
        return $blade->run('url', $config);
    }
}