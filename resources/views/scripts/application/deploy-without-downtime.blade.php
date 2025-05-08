@include('partials.scripts.shell-defaults')

@include('scripts.application._shell-variables')

# Create the necessary directories
mkdir -p {!! $repositoryDirectory !!}
mkdir -p {!! $sharedDirectory !!}
mkdir -p {!! $releaseDirectory !!}
mkdir -p {!! $logsDirectory !!}

# Cleanup old releases
@include('scripts.application._cleanup-old-releases')

@if($application->before_update_hook)
    echo "Running hook before updating repository"
    cd {!! $releaseDirectory !!}
    {!! $application->before_update_hook !!}
@endif

@unless($latestFinishedDeployment)
    rm -rf {{ $currentDirectory }}
@endunless

@if($application->repository)
    @include('scripts.application._update-repository')

    @if($application->after_update_hook)
        echo "Running hook after updating repository"
        cd {!! $releaseDirectory !!}
        {!! $application->after_update_hook !!}
    @endif
@endif

@unless($latestFinishedDeployment)
    @include('scripts.application._prepare-fresh-installation')
@endunless

@include('scripts.application._link-shared-directories')

@include('scripts.application._link-shared-files')

@include('scripts.application._make-directories-writable')

@if($application->before_activate_hook)
    echo "Running hook before putting the site live"
    cd {!! $releaseDirectory !!}
    {!! $application->before_activate_hook !!}
@endif

@include('scripts.application._make-deployment-current')

@if($application->after_activate_hook)
    echo "Running hook after putting the site live"
    cd {!! $releaseDirectory !!}
    {!! $application->after_activate_hook !!}
@endif

@if($application->repository)
    @include('scripts.application._send-repository-data')
@endif

echo "Done!"
