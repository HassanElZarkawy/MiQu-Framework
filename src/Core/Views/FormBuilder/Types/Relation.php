<?php

namespace Miqu\Core\Views\FormBuilder\Types;

use eftec\bladeone\BladeOne;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Miqu\Core\Views\FormBuilder\FieldBuilder;
use Miqu\Core\Views\FormBuilder\FieldConfigurations;
use Miqu\Core\Views\FormBuilder\FieldMembers;
use Miqu\Core\Views\FormBuilder\IField;
use ReflectionException;

class Relation implements IField
{
    use FieldMembers, FieldConfigurations, FieldBuilder;

    public const DISPLAY_MODE_OPTIONS = 'select';

    public const DISPLAY_MODE_LIST = 'list';

    public const DISPLAY_MULTI_OPTIONS = 'multiSelect';

    /**
     * @var Model|null
     */
    private $instance = null;

    /**
     * @var string|null
     */
    private $relation = null;

    /**
     * @var string|null
     */
    private $key = null;

    /**
     * @var string|null
     */
    private $value = null;

    /**
     * @var string
     */
    private $mode = 'select';

    /**
     * @var array
     */
    private $selectedValues = [];

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
        return 'relation';
    }

    /**
     * @param string $abstract
     * @return $this
     */
    public function model(string $abstract): Relation
    {
        try {
            $this->instance = app()->make($abstract);
        } catch (ReflectionException $e) {
        }
        return $this;
    }

    /**
     * @param string $mode
     * @return $this
     */
    public function displayMode(string $mode): Relation
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function relationKey(string $key): Relation
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function relationValue(string $value): Relation
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function setConfiguration(array $configuration): IField
    {
        static::applyConfiguration($this, $configuration);
        if (isset($configuration['model']))
        {
            if ($configuration['model'] instanceof Model)
                $this->instance = $configuration['model'];
            else
                $this->instance = app()->make($configuration['model']);
        }

        if (isset($configuration['relation']))
            $this->relation = $configuration['relation'];

        if (isset($configuration['display']))
            $this->mode = $configuration['display'];

        if (isset($configuration['selected_values']))
            $this->selectedValues = $configuration['selected_values'];

        if (isset($configuration['relation_key']))
            $this->key = $configuration['relation_key'];

        if (isset($configuration['relation_value']))
            $this->value = $configuration['relation_value'];

        return $this;
    }

    public function getConfiguration(): array
    {
        $config = static::getDefaultConfiguration($this);
        $config['model'] = $this->instance;
        $config['display'] = $this->mode;
        $config['relation'] = $this->relation;
        $config['selected_values'] = $this->selectedValues;
        $config['relation_key'] = $this->key;
        $config['relation_value'] = $this->value;
        return $config;
    }

    /**
     * @throws Exception
     */
    public function render(BladeOne $blade): string
    {
        $config = $this->getConfiguration();
        $options = [];
        if ($this->instance !== null)
        {
            if ($this->value instanceof Collection) {
                $options = $this->value->mapWithKeys(function($record){
                    $this->selectedValues[] = $record->{$this->key};
                    return [ $this->value->{$this->key} => $record->{$this->key} ];
                });
            } else if ($this->value instanceof Model) {
                $options = [$this->value->{$this->key} => $this->value->{$this->key}];
                $this->selectedValues[] = $this->value->{$this->key};
            } else {
                $options = $this->instance->query()->get([ $this->key, $this->value ])->mapWithKeys(function($record) {
                    $this->selectedValues[] = $record->{$this->key};
                    return [ $record->{$this->key} => $record->{$this->value} ];
                })->all();
            }
        }
        $config['options'] = $options;
        $config['assistText'] = '--Select One--';
        if ($this->mode === self::DISPLAY_MODE_OPTIONS || $this->mode === self::DISPLAY_MODE_LIST)
            $config['value'] = count($this->selectedValues) > 0 ? $this->selectedValues[0] : null;
        else
            $config['selected_values'] = $this->selectedValues;
        return $blade->run($this->mode, $config);
    }
}