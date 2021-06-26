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
        if ( ! env('providers') )
            return;

        $this->providers = collect(env('providers'))->map(function($abstract) {
            return $this->container->Resolve($abstract);
        })->filter(function($provider) {
            return $provider instanceof ServiceProvider;
        })->each(function($provider) {
            /** @var $provider ServiceProvider */
            $provider->container = $this->container;
            $provider->register();
        })->all();
    }
}