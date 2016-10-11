<?php
namespace KjmTrue\Module;

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
        $modules = config('module.modules', []);

        foreach ($modules as $module) {
            $module_dir = base_path("modules/{$module}");

            // Load routes.php module
            if(file_exists($module_dir . '/routes.php') & !$this->app->routesAreCached()){
                require $module_dir . '/routes.php';
            }

            // Load view modules
            if(is_dir($module_dir . '/resources/views')){
                $this->loadViewsFrom($module_dir . '/resources/views', $module);
            }

            // Load lang modules
            if(is_dir($module_dir . '/resources/lang')){
                $this->loadTranslationsFrom($module_dir . '/resources/lang', $module);
            }
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
