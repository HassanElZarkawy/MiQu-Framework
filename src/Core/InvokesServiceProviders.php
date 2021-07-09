<?php

namespace Miqu\Core;

use Illuminate\Support\Collection;

trait InvokesServiceProviders
{
    /**
     * @var Collection
     */
    private $providers;

    public function registerProviders()
    {
        if ( ! \Miqu\Helpers\env('providers') )
            return;

        $this->providers = \Miqu\Helpers\collect(\Miqu\Helpers\env('providers'))->map(function($abstract) {
            return $this->container->Resolve($abstract);
        })->filter(function($provider) {
            return $provider instanceof ServiceProvider;
        })->each(function($provider) {
            /** @var $provider ServiceProvider */
            $provider->container = $this->container;
            $provider->register();
        });
    }

    public function bootServiceProviders()
    {
        $this->providers->each(function($provider) {
            /** @var $provider ServiceProvider */
            $provider->boot();
        });
    }
}