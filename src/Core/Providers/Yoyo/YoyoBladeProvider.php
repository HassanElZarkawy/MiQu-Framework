<?php

namespace Miqu\Core\Providers\Yoyo;

use Clickfwd\Yoyo\Component;
use Clickfwd\Yoyo\Interfaces\ViewProviderInterface;
use Clickfwd\Yoyo\View;
use eftec\bladeone\BladeOne;
use Exception;
use Miqu\Core\Interfaces\IViewEngine;
use ReflectionException;

class YoyoBladeProvider implements ViewProviderInterface
{
    /**
     * @var View
     */
    private $view;

    /** @var string */
    private $path;

    /**
     * @var bool
     */
    public $isRendering;

    /**
     * @var BladeOne
     */
    private $blade;

    /**
     * @var string
     */
    private $template;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var Component
     */
    private $component;

    /**
     * @throws ReflectionException
     */
    public function __construct($view)
    {
        $this->view = $view;
        $this->blade = app()->make(IViewEngine::class);
        $this->registerBladeDirectives();
        $this->path = 'components' . DIRECTORY_SEPARATOR;
    }

    public function render($template, $vars = []): ViewProviderInterface
    {
        $this->template = $template;
        $this->parameters = $vars;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function makeFromString($content, $vars = []): string
    {
        return $this->blade->runString($content, $vars);
    }

    public function exists($template): bool
    {
        return true;
    }

    public function getProviderInstance(): View
    {
        return $this->view;
    }

    public function startYoyoRendering($component): void
    {
        $this->isRendering = true;
        $this->component = $component;
    }

    public function stopYoyoRendering(): void
    {
        $this->isRendering = false;
    }

    /**
     * @throws Exception
     */
    public function __toString()
    {
        if ( $this->parameters === null )
            $this->parameters = [];

        $this->parameters[ 'component' ] = $this->component;
        return $this->blade->run( $this->path . $this->template, $this->parameters);
    }

    private function registerBladeDirectives()
    {
        // $this->blade->directive('yoyo', [ $this, 'yoyo' ]);
        $this->blade->directive('yoyo_scripts', [ $this, 'yoyo_scripts' ]);
        $this->blade->directive('spinning', [ $this, 'spinning' ]);
        $this->blade->directive('endspinning', [ $this, 'endspinning' ]);
        $this->blade->directive('emit', [ $this, 'emit' ]);
        $this->blade->directive('emitTo', [ $this, 'emitTo' ]);
        $this->blade->directive('emitToWithSelector', [ $this, 'emitToWithSelector' ]);
        $this->blade->directive('emitSelf', [ $this, 'emitSelf' ]);
        $this->blade->directive('emitUp', [ $this, 'emitUp' ]);
    }

    public function yoyo($expression): string
    {
        return <<<yoyo
<?php
\$yoyo = new \Clickfwd\Yoyo\Yoyo();
if (Yoyo\is_spinning()) {
    echo \$yoyo->mount($expression)->refresh();
} else {
    echo \$yoyo->mount($expression)->render();
}
?>
yoyo;
    }

    public function yoyo_scripts(): string
    {
        return '<?php Yoyo\yoyo_scripts(); ?>';
    }

    public function spinning($expression): string
    {
        return $expression !== ''
            ? '<?php if($spinning && '. $expression . '): ?>'
            : '<?php if($spinning): ?>';
    }

    public function endspinning(): string
    {
        return '<?php endif; ?>';
    }

    public function emit($expression): string
    {
        return "<?php \$this->emit($expression); ?>";
    }

    public function emitTo($expression): string
    {
        return "<?php \$this->emitTo($expression); ?>";
    }

    public function emitToWithSelector($expression): string
    {
        return "<?php \$this->emitToWithSelector($expression); ?>";
    }

    public function emitSelf($expression): string
    {
        return "<?php \$this->emitSelf($expression); ?>";
    }

    public function emitUp($expression): string
    {
        return "<?php \$this->emitUp($expression); ?>";
    }
}