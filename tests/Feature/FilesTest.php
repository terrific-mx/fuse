<?php

use App\Models\Server;
use App\Models\Site;
use Illuminate\Support\Facades\Process;
use Livewire\Volt\Volt;

it('creates and runs a get_env_file task for the site and sets the env content on the component', function () {
    Process::fake([
        '*' => Process::sequence()
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload script
            ->push(Process::result(output: "APP_KEY=base64:TESTKEY\nDB_HOST=localhost")), // Execute script
    ]);

    $server = Server::factory()->create();
    $site = Site::factory()->for($server)->create();

    Volt::test('servers.sites.files', ['server' => $server, 'site' => $site])
        ->call('getEnvFile')
        ->assertSet('envContent', "APP_KEY=base64:TESTKEY\nDB_HOST=localhost");

    $envTask = $server->tasks()
        ->where('name', 'get_env_file')
        ->where('user', 'fuse')
        ->first();

    expect($envTask)->not()->toBeNull();
    expect($envTask->script)->toContain('cat');

    Process::assertRan(function ($process, $result) {
        return true;
    });
});

it('creates and runs a set_env_file task for the site and saves the env content on the server', function () {
    $newEnv = "APP_KEY=base64:NEWKEY\nDB_HOST=127.0.0.1";

    Process::fake([
        '*' => Process::sequence()
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload script
            ->push(Process::result()), // Execute script (no output expected for write)
    ]);

    $server = Server::factory()->create();
    $site = Site::factory()->for($server)->create();

    Volt::test('servers.sites.files', ['server' => $server, 'site' => $site])
        ->set('envContent', $newEnv)
        ->call('saveEnvFile')
        ->assertSet('envContent', $newEnv);

    $envTask = $server->tasks()
        ->where('name', 'set_env_file')
        ->where('user', 'fuse')
        ->first();

    expect($envTask)->not()->toBeNull();
    expect($envTask->script)->toContain($site->path.'/shared/.env');
    expect($envTask->script)->toContain($newEnv);

    Process::assertRan(function ($process, $result) {
        return true;
    });
});
