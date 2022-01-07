<?php

namespace Miqu\Core\Views\FormBuilder\Types;

use eftec\bladeone\BladeOne;
use Exception;
use Miqu\Core\Views\FormBuilder\FieldBuilder;
use Miqu\Core\Views\FormBuilder\FieldConfigurations;
use Miqu\Core\Views\FormBuilder\FieldMembers;
use Miqu\Core\Views\FormBuilder\IField;

class Select implements IField
{
    use FieldMembers, FieldConfigurations, FieldBuilder;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var string|null
     */
    private $assistText = '--Select One--';

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
        return 'select';
    }

    /**
     * Options should be an assoc. array. Keys being the value of the control, and array values are the text displayed.
     * @param array $options
     * @return Select
     */
    public function options(array $options): Select
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Sets an additional option with no value assigned to it. Handy if you don't have a default value
     * @param string $text
     * @return Select
     */
    public function SelectText(string $text): Select
    {
        $this->assistText = $text;
        return $this;
    }

    public function setConfiguration(array $configuration): IField
    {
        static::applyConfiguration($this, $configuration);
        if (isset($configuration['options']) && is_array($configuration['options']))
            $this->options = $configuration['options'];
        if (isset($configuration['assistText']))
            $this->SelectText($configuration['assistText']);
        return $this;
    }

    public function getConfiguration(): array
    {
        $config = static::getDefaultConfiguration($this);
        $config['options'] = $this->options;
        return $config;
    }

    /**
     * @param BladeOne $blade
     * @return string
     * @throws Exception
     */
    public function render(BladeOne $blade): string
    {
        $config = $this->getConfiguration();
        $config['options'] = $this->options;
        $config['assistText'] = $this->assistText;
        return $blade->run('select', $config);
    }
}