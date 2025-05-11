<?php

use App\Jobs\DeployApplication;
use App\Jobs\UpdateApplicationCaddyFile;
use App\Models\Application;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

describe('application settings', function () {
    it('can update the application settings', function () {
        Process::fake();
        $application = Application::factory()->create([
            'repository' => 'example/another-valid-repository',
            'branch' => 'another-valid-branch',
            'web_directory' => 'public_html',
            'php_version' => 'PHP 8.2',
        ]);
        $user = $application->user();

        Volt::actingAs($user)->test('pages.applications.settings', ['application' => $application])
            ->set('repository', 'example/valid-repository')
            ->set('branch', 'valid-branch')
            ->set('web_directory', 'public')
            ->set('php_version', 'PHP 8.3')
            ->call('save');

        expect($application->fresh())
            ->repository->toBe('example/valid-repository')
            ->branch->toBe('valid-branch')
            ->web_directory->toBe('public')
            ->php_version->toBe('PHP 8.3');
    });

    it('runs the script to update caddyfile with new settings', function () {
        Process::fake();
        $application = Application::factory()->create();
        $user = $application->user();

        Volt::actingAs($user)->test('pages.applications.settings', ['application' => $application])
            ->call('save');

        expect($application->server->tasks->last())->name->toBe('Updating Caddyfile');
    });

    it('dispatches a job to update the application caddyfile when the application settings are saved', function () {
        Queue::fake();

        $application = Application::factory()->create();
        $user = $application->user();

        Volt::actingAs($user)->test('pages.applications.settings', ['application' => $application])
            ->call('save');

        Queue::assertPushed(UpdateApplicationCaddyFile::class);
    });

    it('creates a new application deployument', function () {})->todo();

    it('runs the script to deploy the application', function () {})->todo();
});

describe('deployments', function () {
    it('can create a new deployment', function () {
        Process::fake();
        $application = Application::factory()->create();

        Volt::test('pages.applications.deployments', ['application' => $application])
            ->call('deploy');

        expect($application->deployments)->toHaveCount(1);
    });

    it('runs the deployment script', function () {
        Process::fake();
        $application = Application::factory()->create();

        Volt::test('pages.applications.deployments', ['application' => $application])
            ->call('deploy');

        expect($application->server->tasks()->first())
            ->name->toBe('Deploying application')
            ->user->toBe('fuse');
    });

    it('marks the deployment as finished on succesful task deployment', function () {
        Process::fake();
        $application = Application::factory()->create();

        Volt::test('pages.applications.deployments', ['application' => $application])
            ->call('deploy');

        tap($application->server->tasks->last(), function (Task $task) {
            expect($task)->name->toBe('Deploying application');

            $task->finish(exitCode: 0);
        });

        expect($application->deployments()->first())
            ->status->toBe('finished');
    });

    it('marks the deployment as failed on failed task deployment', function () {
        Process::fake();
        $application = Application::factory()->create();

        Volt::test('pages.applications.deployments', ['application' => $application])
            ->call('deploy');

        tap($application->server->tasks->last(), function (Task $task) {
            expect($task)->name->toBe('Deploying application');

            $task->finish(exitCode: 999);
        });

        expect($application->deployments()->first())
            ->status->toBe('failed');
    });

    it('cannot create a new deployment if the application has a pending deployment', function () {
        $application = Application::factory()->create();
        $application->deployments()->create(['status' => 'pending']);

        Volt::test('pages.applications.deployments', ['application' => $application])
            ->call('deploy');

        expect($application->deployments()->pending()->count())->toBe(1);
    });

    it('dispatches a deployment event', function () {
        Queue::fake();
        $application = Application::factory()->create();

        Volt::test('pages.applications.deployments', ['application' => $application])
            ->call('deploy');

        Queue::assertPushed(DeployApplication::class);
    });

    it('checks that only authorized users can view the application deployments page', function () {
        $application = Application::factory()->create();
        $user = $application->user();
        /** @var User */
        $anotherUser = User::factory()->create();

        actingAs($anotherUser)->get("applications/{$application->id}/deployments")->assertForbidden();
    });

    it('redirects unauthenticated users to the login page', function () {
        $application = Application::factory()->create();

        get("applications/{$application->id}/deployments")->assertRedirect('/login');
    });
});
