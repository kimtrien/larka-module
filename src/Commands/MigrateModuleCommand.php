<?php
namespace KjmTrue\Module\Commands;

use Artisan;
use Illuminate\Console\Command as ConsoleCommand;

class MigrateModuleCommand extends ConsoleCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:module {module?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate Module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $module = $this->argument('module');
        if ($module) {
            Artisan::call("vendor:publish", [
                "--tag"   => "migrations-module-" . $module,
                "--force" => true
            ]);
        } else {
            // Load list modules
            if ($env_modules = env('MODULES')) {
                $modules = explode(',', $env_modules);
            } else {
                $modules = config('module.modules', []);
            }

            foreach ($modules as $module_name) {
                Artisan::call("vendor:publish", [
                    "--tag"   => "migrations-module-" . $module_name,
                    "--force" => true
                ]);
            }
        }

        Artisan::call("migrate", [
            "--path" => "/database/migrations/modules"
        ]);

        echo Artisan::output();
    }
}