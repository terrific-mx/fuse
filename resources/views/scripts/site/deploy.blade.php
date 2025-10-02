@include('scripts.partials.shell-defaults')

export PHP_BINARY={!! $site->php_version->getBinary() !!}

# Create the necessary directories
mkdir -p {!! $repositoryDirectory !!}
mkdir -p {!! $sharedDirectory !!}
mkdir -p {!! $releaseDirectory !!}
mkdir -p {!! $logsDirectory !!}

# Cleanup old releases
DEPLOYMENT_KEEP="{{ $site->latestFinishedDeployment ? $site->latestFinishedDeployment->created_at->timestamp : 'none' }}"

# Get a list of all deployments, sorted by timestamp in ascending order
DEPLOYMENT_LIST=($(ls -1 {!! $releasesDirectory !!} | sort -n))

# Determine how many deployments to delete
NUM_TO_DELETE=$((${#DEPLOYMENT_LIST[@]} - {{ $site->deployment_releases_retention }}))

# Loop through the deployments to delete
for ((i=0; i<$NUM_TO_DELETE; i++)); do
    DEPLOY=${DEPLOYMENT_LIST[$i]}
    # Skip the deployment to keep
    if [[ $DEPLOY == $DEPLOYMENT_KEEP ]]; then
        continue
    fi

    # Delete the deployment
    rm -rf {!! $releasesDirectory !!}/$DEPLOY
done

@if($site->hook_before_updating_repository)
    echo "Running hook before updating repository"
    cd {!! $releaseDirectory !!}
    {!! $site->hook_before_updating_repository !!}
@endif

@if($site->repository_url)
    # Check if the repository exists and if the remote URL is correct, if not, delete it
    if [ -f "{!! $repositoryDirectory !!}/HEAD" ]; then
        cd {!! $repositoryDirectory !!}
        CURRENT_REMOTE_URL=$(git config --get remote.origin.url || echo '');

        if [ "$CURRENT_REMOTE_URL" != '{!! $site->repository_url !!}' ]; then
            @if($site->zero_downtime_deployment)
                rm -rf {!! $repositoryDirectory !!}
                cd {!! $site->path !!}
                mkdir -p {!! $repositoryDirectory !!}
            @else
                git remote set-url origin {!! $site->repository_url !!}
            @endif

        fi
    fi

    @if($site->deploy_key_private)
        # Store the deploy key and set the GIT SSH command
    cat <<EOF >> {!! $site->path !!}/deploy_key
    {{ $site->deploy_key_private }}
    EOF

        chmod 600 {!! $site->path !!}/deploy_key
        export GIT_SSH_COMMAND="ssh -i {!! $site->path !!}/deploy_key"
    @endif

    cd {!! $site->path !!}

    # Clone the repository if it doesn't exist
    @if($site->zero_downtime_deployment)
        if [ ! -f "{!! $repositoryDirectory !!}/HEAD" ]; then
            git clone --mirror {!! $site->repository_url !!} {!! $repositoryDirectory !!}
        fi
    @else
        if [ ! -f "{!! $repositoryDirectory !!}/.git/HEAD" ]; then
            git clone {!! $site->repository_url !!} {!! $repositoryDirectory !!}
        fi
    @endif

    # Fetch the latest changes from the repository
    cd {!! $repositoryDirectory !!}

    @if($site->zero_downtime_deployment)
        git remote update
    @else
        git pull origin {!! $site->repository_branch !!}
    @endif

    @if($site->zero_downtime_deployment)
        # Clone the repository into the release directory
        cd {!! $releaseDirectory !!}
        git clone -l {!! $repositoryDirectory !!} .
        git checkout --force {!! $site->repository_branch !!}
    @endif

    cd {!! $site->path !!}

    @if($site->hook_after_updating_repository)
        echo "Running hook after updating repository"
        cd {!! $releaseDirectory !!}
        {!! $site->hook_after_updating_repository !!}
    @endif

@endif

@unless($site->installed_at)
    cd {!! $site->path !!}

    cd {!! $site->path !!}

    ENV_PATH="{!! $site->zero_downtime_deployment ? $sharedDirectory : $repositoryDirectory !!}/.env"
    EXAMPLE_ENV_PATH="{!! $site->zero_downtime_deployment ? $releaseDirectory : $repositoryDirectory !!}/.env.example"

    if [ ! -f $ENV_PATH ] && [ -f $EXAMPLE_ENV_PATH ]; then
        cp $EXAMPLE_ENV_PATH $ENV_PATH
        cd {!! $site->zero_downtime_deployment ? $sharedDirectory : $repositoryDirectory !!}

        @foreach($env as $search => $replace)
            sed -i --follow-symlinks "s|^{{ $search }}=.*|{{ $search }}={{ $replace }}|g" .env
        @endforeach

    fi

    cd {!! $site->path !!}
@endunless

@foreach($sharedDirectories() as $directory)
    if [ ! -d "{!! $sharedDirectory !!}/{!! $directory !!}" ]; then
        # Create shared directory if it does not exist.
        mkdir -p {!! $sharedDirectory !!}/{!! $directory !!}

        if [ -d "{!! $releaseDirectory !!}/{!! $directory !!}" ]; then
            # Copy contents of release directory to shared directory if it exists.
            cp -r {!! $releaseDirectory !!}/{!! $directory !!} {!! $sharedDirectory !!}/{!! dirname($directory) !!}
        fi
    fi

    #  Remove shared directory from release directory if it exists.
    rm -rf {!! $releaseDirectory !!}/{!! $directory !!}

    # Create parent directory of shared directory in release directory if it does not exist,
    # otherwise symlink will fail.
    mkdir -p `dirname {!! $releaseDirectory !!}/{!! $directory !!}`

    # Symlink shared directory to release directory.
    ln -nfs --relative {!! $sharedDirectory !!}/{!! $directory !!} {!! $releaseDirectory !!}/{!! $directory !!}

@endforeach

@foreach($sharedFiles() as $file)
    # Create directories in shared and release directories if they don't exist
    mkdir -p {!! $releaseDirectory !!}/{!! dirname($file) !!}
    mkdir -p {!! $sharedDirectory !!}/{!! dirname($file) !!}

    # If the shared file does not exist, but the release file does, copy the release file to shared
    if [ ! -f "{!! $sharedDirectory !!}/{!! $file !!}" ] && [ -f "{!! $releaseDirectory !!}/{!! $file !!}" ]; then
        cp {!! $releaseDirectory !!}/{!! $file !!} {!! $sharedDirectory !!}/{!! $file !!}
    fi

    # If the shared file still does not exist, create it
    if [ ! -f "{!! $sharedDirectory !!}/{!! $file !!}" ]; then
        touch {!! $sharedDirectory !!}/{!! $file !!}
    fi

    # If the release file exists, remove it
    if [ -f "{!! $releaseDirectory !!}/{!! $file !!}" ]; then
        rm -rf {!! $releaseDirectory !!}/{!! $file !!}
    fi

    # Create symlink
    ln -nfs --relative {!! $sharedDirectory !!}/{!! $file !!} {!! $releaseDirectory !!}/{!! $file !!}

@endforeach

@foreach($writeableDirectories() as $directory)
    DIRECTORY_IS_WRITEABLE=$(getfacl -p {!! $releaseDirectory !!}/{!! $directory !!} | grep "^user:{!! $site->user !!}:.*w" | wc -l)

    if [ $DIRECTORY_IS_WRITEABLE -eq 0 ]; then
        # Make the directory writable (without sudo)
        setfacl -L -m u:{!! $site->user !!}:rwX {!! $releaseDirectory !!}/{!! $directory !!}
        setfacl -dL -m u:{!! $site->user !!}:rwX {!! $releaseDirectory !!}/{!! $directory !!}
    fi

@endforeach

@if($site->hook_before_making_current)
    echo "Running hook before putting the site live"
    cd {!! $releaseDirectory !!}
    {!! $site->hook_before_making_current !!}
@endif

cd {!! $site->path !!}
ln -nfs --relative {!! $releaseDirectory !!} {!! $currentDirectory !!}

@if($site->hook_after_making_current)
    echo "Running hook after putting the site live"
    cd {!! $releaseDirectory !!}
    {!! $site->hook_after_making_current !!}
@endif

echo "Done!"
