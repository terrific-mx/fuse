<?php

use App\Jobs\DeploySite;
use App\Models\Server;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Add Site')] class extends Component
{
    public Server $server;

    public string $hostname = '';

    public string $php_version = '';

    public string $repository_url = '';

    public string $repository_branch = '';

    public function mount()
    {
        $this->authorize('view', $this->server);
    }

    public function rules(): array
    {
        return [
            'hostname' => ['required', 'string', 'max:255'],
            'php_version' => ['required', 'string', 'in:8.4,8.3,8.1'],
            'repository_url' => ['required', 'string', 'max:255'],
            'repository_branch' => ['required', 'string', 'max:255'],
        ];
    }

    public function save()
    {
        $this->validate();

        $site = $this->server->sites()->create([
            'hostname' => $this->hostname,
            'php_version' => $this->php_version,
            'repository_url' => $this->repository_url,
            'repository_branch' => $this->repository_branch,
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

        return redirect()->route('servers.sites.show', [$this->server, $site]);
    }
}; ?>

<div class="max-w-[512px] mx-auto">
    <flux:link :href="route('servers.sites.index', $server)" class="inline-flex items-center gap-2 text-sm" variant="subtle" inline wire:navigate>
        <flux:icon.chevron-left variant="micro" />
        Sites
    </flux:link>

    <flux:spacer class="mt-4 lg:mt-8" />

    <form wire:submit="save">
        <flux:heading class="text-xl">Add a site</flux:heading>

        <flux:spacer class="mt-10" />

        <div class="space-y-6">
            <flux:field>
                <flux:input wire:model="hostname" label="Hostname" required />
                <flux:error name="hostname" />
            </flux:field>
            <flux:field>
                <flux:select wire:model="php_version" label="PHP version" required>
                    <flux:select.option></flux:select.option>
                    <flux:select.option value="8.4">8.4</flux:select.option>
                    <flux:select.option value="8.3">8.3</flux:select.option>
                    <flux:select.option value="8.2">8.2</flux:select.option>
                    <flux:select.option value="8.1">8.1</flux:select.option>
                </flux:select>
                <flux:error name="php_version" />
            </flux:field>

            <flux:callout variant="secondary">
                <flux:callout.heading icon="information-circle">Repository access required</flux:callout.heading>
                <flux:callout.text>
                    To deploy code from your repository, add this server’s public SSH key as an access key to your
                    repository provider (e.g., GitHub, GitLab). This grants the server read access to your
                    repository so it can fetch and deploy your code.
                </flux:callout.text>
                <x-slot name="actions">
                    <flux:input icon="key" value="{{ $server->public_ssh_key }}" readonly copyable />
                </x-slot>
            </flux:callout>

            <flux:field>
                <flux:input wire:model="repository_url" label="Repository URL" required />
                <flux:error name="repository_url" />
            </flux:field>
            <flux:field>
                <flux:input wire:model="repository_branch" label="Repository Branch" required />
                <flux:error name="repository_branch" />
            </flux:field>
        </div>

        <flux:spacer class="mt-8" />

        <div class="flex flex-col gap-4">
    <flux:button type="submit" variant="primary" color="zinc" class="w-full">Add site</flux:button>
</div>
    </form>
</div>

