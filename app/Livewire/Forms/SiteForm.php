<?php

namespace App\Livewire\Forms;

use App\Jobs\DeploySite;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;

class SiteForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $hostname = '';

    #[Validate('required|string|in:8.4,8.3,8.1')]
    public string $php_version = '';

    #[Validate('required|string|url|max:255')]
    public string $repository_url = '';

    #[Validate('required|string|max:255')]
    public string $repository_branch = '';

    public function store($server): void
    {
        $this->validate();

        $site = $server->sites()->create([
            'hostname' => $this->hostname,
            'php_version' => $this->php_version,
            'repository_url' => $this->repository_url,
            'repository_branch' => $this->repository_branch,

            // Laravel defaults for new attributes
            'shared_directories' => ['storage'],
            'shared_files' => ['.env'],
            'writable_directories' => [
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
            'script_after_deploy' => '',
            'script_before_activate' => <<<'EOT'
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
            'script_after_activate' => '',
        ]);

        $deployment = $site->deployments()->create([
            'status' => 'pending',
            'triggered_by' => Auth::id(),
        ]);

        DeploySite::dispatch($deployment);

        $this->reset();
    }
}
