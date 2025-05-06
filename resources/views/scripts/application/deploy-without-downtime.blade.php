@include('partials.scripts.shell-defaults')

@include('scripts.application._shell-variables')

# Create the necessary directories
mkdir -p {!! $repositoryDirectory !!}
mkdir -p {!! $sharedDirectory !!}
mkdir -p {!! $releaseDirectory !!}
mkdir -p {!! $logsDirectory !!}

# Cleanup old releases
@include('scripts.application._cleanup-old-releases')

@if($hookBeforeUpdatingRepository)
    echo "Running hook before updating repository"
    cd {!! $releaseDirectory !!}
    {!! $hookBeforeUpdatingRepository !!}
@endif

@if($application->repository)
    @include('scripts.application._update-repository')

    @if($hookAfterUpdatingRepository)
        echo "Running hook after updating repository"
        cd {!! $releaseDirectory !!}
        {!! $hookAfterUpdatingRepository !!}
    @endif
@endif

@unless($latestFinishedDeployment)
    @include('scripts.application._prepare-fresh-installation')
@endunless

@include('scripts.application._link-shared-directories')

@include('scripts.application._link-shared-files')

@include('scripts.application._make-directories-writable')

@if($hookBeforeMakingCurrent)
    echo "Running hook before putting the site live"
    cd {!! $releaseDirectory !!}
    {!! $hookBeforeMakingCurrent !!}
@endif

@include('scripts.application._make-deployment-current')

@if($hookAfterMakingCurrent)
    echo "Running hook after putting the site live"
    cd {!! $releaseDirectory !!}
    {!! $hookAfterMakingCurrent !!}
@endif

@if($application->repository)
    @include('scripts.application._send-repository-data')
@endif

echo "Done!"
