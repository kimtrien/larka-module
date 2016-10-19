<?php
namespace KjmTrue\Module;

use Event;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/module.php' => config_path('module.php'),
        ]);

        // Load list modules
        if ($env_modules = env('MODULES')) {
            $modules = explode(',', $env_modules);
        } else {
            $modules = config('module.modules', []);
        }

        foreach ($modules as $module) {
            $module_dir = base_path("modules/{$module}");

            // Load routes.php module
            if(file_exists($module_dir . '/routes.php') & !$this->app->routesAreCached()){
                require $module_dir . '/routes.php';
            }

            // Load menus.php module
            if(file_exists($module_dir . '/menus.php')){
                require $module_dir . '/menus.php';
            }

            // Load view modules
            if(is_dir($module_dir . '/resources/views')){
                $this->loadViewsFrom($module_dir . '/resources/views', $module);
            }

            // Load lang modules
            if(is_dir($module_dir . '/resources/lang')){
                $this->loadTranslationsFrom($module_dir . '/resources/lang', $module);
            }

            // publishes migrations
            if(is_dir($module_dir . '/database/migrations')){
                $this->publishes([
                    $module_dir . '/database/migrations/' => database_path('migrations/modules')
                ], 'migrations-modules');
            }
        }

        Event::listen('Illuminate\Auth\Events\Login', function ($user) {
            (new CheckLogin())->check();
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('menu-admin', function () {
            return new MenuAdmin();
        });

        $this->app->singleton('menu-frontend', function () {
            return new MenuFrontend();
        });
    }

    public function provides()
    {
        return ['menu-admin', 'menu-frontend'];
    }
}
