<?php

namespace KjmTrue\Module;

use Exception;
use Illuminate\Support\Collection;

class MenuFrontend
{
    /**
     * @var Collection
     */
    protected static $menus;

    public function __construct()
    {
        self::$menus = collect(config('menu.frontend', []));
    }

    /**
     * @param string $key
     * @param string $title
     * @param int    $priority
     *
     * @return $this
     */
    public function addGroup($key, $title, $priority = 50)
    {
        $data = [
            'title'    => $title,
            'priority' => $priority,
            'items'    => [],
        ];

        self::$menus->put($key, $data);

        return $this;
    }

    public function addItem($group_key, $item_key, $title, $route_name, $parameters = [])
    {
        if (self::$menus->has($group_key)) {
            $group = self::$menus->get($group_key);
            $group['items'][$item_key] = [
                'title'      => $title,
                'route_name' => $route_name,
                'parameters' => $parameters,
            ];

            self::$menus->put($group_key, $group);
        } else {
            throw new Exception('Menu not has group ' . $group_key);
        }

        return $this;
    }

    /**
     * @param $key
     * @param $data
     *
     * @return $this
     */
    public function addMenu($key, $data)
    {
        if (empty($data['priority'])) {
            $data['priority'] = 50;
        }

        self::$menus->put($key, $data);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMenu()
    {
        return self::$menus->sortBy('priority');
    }
}