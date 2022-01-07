<?php

namespace Miqu\Core\Views\FormBuilder;

trait FieldConfigurations
{
    public static function applyConfiguration($instance, array $configuration): IField
    {
        if (isset($configuration['label']))
            $instance->label($configuration['label']);
        if (isset($configuration['id']))
            $instance->property = $configuration['id'];
        if (isset($configuration['width']))
            $instance->width((int)$configuration['width']);
        if (isset($configuration['required']) && $configuration['required'])
            $instance->required();
        if (isset($configuration['helpText']))
            $instance->helpText($configuration['helpText']);
        if (isset($configuration['value']))
            $instance->defaultValue($configuration['value']);
        if (isset($configuration['classes']))
            $instance->classes($configuration['classes']);
        return $instance;
    }

    /**
     * @param IField $field
     * @return array
     */
    public static function getDefaultConfiguration(IField $field): array
    {
        $label = $field->label ?? (string)string($field->get('property'))->titleize();
        return [
            'type' => $field->get('type'),
            'label' => $label,
            'id' => $field->get('property'),
            'name' => $field->get('property'),
            'width' => $field->get('width'),
            'required' => $field->get('required'),
            'classes' => $field->get('classes'),
            'value' => $field->get('default_value'),
            'helpText' => $field->get('helpText'),
            'placeholder' => $field->get('placeholder'),
        ];
    }

    public function getConfiguration(): array
    {
        return static::getDefaultConfiguration($this);
    }
}