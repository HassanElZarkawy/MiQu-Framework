<?php

namespace Miqu\Core\Views\FormBuilder\Types;

use eftec\bladeone\BladeOne;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Miqu\Core\Views\FormBuilder\Field;
use ReflectionException;

class Relation extends Field
{
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
    private $mode = 'options';

    /**
     * @var array
     */
    private $selectedValues = [];

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function applyConfiguration(array $configuration): Field
    {
        parent::applyConfiguration($configuration);
        if (isset($configuration['model']))
            $this->instance = app()->make($configuration['model']);

        if (isset($configuration['relation']))
            $this->relation = $configuration['relation'];

        if (isset($configuration['display']))
            $this->mode = $configuration['display'];

        if (isset($configuration['selected_values']))
            $this->selectedValues = $configuration['selected_values'];

        if (isset($configuration['key']))
            $this->key = $configuration['key'];
        else
            throw new Exception('Key must be set for relationship');

        if (isset($configuration['value']))
            $this->value = $configuration['value'];
        else
            throw new Exception('Value must be set for relationship');

        return $this;
    }

    public function render(BladeOne $blade): string
    {
        $config = $this->getConfiguration();
        $options = [];
        if ($this->instance !== null)
        {
            $options = $this->instance->query()->get([ $this->key, $this->value ])->mapWithKeys(function($record) {
                return [ $record->{$this->key} => $record->{$this->value} ];
            })->all();
        }
        $config['options'] = $options;
        $config['assistText'] = '--Select One--';
        $config['selected_values'] = $this->selectedValues;
        return $blade->run($this->mode, $config);
    }
}