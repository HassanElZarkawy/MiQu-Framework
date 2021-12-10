<?php

namespace Miqu\Core\Views\FormBuilder\Types;

use eftec\bladeone\BladeOne;
use Exception;
use Miqu\Core\Views\FormBuilder\Field;

class Currency extends Field
{
    /**
     * @var string|null
     */
    private $currency = null;

    public function setCurrency(string $currencyCode): Currency
    {
        $this->currency = $currencyCode;
        return $this;
    }

    public function applyConfiguration(array $configuration): Field
    {
        parent::applyConfiguration($configuration);
        if (isset($configuration['currency']))
            $this->currency = $configuration['currency'];
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
        $config['currency'] = $this->currency ?? 'USD';
        return $blade->run('currency', $config);
    }
}