<?php

namespace App\Livewire\Forms;

use App\Models\Site;
use Livewire\Attributes\Validate;
use Livewire\Form;

class SiteForm extends Form
{
    public string $hostname = '';

    public string $php_version = '';

    public string $type = '';

    public string $web_folder = '/public';

    public string $repository_url = '';

    public string $repository_branch = '';

    public bool $use_deploy_key = false;

    public function store($server): void
    {
        $server->sites()->create([
            'hostname' => $this->hostname,
            'php_version' => $this->php_version,
            'repository_url' => $this->repository_url,
            'repository_branch' => $this->repository_branch,

            // Laravel defaults for new attributes
            'shared_directories' => ['storage'],
            'shared_files' => ['.env'],
            'writeable_directories' => [
                'bootstrap/cache',
                'storage',
                'storage/app',
                'storage/app/public',
                'storage/framework',
                'storage/framework/cache',
                'storage/framework/sessions',
                'storage/framework/views',
                'storage/logs',
            ],
            'script_before_deploy' => '',
            'script_after_deploy' => <<<'EOT'
                composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
                npm install --prefer-offline --no-audit
                npm run build
                $PHP_BINARY artisan storage:link
                $PHP_BINARY artisan config:cache
                $PHP_BINARY artisan route:cache
                $PHP_BINARY artisan view:cache
                $PHP_BINARY artisan event:cache
                # $PHP_BINARY artisan migrate --force
                EOT,
            'script_before_activate' => '',
            'script_after_activate' => '',
        ]);

        $this->reset();
    }
}
