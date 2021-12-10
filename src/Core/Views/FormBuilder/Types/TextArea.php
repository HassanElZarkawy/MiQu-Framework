<?php

namespace Miqu\Core\Views\FormBuilder\Types;

use eftec\bladeone\BladeOne;
use Exception;
use Miqu\Core\Views\FormBuilder\Field;

class TextArea extends Field
{
    /**
     * @var int
     */
    private $columns = 10;

    /**
     * @var int
     */
    private $rows = 5;

    /**
     * @param int $cols
     * @return TextArea
     */
    public function columns(int $cols): TextArea
    {
        $this->columns = $cols;
        return $this;
    }

    /**
     * @param int $rows
     * @return TextArea
     */
    public function rows(int $rows): TextArea
    {
        $this->rows = $rows;
        return $this;
    }

    public function applyConfiguration(array $configuration): Field
    {
        parent::applyConfiguration($configuration);
        if (isset($configuration['rows']))
            $this->rows($configuration['rows']);
        if (isset($configuration['columns']))
            $this->columns($configuration['columns']);
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
        $config['rows'] = $this->rows;
        $config['columns'] = $this->columns;
        return $blade->run('textArea', $config);
    }
}