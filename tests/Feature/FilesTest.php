<?php

use App\Models\Server;
use App\Models\Site;
use Illuminate\Support\Facades\Process;
use Livewire\Volt\Volt;

it('creates and runs a get_env_file task for the site and sets the env content on the component', function () {
    // Fake the process for the remote command
    Process::fake([
        '*' => Process::sequence()
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload script
            ->push(Process::result(output: "APP_KEY=base64:TESTKEY\nDB_HOST=localhost")), // Execute script
    ]);

    $server = Server::factory()->create();
    $site = Site::factory()->for($server)->create();

    // Simulate calling the action on the Volt component
    Volt::test('servers.sites.files', [
        'server' => $server,
        'site' => $site,
    ])
        ->call('getEnvFile')
        ->assertSet('envContent', "APP_KEY=base64:TESTKEY\nDB_HOST=localhost");

    // Assert the task was created with the correct script
    $envTask = $server->tasks()
        ->where('name', 'get_env_file')
        ->where('user', 'fuse')
        ->first();

    expect($envTask)->not()->toBeNull();
    expect($envTask->script)->toContain('cat');

    // Assert the process was run
    Process::assertRan(function ($process, $result) {
        return true;
    });
});
