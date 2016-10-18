<?php
namespace KjmTrue\Module\Facades;

use Illuminate\Support\Facades\Facade;

class MenuFrontend extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'menu-frontend';
    }
}
