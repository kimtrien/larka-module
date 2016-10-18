<?php

namespace KjmTrue\Module;


use Illuminate\Support\Collection;

class MenuAdmin
{
    /**
     * @var Collection
     */
    protected static $menu_admin;

    public function __construct()
    {
        self::$menu_admin = collect(config('menu.admin'));
    }

    /**
     * @param $key
     * @param $data
     *
     * @return $this
     */
    public function addMenu($key, $data)
    {
        if (empty($data['menu_order'])) {
            $data['menu_order'] = 50;
        }

        self::$menu_admin->put($key, $data);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMenu()
    {
        return self::$menu_admin->sortBy('menu_order');
    }
}