<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeServiceCommand extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new Service class';

    public function handle()
    {
        $name = $this->argument('name');
        $serviceName = ucfirst($name);
        $path = app_path("Services/{$serviceName}.php");

        if (File::exists($path)) {
            $this->error("Service {$serviceName} already exists!");
            return;
        }

        if (!File::exists(app_path('Services'))) {
            File::makeDirectory(app_path('Services'), 0755, true);
        }

        $stub = <<<PHP
<?php

namespace App\Services;

class {$serviceName}
{
    // TODO: Implement service logic
}
PHP;

        File::put($path, $stub);

        $this->info("Service {$serviceName} created successfully!");
    }
}
