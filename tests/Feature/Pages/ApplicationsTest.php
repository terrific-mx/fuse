<?php

use App\Jobs\DeleteApplication;
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

describe('delete', function () {
    it('can delete an application', function () {
        Process::fake();
        $application = Application::factory()->create();
        $user = $application->user();

        Volt::actingAs($user)->test('pages.applications.delete', ['application' => $application])
            ->call('delete');

        expect(Application::count())->toBe(0);
    });

    it('runs script to upade all caddy imports', function () {
        Process::fake();
        $application = Application::factory()->create();
        $user = $application->user();

        Volt::actingAs($user)->test('pages.applications.delete', ['application' => $application])
            ->call('delete');

        expect($application->server->tasks->first())->name->toBe('Updating Caddy Imports');
    });

    it('run script to delete application folder', function () {
        Process::fake();
        $application = Application::factory()->create();
        $user = $application->user();

        Volt::actingAs($user)->test('pages.applications.delete', ['application' => $application])
            ->call('delete');

        expect($application->server->tasks)->toHaveCount(2);
        expect($application->server->tasks->last())->name->toBe('Deleting folder');
    });

    it('checks only authorized users can view the delete application page', function () {
        $application = Application::factory()->create();
        $user = $application->user();
        /** @var User */
        $anotherUser = User::factory()->create();

        actingAs($anotherUser)->get("applications/{$application->id}/delete")->assertForbidden();
    });

    it('checks unauthenticated users are redirected to login page', function () {
        $application = Application::factory()->create();

        get("applications/{$application->id}/delete")->assertRedirect('/login');
    });

    it('dispatches a job to delete the application from the server', function () {
        Queue::fake();
        $application = Application::factory()->create();
        $user = $application->user();

        Volt::actingAs($user)->test('pages.applications.delete', ['application' => $application])
            ->call('delete');

        Queue::assertPushed(DeleteApplication::class);
    });
});

describe('deployment settings', function () {
    it('can update the deployment settings', function () {
        $application = Application::factory()->create();
        $user = $application->user();

        Volt::actingAs($user)->test('pages.applications.deployment-settings', ['application' => $application])
            ->set('releases_to_retain', 10)
            ->set('shared_directories', '/path/to/shared/directory')
            ->set('writable_directories', '/path/to/writable/directory')
            ->set('shared_files', '/path/to/shared/file')
            ->set('before_update_hook', 'echo "before update hook"')
            ->set('after_update_hook', 'echo "after update hook"')
            ->set('before_activate_hook', 'echo "before activate hook"')
            ->set('after_activate_hook', 'echo "after activate hook"')
            ->call('save');

        expect($application->fresh())
            ->releases_to_retain->toBe(10)
            ->shared_directories->toBe(['/path/to/shared/directory'])
            ->writable_directories->toBe(['/path/to/writable/directory'])
            ->shared_files->toBe(['/path/to/shared/file'])
            ->before_update_hook->toBe('echo "before update hook"')
            ->after_update_hook->toBe('echo "after update hook"')
            ->before_activate_hook->toBe('echo "before activate hook"')
            ->after_activate_hook->toBe('echo "after activate hook"');
    });

    it('validates releases_to_retain is required', function () {
        $application = Application::factory()->create();
        $user = $application->user();

        Volt::actingAs($user)->test('pages.applications.deployment-settings', ['application' => $application])
            ->set('releases_to_retain', '')
            ->call('save')
            ->assertHasErrors(['releases_to_retain' => 'required']);
    });

    it('validates releases_to_retain is an integer', function () {
        $application = Application::factory()->create();
        $user = $application->user();

        Volt::actingAs($user)->test('pages.applications.deployment-settings', ['application' => $application])
            ->set('releases_to_retain', 'not an integer')
            ->call('save')
            ->assertHasErrors(['releases_to_retain' => 'integer']);
    });

    it('validates releases_to_retain is at least 1', function () {
        $application = Application::factory()->create();
        $user = $application->user();

        Volt::actingAs($user)->test('pages.applications.deployment-settings', ['application' => $application])
            ->set('releases_to_retain', 0)
            ->call('save')
            ->assertHasErrors(['releases_to_retain' => 'min']);
    });

    it('validates releases_to_retain is at most 50', function () {
        $application = Application::factory()->create();
        $user = $application->user();

        Volt::actingAs($user)->test('pages.applications.deployment-settings', ['application' => $application])
            ->set('releases_to_retain', 51)
            ->call('save')
            ->assertHasErrors(['releases_to_retain' => 'max']);
    });

    it('checks only authorized users can view the deployment settings page', function () {
        $application = Application::factory()->create();
        $user = $application->user();
        /** @var User */
        $anotherUser = User::factory()->create();

        actingAs($anotherUser)->get("applications/{$application->id}/deployment-settings")->assertForbidden();
    });

    it('checks unauthenticated users are redirected to login page', function () {
        $application = Application::factory()->create();

        get("applications/{$application->id}/deployment-settings")->assertRedirect('/login');
    });
});
