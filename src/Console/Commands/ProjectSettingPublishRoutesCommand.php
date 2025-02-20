<?php

namespace Mabrouk\ProjectSetting\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ProjectSettingPublishRoutesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setting:publish-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the routes for the Project Setting package';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $routesPublishSubDirectory = config('project_settings.routes_publish_subdirectory');

        $routes = [
            'project_settings_admin_routes.php',
            'project_settings_client_routes.php',
            'project_settings_backend_routes.php',
        ];

        $alreadyPublished = true;

        foreach ($routes as $route) {
            if (!File::exists(base_path("routes/{$routesPublishSubDirectory}{$route}"))) {
                $alreadyPublished = false;
                break;
            }
        }

        if ($alreadyPublished) {
            $this->warn("Routes have already been published in routes/{$routesPublishSubDirectory} directory.");
            return Command::SUCCESS;
        }

        if (! $this->shouldPublishAlreadyLoadedRoutes()) {
            $this->warn('Routes publishing is aborted.');
            return Command::SUCCESS;
        }

        foreach ($routes as $route) {
            $sourcePath = __DIR__ . "/../../routes/{$route}";
            $destinationPath = base_path("routes/{$routesPublishSubDirectory}{$route}");
            File::copy($sourcePath, $destinationPath);
        }

        $this->callSilent('vendor:publish', [
            '--provider' => 'Mabrouk\ProjectSetting\ProjectSettingServiceProvider',
        ]);

        exec('composer dump-autoload');

        $this->info('Routes have been published successfully.');

        return Command::SUCCESS;
    }

    private function shouldPublishAlreadyLoadedRoutes()
    {
        if (config('project_settings.load_routes')) {
            return $this->confirm(
                'Loading routes is enabled in the configuration file. Do you want to publish them anyway?',
                false
            );
        }
        return true;
    }
}