<?php

namespace Miqu\Core\Views\FormBuilder;

trait FieldBuilder
{
    /**
     * @param string $text
     * @return self
     */
    public function label(string $text): self
    {
        $this->label = $text;
        $this->includeLabel = $text !== null;
        return $this;
    }

    /**
     * @param int $width
     * @return self
     */
    public function width(int $width): self
    {
        $this->width = ($width > 0 && $width <= 12) ? $width : 12;
        return $this;
    }

    /**
     * @param array $classes
     * @return self
     */
    public function classes(array $classes): self
    {
        $this->classes = $classes;
        return $this;
    }

    /**
     * @param string $value
     * @return self
     */
    public function defaultValue(string $value): self
    {
        $this->default_value = $value;
        return $this;
    }

    /**
     * @return self
     */
    public function required(): self
    {
        $this->required = true;
        return $this;
    }

    /**
     * @param string|null $helpText
     * @return self
     */
    public function helpText(?string $helpText): self
    {
        $this->helpText = $helpText;
        return $this;
    }

    /**
     * @param string|null $placeholder
     * @return self
     */
    public function placeholder(?string $placeholder): self
    {
        $this->placeholder = $placeholder;
        return $this;
    }
}