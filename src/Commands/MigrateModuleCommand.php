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
    protected $signature = 'migrate:module';

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
        Artisan::call("vendor:publish", [
            "--tag"   => "migrations-modules",
            "--force" => true
        ]);

        Artisan::call("migrate", [
            "--path" => "/database/migrations/modules"
        ]);

        echo "Todo print message\n";
    }
}