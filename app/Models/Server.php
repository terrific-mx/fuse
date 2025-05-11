<?php

namespace App\Models;

use App\Callbacks\CheckProvisioning;
use App\FakeServerProvider;
use App\Jobs\ProvisionServer;
use App\Scripts\GetCurrentDirectory;
use App\Scripts\ProvisionServer as ProvisionServerScript;
use App\Scripts\Script;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    /** @use HasFactory<\Database\Factories\ServerFactory> */
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serverProvider()
    {
        return $this->belongsTo(ServerProvider::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function isProvisioned()
    {
        return $this->status === 'provisioned';
    }

    public function markAsProvisioned()
    {
        return tap($this)->update(['status' => 'provisioned']);
    }

    public function isProvisioning()
    {
        return $this->status === 'provisioning';
    }

    public function markAsProvisioning()
    {
        return tap($this)->update(['status' => 'provisioning']);
    }

    public function provision()
    {
        ProvisionServer::dispatch($this);

        $this->update(['provisioning_job_dispatched_at' => Carbon::now()]);
    }

    public function isReadyForProvisioning()
    {
        if (! $this->public_address) {
            $this->retrievePublicAddress();
        }

        $canAccess = $this->public_address && trim($this->run(
            new GetCurrentDirectory
        )->output) === '/root';

        return $canAccess;
    }

    public function runProvisioningScript()
    {
        if ($this->isProvisioning()) {
            return;
        }

        $this->markAsProvisioning();

        return $this->runInBackground(new ProvisionServerScript($this), [
            'then' => [CheckProvisioning::class],
        ]);
    }

    public function withProvider(): FakeServerProvider
    {
        return $this->serverProvider->client();
    }

    public function olderThan(int $minutes, $attribute = 'created_at')
    {
        return $this->{$attribute}->lte(Carbon::now()->subMinutes($minutes));
    }

    public function ownerKeyPath()
    {
        return $this->user->keyPath();
    }

    public function run(Script $script, $options = [])
    {
        return $this->createTaskFromScript($script, $options)->run();
    }

    public function runInBackground(Script $script, $options = [])
    {
        return $this->createTaskFromScript($script, $options)->runInBackground();
    }

    protected function retrievePublicAddress()
    {
        $publicIp = $this->withProvider()->getPublicIpAddress($this);

        if (! $publicIp) {
            return;
        }

        $this->update(['public_address' => $publicIp]);
    }

    protected function createTaskFromScript(Script $script, $options = [])
    {
        if (! array_key_exists('timeout', $options)) {
            $options['timeout'] = $script->timeout();
        }

        return $this->tasks()->create([
            'name' => $script->name(),
            'user' => $script->sshAs,
            'options' => $options,
            'script' => (string) $script,
            'output' => '',
        ]);
    }
}
