<?php
namespace KjmTrue\Module\Facades;

use Illuminate\Support\Facades\Facade;

class MenuAdmin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'menu-admin';
    }
}
