<?php


namespace Miqu\Core\Views;

use eftec\bladeone\BladeOne;
use Exception;
use Miqu\Core\Authentication;
use Miqu\Core\Interfaces\IViewEngine;

class Blade extends BladeOne implements IViewEngine
{
    public function __construct()
    {
        $views_base_path = BASE_DIRECTORY . \Miqu\Helpers\env('blade.views_path');
        $blade_compile_path = BASE_DIRECTORY . \Miqu\Helpers\env('blade.bin_path');
        $blade_mode = \Miqu\Helpers\env('blade.mode');
        parent::__construct( $views_base_path, $blade_compile_path, $blade_mode );

        $this->setErrorCallback();
        $this->setBaseUrl( getBaseUrl() );
        try {
            $this->setAuthenticationPolicies();
        } catch (Exception $exception) {
            // fail silently.
        }
    }

    private function setErrorCallback()
    {
        $this->setErrorFunction(function($key) {
            if ( count( $this->variables ) === 0 )
                return false;

            if ( ! isset( $this->variables[ 'errors' ] ) )
                return false;

            if ( ! isset( $this->variables[ 'errors' ][ $key ] ) )
                return false;

            return true;
        });
    }

    /**
     * @throws Exception
     */
    private function setAuthenticationPolicies()
    {
        $authentication = new Authentication;
        if ( ! $authentication->check() )
            return;

        $role = collect($authentication->user()->roles())->first();
        $permissions = collect($authentication->user()->permissions())->map(function($item) {
            return $item->name;
        })->all();

        $this->setAuth( $authentication->user()->name, $role ? $role->name : '', $permissions );

        $this->setCanFunction(function($permission) use ( $permissions ) {
            if ( in_array( $permission, $permissions ) )
                return true;
            return false;
        });
    }
}