<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeService extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a service class';

    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Services/{$name}.php");

        if (!is_dir(app_path('Services'))) {
            mkdir(app_path('Services'), 0755, true);
        }

        $stub = <<<PHP
<?php

namespace App\Services;

class {$name}
{
    //
}
PHP;

        file_put_contents($path, $stub);

        $this->info("Service created at: {$path}");
    }
}
    