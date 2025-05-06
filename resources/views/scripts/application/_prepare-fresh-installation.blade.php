cd {!! $application->path() !!}

{{-- For example: tasks/deployment/prepare-fresh-installation/laravel.blade.php --}}
@includeIf('scripts.application._prepare-fresh-installation-'.$applicationType, ['status' => 'complete'])

cd {!! $application->path() !!}
